<?php

namespace App\Controllers;

use App\Models\LotesProductosModel;
use App\Models\ProductosModel;
use App\Models\DetalleAjusteModel;
use App\Models\AjustesInventarioModel;

use PhpOffice\PhpSpreadsheet\IOFactory;

class LotesProductos extends BaseController
{
    protected $lotesModel, $productos,$ajusteModel, $detalleModel;

    public function __construct()
    {
        $this->lotesModel = new LotesProductosModel();
        $this->productos = new ProductosModel();
        $this->detalleModel = new DetalleAjusteModel();
        $this->ajusteModel = new AjustesInventarioModel();
    }

    public function index()
    {
        $data['lotes'] = $this->lotesModel->findAll();
        return view('lotes/index', $data); // Crea una vista 'index' para mostrar los lotes
    }

    public function crear()
    {
        return view('lotes/crear'); // Crea una vista 'crear' con un formulario
    }

    public function guardar()
    {
        $data = [
            'id_producto' => $this->request->getPost('id_producto'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'cantidad' => $this->request->getPost('cantidad'),
            'activo' => 1,
            'fecha_registro' => date('Y-m-d H:i:s')
        ];

        $this->lotesModel->insertarLote($data);
        return redirect()->to('/lotesproductos');
    }

    public function editar($id)
    {
        $data['lote'] = $this->lotesModel->find($id);
        return view('lotes/editar', $data); // Crea una vista 'editar'
    }

    public function actualizar($id)
    {
        $data = [
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'cantidad' => $this->request->getPost('cantidad')
        ];

        $this->lotesModel->actualizarLote($id, $data);
        return redirect()->to('/lotesproductos');
    }

    public function eliminar($id)
    {
        $this->lotesModel->eliminarLote($id);
        return redirect()->to('/lotesproductos');
    }


    
    public function agregarLote()
{
    $id_producto = $this->request->getPost('id_producto');
    $fecha_vencimiento = $this->request->getPost('fecha_vencimiento');
    $cantidad = $this->request->getPost('cantidad');

    $loteModel = new \App\Models\LotesProductosModel();

    $loteData = [
        'id_producto' => $id_producto,
        'fecha_vencimiento' => $fecha_vencimiento,
        'cantidad' => $cantidad,
        'fecha_registro' => date('Y-m-d H:i:s')
    ];

    // Guardar el lote con sincronización del estado activo
    $loteModel->guardarLote($loteData);

    // Redireccionar o mostrar mensaje de éxito
}


    public function importarVista()
    {
        $data = ['titulo' => 'Importar Conteo Físico de Productos'];
        echo view('header');
        echo view('lotesProductos/importar', $data);
        echo view('footer');
    }
public function importarExcel()
{
    $archivo = $this->request->getFile('archivo_excel');

    // Validación del archivo
    if (!$archivo->isValid() || $archivo->getExtension() !== 'xlsx') {
        return redirect()->back()->with('mensaje', '❌ Archivo inválido. Solo se permite .xlsx');
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getTempName());
    $datos = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    unset($datos[1]); // Quitar encabezados

    // Modelos
    $productoModel = new \App\Models\ProductosModel();
    $loteModel     = new \App\Models\LotesProductosModel();
    $ajusteModel   = $this->ajusteModel;
    $detalleModel  = $this->detalleModel;

    $insertados = 0;
    $errores    = [];
    $linea      = 2;
    $ajustes    = [];

    foreach ($datos as $fila) {
        // Si la fila está completamente vacía
        if (empty(trim(implode('', $fila)))) {
            $linea++;
            continue;
        }

        $nombre_producto   = trim($fila['A'] ?? '');
        $cantidad_fisica   = (float) trim($fila['B'] ?? 0);
        $fecha_vencimiento = trim($fila['C'] ?? '');

        // Validación básica
        if (!$nombre_producto || $cantidad_fisica <= 0) {
            $errores[] = "Fila $linea: Campos incompletos o cantidad inválida.";
            $linea++;
            continue;
        }

        // Buscar producto
        $producto = $productoModel->where('nombre', $nombre_producto)->first();
        if (!$producto) {
            $errores[] = "Fila $linea: Producto '$nombre_producto' no encontrado.";
            $linea++;
            continue;
        }

        // 🔹 Procesar la fecha de vencimiento
        $fecha_final = null; // por defecto null = sin vencimiento

        if ($fecha_vencimiento !== '') {
            $texto = strtolower(trim($fecha_vencimiento));

            // Si el texto indica que no requiere fecha de vencimiento
            if (in_array($texto, ['no requiere', 'sin vencimiento', 'n/a', 'no aplica'])) {
                $fecha_final = null; // producto sin vencimiento
            } else {
                $timestamp = strtotime($fecha_vencimiento);
                if ($timestamp === false) {
                    $errores[] = "Fila $linea: Fecha de vencimiento inválida '$fecha_vencimiento'. Se guardará como sin vencimiento.";
                    $fecha_final = null;
                } else {
                    $fecha_final = date('Y-m-d', $timestamp);
                }
            }
        } else {
            // Si la celda está vacía → sin vencimiento
            $fecha_final = null;
        }

        // Obtener stock actual
        $stockAntes = $loteModel
            ->selectSum('cantidad', 'stock')
            ->where('id_producto', $producto['id'])
            ->where('activo', 1)
            ->get()
            ->getRowArray();

        $cantidadAntes = $stockAntes['stock'] ?? 0;

        // Desactivar lotes anteriores activos
        $loteModel->where('id_producto', $producto['id'])
            ->where('activo', 1)
            ->set(['activo' => 0])
            ->update();

        // Insertar nuevo lote
        $loteModel->insert([
            'id_producto'       => $producto['id'],
            'fecha_vencimiento' => $fecha_final, // puede ser NULL
            'cantidad'          => $cantidad_fisica,
            'activo'            => 1,
            'fecha_registro'    => date('Y-m-d H:i:s'),
            'movimiento'        => 'AJUSTE',
            'id_detalle_compra' => null
        ]);

        $idLoteNuevo = $loteModel->getInsertID();

        // Guardar detalle del ajuste
        $ajustes[] = [
            'id_producto'      => $producto['id'],
            'id_lote'          => $idLoteNuevo,
            'cantidad_antes'   => $cantidadAntes,
            'cantidad_despues' => $cantidad_fisica,
            'diferencia'       => $cantidad_fisica - $cantidadAntes,
            'observacion'      => 'Ajuste por importación desde Excel'
        ];

        $insertados++;
        $linea++;
    }

    // 🔹 Registrar ajuste general
    if (!empty($ajustes)) {
        $idAjuste = $ajusteModel->insert([
            'fecha'         => date('Y-m-d H:i:s'),
            'motivo'        => 'Importación desde Excel',
            'observaciones' => 'Ajuste físico importado desde Excel',
            'id_usuario'    => session()->id_usuario ?? null
        ]);

        foreach ($ajustes as $detalle) {
            $detalle['id_ajuste'] = $idAjuste;
            $detalleModel->insert($detalle);
        }
    }

    // 🔹 Mensaje final
    $mensaje = "✅ <strong>$insertados productos ajustados correctamente.</strong>";
    if (!empty($errores)) {
        $mensaje .= "<br><strong>⚠️ Errores o advertencias:</strong><ul>";
        foreach ($errores as $error) {
            $mensaje .= "<li>$error</li>";
        }
        $mensaje .= "</ul>";
    }

    return redirect()->to(base_url('lotesProductos/importarVista'))->with('mensaje', $mensaje);
}





public function reporte_vencimiento()
{
    $catModel = new \App\Models\CategoriasModel();
    $data['categorias'] = $catModel->where('activo', 1)->findAll();

    echo view('header');
    echo view('lotesProductos/reporte_vencimiento', $data);
    echo view('footer');
}



public function datosVencimiento()
{
    $db = \Config\Database::connect();

    $tipo = $this->request->getGet('tipo'); // vencidos | desechados | por_vencer
    $anio = $this->request->getGet('anio');
    $id_categoria = $this->request->getGet('categoria');
    $desde_param = $this->request->getGet('desde');
    $hasta_param = $this->request->getGet('hasta');

    if (!$tipo || !$anio) {
        return $this->response->setJSON(['error' => 'Faltan datos.']);
    }

    // Rango de fechas
    $desde = $desde_param ?: "$anio-01-01";
    $hasta = $hasta_param ?: "$anio-12-31";

    // Filtro base
    $condiciones = [];

    // Tipo de reporte
    if ($tipo === 'vencidos') {
          $condiciones[] = "fecha_vencimiento < CURDATE()";
        $condiciones[] = "fecha_vencimiento BETWEEN '$desde' AND '$hasta'";
        $condiciones[] = "activo = 1";
    } elseif ($tipo === 'desechados') {
        $condiciones[] = "fecha_vencimiento BETWEEN '$desde' AND '$hasta'";
        $condiciones[] = "activo = 0";
    } elseif ($tipo === 'por_vencer') {
        $condiciones[] = "fecha_vencimiento BETWEEN '$desde' AND '$hasta'";
        $condiciones[] = "activo = 1";
    } else {
        return $this->response->setJSON(['error' => 'Tipo inválido.']);
    }

    // Filtro por categoría
    if ($id_categoria && is_numeric($id_categoria) && (int)$id_categoria > 0) {
        $condiciones[] = "id_producto IN (SELECT id FROM productos WHERE id_categoria = $id_categoria)";
    }

    $where = implode(' AND ', $condiciones);

    // Consulta principal
    $query = $db->query("
        SELECT 
            DATE_FORMAT(fecha_vencimiento, '%Y-%m') AS mes,
            SUM(cantidad) AS total
        FROM lotes_productos
        WHERE $where
        GROUP BY mes
        ORDER BY mes
    ")->getResultArray();

    // Inicializar los meses del año con 0
    $meses_completos = [];
    for ($i = 1; $i <= 12; $i++) {
        $mes = "$anio-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        $meses_completos[$mes] = 0;
    }

    foreach ($query as $row) {
        if (isset($meses_completos[$row['mes']])) {
            $meses_completos[$row['mes']] = (int)$row['total'];
        }
    }

    return $this->response->setJSON([
        'meses' => array_keys($meses_completos),
        // Cambiar el nombre según el tipo seleccionado
        $tipo === 'vencidos' ? 'vencidos_activos' :
        ($tipo === 'desechados' ? 'vencidos_desechados' : 'cantidades') => array_values($meses_completos)
    ]);
}







public function reporteVencimientoPDF()
{
    helper('text');
    $db = \Config\Database::connect();

    $tipo = $this->request->getGet('tipo'); // vencidos | desechados | por_vencer
    $categoria = $this->request->getGet('categoria');
    $desde = $this->request->getGet('desde') ?: date('Y-01-01');
    $hasta = $this->request->getGet('hasta') ?: date('Y-12-31');

    $condiciones = [];

    // Filtro por tipo
    if ($tipo === 'vencidos') {
        $condiciones[] = "lp.fecha_vencimiento < CURDATE()";
        $condiciones[] = "lp.fecha_vencimiento BETWEEN '$desde' AND '$hasta'";
        $condiciones[] = "lp.activo = 1";
    } elseif ($tipo === 'desechados') {
        $condiciones[] = "lp.fecha_vencimiento < CURDATE()";
        $condiciones[] = "lp.fecha_vencimiento BETWEEN '$desde' AND '$hasta'";
        $condiciones[] = "lp.activo = 0";
    } elseif ($tipo === 'por_vencer') {
        $condiciones[] = "lp.fecha_vencimiento >= CURDATE()";
        $condiciones[] = "lp.fecha_vencimiento BETWEEN '$desde' AND '$hasta'";
        $condiciones[] = "lp.activo = 1";
    }

    // Filtro por categoría
    if ($categoria && is_numeric($categoria) && (int)$categoria > 0) {
        $condiciones[] = "p.id_categoria = $categoria";
    }

    $where = implode(" AND ", $condiciones);

    // Consulta agrupada por producto y fecha vencimiento
    $productos = $db->query("
        SELECT 
            p.id,
            p.codigo, 
            p.nombre, 
            lp.fecha_vencimiento, 
            SUM(lp.cantidad) AS cantidad_total
        FROM lotes_productos lp
        JOIN productos p ON p.id = lp.id_producto
        WHERE $where
        GROUP BY p.id, p.codigo, p.nombre, lp.fecha_vencimiento
        ORDER BY lp.fecha_vencimiento ASC
    ")->getResultArray();

    if (empty($productos)) {
        echo 'No hay productos que coincidan con los filtros.';
        exit();
    }

    // Crear el PDF
    $pdf = new \FPDF('P', 'mm', 'letter');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetTitle("Reporte PDF");
    $pdf->SetFont("Arial", 'B', 12);
    $pdf->Image("images/logotipo.png", 10, 5, 20);
    $pdf->Cell(0, 10, utf8_decode("REPORTE DE PRODUCTOS " . strtoupper(str_replace('_', ' ', $tipo))), 0, 1, 'C');
    $pdf->Ln(10);

    // Encabezado tabla
    $pdf->SetFont("Arial", 'B', 10);
    $pdf->Cell(30, 7, utf8_decode("Código"), 1, 0, 'C');
    $pdf->Cell(70, 7, utf8_decode("Nombre Producto"), 1, 0, 'C');
    $pdf->Cell(38, 7, utf8_decode("Fecha Vencimiento"), 1, 0, 'C');
    $pdf->Cell(20, 7, utf8_decode("Cajas"), 1, 0, 'C');  // antes fardos o cajas según jerarquía
    $pdf->Cell(20, 7, utf8_decode("Paquetes"), 1, 0, 'C');
    $pdf->Cell(20, 7, utf8_decode("Unidades"), 1, 1, 'C');

    // Contenido tabla
    $pdf->SetFont("Arial", '', 9);
    foreach ($productos as $prod) {
        // Obtener jerarquía con factores
        $datosJerarquia = $this->obtenerJerarquiaConFactores($prod['id']);
        if (empty($datosJerarquia)) {
            // Si no hay jerarquía, mostrar cantidades sin descomponer
            $niveles = [
                'fardos' => 0,
                'cajas' => 0,
                'unidades' => $prod['cantidad_total']
            ];
        } else {
            // Descomponer cantidades según jerarquía
            $desglose = $this->descomponerJerarquiaSucesiva($prod['cantidad_total'], $datosJerarquia['jerarquia']);
            $niveles = $desglose['niveles'];
        }

        $pdf->Cell(30, 6, utf8_decode($prod['codigo']), 1, 0, 'C');
        $pdf->Cell(70, 6, utf8_decode($prod['nombre']), 1, 0);
        $pdf->Cell(38, 6, date('d/m/Y', strtotime($prod['fecha_vencimiento'])), 1, 0, 'C');
        $pdf->Cell(20, 6, round($niveles['fardos'], 2), 1, 0, 'C');
        $pdf->Cell(20, 6, round($niveles['cajas'], 2), 1, 0, 'C');
        $pdf->Cell(20, 6, round($niveles['unidades'], 2), 1, 1, 'C');
    }

    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output('I', 'reporte_' . $tipo . '.pdf');
}


private function obtenerJerarquiaConFactores($id_producto)
{
    $db = \Config\Database::connect();

    $presentaciones = $db->table('presentaciones_productos')
        ->where('id_producto', $id_producto)
        ->get()
        ->getResultArray();

    if (empty($presentaciones)) return [];

    $presentacionesPorId = [];
    foreach ($presentaciones as $p) {
        $presentacionesPorId[$p['id']] = $p;
    }

    // Buscar la unidad base (cantidad_unidades = 1)
    $unidad = null;
    foreach ($presentaciones as $p) {
        if ((int)$p['cantidad_unidades'] === 1) {
            $unidad = $p;
            break;
        }
    }

    if (!$unidad) return [];

    $jerarquia = [];
    $actual = $unidad;
    $jerarquia[] = $actual;

    while (!empty($actual['id_padre']) && isset($presentacionesPorId[$actual['id_padre']])) {
        $padre = $presentacionesPorId[$actual['id_padre']];
        $jerarquia[] = $padre;
        $actual = $padre;
    }

    return ['jerarquia' => $jerarquia];
}

private function descomponerJerarquiaSucesiva($cantidad_total, $jerarquia)
{
    $niveles = [
        'unidades' => $cantidad_total,
        'cajas' => 0,
        'fardos' => 0
    ];

    // Paso 1: indexar por ID
    $porId = [];
    foreach ($jerarquia as $p) {
        $porId[$p['id']] = $p;
    }

    // Paso 2: encontrar unidad (la que no tiene hijos)
    $unidad = null;
    foreach ($jerarquia as $p) {
        $esHijo = false;
        foreach ($jerarquia as $q) {
            if ($q['id_padre'] == $p['id']) {
                $esHijo = true;
                break;
            }
        }
        if (!$esHijo) {
            $unidad = $p;
            break;
        }
    }

    if (!$unidad) return [
        'niveles' => $niveles,
        'exacto' => true
    ];
    //'Este producto tiene más de 3 niveles jerárquicos. Se ignorarán niveles superiores.
    // Paso 3: subir jerarquía desde unidad
    $caja = isset($porId[$unidad['id_padre']]) ? $porId[$unidad['id_padre']] : null;
    $fardo = ($caja && isset($porId[$caja['id_padre']])) ? $porId[$caja['id_padre']] : null;

    // Calcular cajas
    if ($caja) {
        $unidades_por_caja = (float) $caja['cantidad_unidades'];
        $niveles['cajas'] = round($cantidad_total / $unidades_por_caja, 2);
    }

    // Calcular fardos
    if ($fardo && $caja) {
        $cajas_por_fardo = (float) $fardo['cantidad_unidades'];
        $niveles['fardos'] = round($niveles['cajas'] / $cajas_por_fardo, 2);
    }

    // Comprobación exacta
    $es_exacto = true;
    if (
        isset($niveles['cajas']) && fmod($niveles['cajas'], 1) !== 0 ||
        isset($niveles['fardos']) && fmod($niveles['fardos'], 1) !== 0
    ) {
        $es_exacto = false;
    }

    return [
        'niveles' => $niveles,
        'exacto' => $es_exacto
    ];
}

public function obtenerStock($idProducto)
{
    $db = \Config\Database::connect();
    $builder = $db->table('lotes_productos');
    $builder->selectSum('cantidad', 'stock');
    $builder->where('id_producto', $idProducto);
    $builder->where('activo', 1); // solo lotes activos
    $query = $builder->get()->getRowArray();

    // Si no hay stock, devuelve 0
    $stock = $query['stock'] ?? 0;

    return $this->response->setJSON(['stock' => $stock]);
}


    


}
