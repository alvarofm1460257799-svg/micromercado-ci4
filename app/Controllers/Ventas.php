<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VentasModel;
use App\Models\TemporalVentaModel;
use App\Models\DetalleVentaModel;
use App\Models\DetalleCompraModel;
use App\Models\ProductosModel;
use App\Models\ConfiguracionModel;
use App\Models\CajasModel;
use App\Models\DetalleRolesPermisosModel;
use App\Models\LotesProductosModel;
use App\Models\ClientesModel;
use App\Models\UsuariosModel;
use App\Models\ComprasModel;




class Ventas extends BaseController
{
    protected $ventas,$compras,$detalle_compra, $temporal_venta, $detalle_venta, $productos, $configuracion, $cajas,$session,$detalleRoles, $lotes_productos, $cliente, $usuario ;
   

    public function __construct()
    {
        $this->ventas = new VentasModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->configuracion = new ConfiguracionModel();
        $this->productos =new ProductosModel();
        $this->cajas =new CajasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->lotes_productos = new LotesProductosModel();
        $this->cliente = new ClientesModel();
        $this->usuario = new UsuariosModel();
         $this->compras = new ComprasModel();
          $this->detalle_compra = new DetalleCompraModel();
        $this->session=session();
        helper(['form']);
       
    }
    
    public function index() {
        // Verifica si el usuario está logueado
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
    
        // Obtener los datos de ventas usando el método 'obtener()' del modelo VentasModel
        $datos = $this->ventas->obtener(1);
    
      
        $data = [
            'titulo' => 'Ventas',
            'datos' => $datos,
        
        ];
    
        // Cargar las vistas con los datos
        echo view('header');
        echo view('ventas/ventas', $data);
        echo view('footer');
    }
    
    
    
    

    public function reportes()
    {
        // Verifica permisos
    
    
        // Verifica si el usuario está autenticado
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
    
        // Verificar si se enviaron las fechas
        $fecha_inicio = $this->request->getPost('fecha_inicio');
        $fecha_fin = $this->request->getPost('fecha_fin');
    
        // Si se enviaron fechas, filtrar las ventas por ese rango
        if ($fecha_inicio && $fecha_fin) {
            $datos = $this->ventas->obtenerVentasPorFechas($fecha_inicio, $fecha_fin);
        } else {
            // Mostrar todas las ventas activas si no se especifican fechas
            $datos = $this->ventas->obtener(1);
        }
    
        $data = ['titulo' => 'Ventas', 'datos' => $datos];
    
        echo view('header');
        echo view('ventas/reportes', $data);
        echo view('footer');
    }
    
    
  public function venta()
{
    if (!isset($this->session->id_usuario)) {
        return redirect()->to(base_url());
    }

    // Si no existe un ID temporal en sesión, crear uno nuevo
    if (!$this->session->has('id_venta_tmp')) {
        $this->session->set('id_venta_tmp', uniqid());
    }

    echo view('header');
    echo view('ventas/caja');
    echo view('footer');
}

public function guarda()
{
    $total = preg_replace('/[\$,]/', '', $this->request->getPost('total'));
    $session = session();

    $id_caja = $session->id_caja;
    $id_cliente = $this->request->getPost('id_cliente');
    $forma_pago = $this->request->getPost('forma_pago');

    $this->temporal_venta = new TemporalVentaModel();
    $this->lotes_productos = new LotesProductosModel();
    $this->detalle_compra = new DetalleCompraModel();
    $this->detalle_venta = new DetalleVentaModel();

    $id_venta = $this->request->getPost('id_venta');
    $permitirSinStock = $this->request->getPost('permitir_sin_stock');

    $productosVenta = $this->temporal_venta->porVenta($id_venta);

    if (empty($productosVenta)) {
        session()->setFlashdata('error', 'No hay productos en la venta.');
        return redirect()->back();
    }

    // Folio dinámico
    $ultimoFolio = $this->ventas->obtenerUltimoFolio();
    $nuevoFolioNumero = !empty($ultimoFolio)
        ? intval(preg_replace('/[^0-9]/', '', $ultimoFolio->folio)) + 1
        : 1;
    $folio = 'NV-' . $nuevoFolioNumero;

    // Registrar la venta
    $resultadoId = $this->ventas->insertaVenta(
        $folio,
        $total,
        $session->id_usuario,
        $id_caja,
        $id_cliente,
        $forma_pago
    );

    if (!$resultadoId) {
        session()->setFlashdata('error', 'Error al registrar la venta.');
        return redirect()->back();
    }

    $ventasSinStockModel = new \App\Models\VentasSinStockModel();

    foreach ($productosVenta as $producto) {
        $id_producto = $producto['id_producto'];

        // -------------------------------
        // Calcular cantidad total en unidades según presentación
        // -------------------------------
        $cantidad_total_en_unidades = 0;

        // Si se seleccionó presentación padre (cantidad_mayor > 0)
        if (!empty($producto['cantidad_mayor']) && $producto['cantidad_mayor'] > 0) {
            $cantidad_total_en_unidades = $producto['cantidad_mayor'] * ($producto['cantidad_unidades'] ?? 1);
        } 
        // Si se seleccionó presentación base (unidad)
        else {
            $cantidad_total_en_unidades = $producto['cantidad'] ?? 0;
        }

        // -------------------------------
        // Obtener lotes disponibles
        // -------------------------------
        $lotes = $this->lotes_productos->where('id_producto', $id_producto)
            ->where('cantidad >', 0)
            ->where('activo', 1)
            ->orderBy('fecha_vencimiento', 'ASC')
            ->findAll();

        // -------------------------------
        // Repartir cantidad en lotes
        // -------------------------------
        $cantidad_restante = $cantidad_total_en_unidades;
        $id_lote_usado = null;
        $precio_compra_real = 0;

        foreach ($lotes as $lote) {
            if ($cantidad_restante <= 0) break;

            $vendido = min($lote['cantidad'], $cantidad_restante);
            $cantidad_restante -= $vendido;

            // Guardar id_lote usado solo la primera vez
            if ($id_lote_usado === null) {
                $id_lote_usado = $lote['id'];
            }

            if (!empty($lote['id_detalle_compra'])) {
                $detalleCompra = $this->detalle_compra->find($lote['id_detalle_compra']);
                $precio_compra_real = $detalleCompra['precio'] ?? 0;
            }

            // Actualizar cantidad del lote
            $this->lotes_productos->update($lote['id'], [
                'cantidad' => $lote['cantidad'] - $vendido
            ]);
        }

        // -------------------------------
        // Guardar detalle de venta
        // -------------------------------
        $this->detalle_venta->save([
            'id_venta' => $resultadoId,
            'id_producto' => $id_producto,
            'id_presentacion' => $producto['id_presentacion'],
            'nombre' => $producto['nombre'],
            'cantidad' => $producto['cantidad'],
            'cantidad_mayor' => $producto['cantidad_mayor'],
            'precio' => $producto['precio'],
            'precio_compra' => $precio_compra_real,
            'id_lote' => $id_lote_usado
        ]);

        // -------------------------------
        // Registrar venta sin stock si aplica
        // -------------------------------
        if ($cantidad_restante > 0 && $permitirSinStock === 'on') {
            $ventasSinStockModel->save([
                'id_venta' => $resultadoId,
                'id_producto' => $id_producto,
                'nombre_producto' => $producto['nombre'],
                'cantidad_faltante' => $cantidad_restante
            ]);
        }
    }

    // -------------------------------
    // Limpiar tabla temporal
    // -------------------------------
    $this->temporal_venta->eliminarVenta($id_venta);
    $session->remove('id_venta_tmp');

    return redirect()->to(base_url() . "/ventas/muestraTicket/" . $resultadoId);
}





function muestraTicket($id_venta){
    $data['id_venta'] = $id_venta;
    echo view('header');
    echo view('ventas/ver_ticket',$data);
    echo view('footer');


 }
function generaTicket($id_venta) {
    $datosVenta = $this->ventas->where('id', $id_venta)->first();

    $detalleVenta = $this->detalle_venta
        ->select('detalle_venta.*, pp.cantidad_unidades')
        ->join('presentaciones_productos pp', 'pp.id = detalle_venta.id_presentacion', 'left')
        ->where('detalle_venta.id_venta', $id_venta)
        ->findAll();

    $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;
    $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor;
    $leyendaTicket = $this->configuracion->select('valor')->where('nombre', 'ticket_leyenda')->get()->getRow()->valor;

    $usuario = $this->usuario->select('usuario')->where('id', $datosVenta['id_usuario'])->get()->getRow();
    $nombreUsuario = $usuario ? $usuario->usuario : 'Desconocido';

    $cliente = $this->cliente->select('nombre')->where('id', $datosVenta['id_cliente'])->get()->getRow();
    $nombreCliente = $cliente ? $cliente->nombre : 'Desconocido';

    $pdf = new \FPDF('P', 'mm', array(80,200));
    $pdf->AddPage();
    $pdf->SetMargins(3, 5, 3);
    $pdf->SetTitle("Venta");

    // Cabecera
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(70, 5, $nombreTienda, 0, 1, 'C');
    $pdf->Image(base_url() . '/images/logotipo.png', 5, 5, 15, 14, 'PNG');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 5, $direccionTienda, 0, 1, 'C');
    $pdf->Ln(5);

    // Datos de venta
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(25, 5, utf8_decode('Fecha y Hora: '), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(45, 5, $datosVenta['fecha_alta'], 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(25, 5, utf8_decode('Ticket: '), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(45, 5, $datosVenta['folio'], 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(25, 5, 'Atendido por: ', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(45, 5, $nombreUsuario, 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(25, 5, 'Cliente: ', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(45, 5, $nombreCliente, 0, 1, 'L');

    $pdf->Line(5, $pdf->GetY(), 75, $pdf->GetY());
    $pdf->Ln(2);

    // Encabezado de tabla
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(9, 5, 'Cant.', 0, 0, 'L');
    $pdf->Cell(35, 5, 'Nombre', 0, 0, 'L');
    $pdf->Cell(15, 5, 'Precio', 0, 0, 'R');
    $pdf->Cell(15, 5, 'Importe', 0, 1, 'R');

    $pdf->SetFont('Arial', '', 7);

  foreach ($detalleVenta as $row) {
    $cantidadUsar = ($row['cantidad_unidades'] == 1) ? $row['cantidad'] : $row['cantidad_mayor'];
    $importe = $cantidadUsar * $row['precio'];

    // Recortar nombre a máximo 20 caracteres
    $maxChars = 35;
    $nombreAjustado = utf8_decode($row['nombre']);
    if (mb_strlen($nombreAjustado) > $maxChars) {
        $nombreAjustado = mb_substr($nombreAjustado, 0, $maxChars);
    }

    $pdf->Cell(9, 5, $cantidadUsar, 0, 0, 'L');
    $pdf->Cell(35, 5, $nombreAjustado, 0, 0, 'L');
    $pdf->Cell(15, 5, number_format($row['precio'], 2, '.', ','), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($importe, 2, '.', ','), 0, 1, 'R');
}


    $pdf->Line(5, $pdf->GetY(), 75, $pdf->GetY());
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(70, 5, 'Total Bs ' . number_format($datosVenta['total'], 2, '.', ','), 0, 1, 'R');
    $pdf->Ln();
    $pdf->MultiCell(70, 4, $leyendaTicket, 0, 'C', 0);

    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output("ticket_pdf.pdf", "I");
}







  public function eliminar($id)
  {
      // Verificar si la venta tiene más de 24 horas
      $venta = $this->ventas->find($id);
      if ($venta) {
          $fechaVenta = new \DateTime($venta['fecha_alta']);
          $fechaActual = new \DateTime();
  
          $intervalo = $fechaVenta->diff($fechaActual);
          if ($intervalo->days >= 1) {
              session()->setFlashdata('error', 'No se puede eliminar la venta. Ha pasado más de un día desde su creación.');
              return redirect()->to(base_url() . '/ventas');
          }
      } else {
          session()->setFlashdata('error', 'Venta no encontrada.');
          return redirect()->to(base_url() . '/ventas');
      }
  
      // Restaurar stock para cada producto asociado
      $productos = $this->detalle_venta->where('id_venta', $id)->findAll();
      foreach ($productos as $producto) {
          $this->productos->actualizaStock($producto['id_producto'], $producto['cantidad'], '+');
      }
  
      // Marcar la venta como eliminada (borrado lógico)
      $this->ventas->update($id, ['activo' => 0]);
  
      session()->setFlashdata('success', 'Venta eliminada correctamente.');
      return redirect()->to(base_url() . '/ventas');
  }
  
  public function eliminados()
  {
    $datos=$this->ventas->obtener(0);
      $data = ['titulo' => 'Ventas Eliminadas', 'datos' => $datos];
      
      echo view('header');
      echo view('ventas/eliminados', $data);
      echo view('footer');
      
  }

  public function calcularVentas()
  {
    $datos=$this->ventas->obtener(0);
      $data = ['titulo' => 'Ventas Eliminadas', 'datos' => $datos];
      
      echo view('header');
      echo view('ventas/eliminados', $data);
      echo view('footer');
      
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

  public function generar()
{
    if (!isset($this->session->id_usuario)) {
        return redirect()->to(base_url());
    }

    $tipo = $this->request->getPost('tipo_reporte');
    $fecha_inicio = $this->request->getPost('fecha_inicio');
    $fecha_fin = $this->request->getPost('fecha_fin');

    if (!$fecha_inicio || !$fecha_fin) {
        return redirect()->back()->with('error', 'Debe seleccionar un rango de fechas.');
    }

    switch ($tipo) {
        case 'ventas':
            return $this->generaReporteVentas($fecha_inicio, $fecha_fin);
        case 'compras':
            return $this->generaReporteCompras($fecha_inicio, $fecha_fin);
        case 'ganancias':
            return $this->generaReporteGanancias($fecha_inicio, $fecha_fin);
        default:
            return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}





public function generaReporteCompras() {
    if (!isset($this->session->id_usuario)) {
        return redirect()->to(base_url());
    }

    $fecha_inicio = $this->request->getPost('fecha_inicio');
    $fecha_fin = $this->request->getPost('fecha_fin');

    if (!$fecha_inicio || !$fecha_fin) {
        return redirect()->to(base_url('compras/reportes'))->with('error', 'Debe seleccionar un rango de fechas');
    }

    $datos = $this->compras->obtenerComprasAgrupadas($fecha_inicio, $fecha_fin);

    $pdf = new \FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Reporte de Compras'), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Desde: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' Hasta: ' . date('d/m/Y', strtotime($fecha_fin)), 0, 1, 'C');
    $pdf->Ln(5);

    // Función para imprimir encabezado de tabla
    $imprimirEncabezado = function($pdf) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 8, 'Fecha(s)', 1, 0, 'C');
        $pdf->Cell(40, 8, 'Proveedor', 1, 0, 'C');
        $pdf->Cell(80, 8, 'Producto', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Unidades', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Paquetes', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Cajas', 1, 0, 'C');
        $pdf->Cell(22, 8, 'Total Unid.', 1, 0, 'C');
        $pdf->Cell(28, 8, 'Total (Bs)', 1, 1, 'C');
    };

    $imprimirEncabezado($pdf);

    $totalGeneral = 0;

    // Agrupar datos por fecha y proveedor
    $grupos = [];
    foreach ($datos as $compra) {
        $key = $compra['fecha'] . '|' . $compra['proveedor'];
        $grupos[$key][] = $compra;
    }

    foreach ($grupos as $key => $compras) {
        [$fecha, $proveedor] = explode('|', $key);

        $cantidadFilas = count($compras);

        // Altura total que ocuparán las filas de productos de este grupo
        $altoFila = 8;

        // Para manejar salto de página:
        $margenInferior = 15;
        $limiteSalto = $pdf->GetPageHeight() - $margenInferior;

        $altoNecesario = $altoFila * $cantidadFilas + $altoFila; // productos + fila totales

        // Salto de página si no hay espacio suficiente
        if ($pdf->GetY() + $altoNecesario > $limiteSalto) {
            $pdf->AddPage();
            $imprimirEncabezado($pdf);
        }

        // Imprimir fecha y proveedor en celdas con altura múltiple
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, $altoFila * $cantidadFilas, date('d/m/Y', strtotime($fecha)), 1, 0, 'C');
        $pdf->Cell(40, $altoFila * $cantidadFilas, utf8_decode($proveedor), 1, 0, 'C');

        $pdf->SetFont('Arial', '', 10);

        // Posiciones para alinear columnas productos
        $xProductos = $pdf->GetX();
        $yProductos = $pdf->GetY();

        $totalGrupoCantidad = 0;
        $totalGrupoMonto = 0;
        $primerProducto = true;

        foreach ($compras as $compra) {
            if (!$primerProducto) {
                $pdf->SetXY($xProductos, $pdf->GetY());
            }

            // Obtener jerarquía y descomponer cantidades
            $jerarquiaData = $this->obtenerJerarquiaConFactores($compra['id_producto']);
            $descompuesto = $this->descomponerJerarquiaSucesiva($compra['cantidad_total'], $jerarquiaData['jerarquia'] ?? []);

            $unidades = $descompuesto['niveles']['unidades'] ?? 0;
            $cajas = $descompuesto['niveles']['cajas'] ?? 0;
            $fardos = $descompuesto['niveles']['fardos'] ?? 0;

            $pdf->Cell(80, $altoFila, utf8_decode($compra['producto']), 1, 0, 'L');
            $pdf->Cell(20, $altoFila, number_format($unidades, 2), 1, 0, 'C');
            $pdf->Cell(20, $altoFila, number_format($cajas, 2), 1, 0, 'C');
            $pdf->Cell(20, $altoFila, number_format($fardos, 2), 1, 0, 'C');
            $pdf->Cell(22, $altoFila, number_format($compra['cantidad_total'], 2), 1, 0, 'C');
            $pdf->Cell(28, $altoFila, number_format($compra['total_producto'], 2), 1, 1, 'C');

            $totalGrupoCantidad += $compra['cantidad_total'];
            $totalGrupoMonto += $compra['total_producto'];

            $primerProducto = false;
        }

        // Fila resumen total por grupo
       // $pdf->SetFont('Arial', 'B', 10);
        //$pdf->Cell(180, $altoFila, 'Totales para fecha y proveedor', 1, 0, 'R');
       // $pdf->Cell(22, $altoFila, number_format($totalGrupoCantidad, 2), 1, 0, 'C');
       // $pdf->Cell(28, $altoFila, number_format($totalGrupoMonto, 2), 1, 1, 'C');

        $totalGeneral += $totalGrupoMonto;
    }

    // Total general al final
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(205, 10, 'Total General de Compras:', 1, 0, 'R');
    $pdf->Cell(50, 10, 'Bs ' . number_format($totalGeneral, 2), 1, 1, 'C');

    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output("reporte_compras.pdf", "I");
}


public function generaReporteVentas() {
    if (!isset($this->session->id_usuario)) {
        return redirect()->to(base_url());
    }

    $fecha_inicio = $this->request->getPost('fecha_inicio');
    $fecha_fin = $this->request->getPost('fecha_fin');

    if ($fecha_inicio && $fecha_fin) {
        $datos = $this->ventas->obtenerVentasAgrupadas($fecha_inicio, $fecha_fin);

        $pdf = new \FPDF('L', 'mm', 'A4'); // Horizontal
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('Reporte de Ventas'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Desde: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' Hasta: ' . date('d/m/Y', strtotime($fecha_fin)), 0, 1, 'C');
        $pdf->Ln(5);

        // Encabezado
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, 10, 'Fecha', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Cliente', 1, 0, 'C');
        $pdf->Cell(85, 10, 'Productos', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Fardos', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Cajas', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Unidades', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Total (Bs)', 1, 1, 'C');

        $totalGeneral = 0;

        // Agrupar por fecha y cliente
     // Agrupar por fecha y cliente
$grupos = [];
foreach ($datos as $venta) {
    $key = $venta['fecha'] . '|' . $venta['cliente'];
    $grupos[$key][] = $venta;
}

foreach ($grupos as $key => $ventasDelGrupo) {
    [$fecha, $cliente] = explode('|', $key);

    // Calcular altura total del grupo
    $alturaTotal = 0;
    foreach ($ventasDelGrupo as $venta) {
        $productoNombre = $venta['producto'];
        $lineas = ceil(strlen($productoNombre) / 50);
        $alturaTotal += max(10, $lineas * 6);
    }

    $pdf->SetFont('Arial', '', 10);

    // Imprimir celda fecha con altura total
    $pdf->Cell(25, $alturaTotal, date('d/m/Y', strtotime($fecha)), 1, 0, 'C');
    // Imprimir celda cliente con altura total
    $pdf->Cell(35, $alturaTotal, utf8_decode($cliente), 1, 0, 'C');

    // Guardar posición para imprimir productos
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Imprimir filas de productos
    foreach ($ventasDelGrupo as $venta) {
        $productoNombre = $venta['producto'];
        $cantidad_total = $venta['cantidad_total'];
        $total_grupo = $venta['total_producto'];

        // Descomponer cantidades
        $jerarquiaData = $this->obtenerJerarquiaConFactores($venta['id_producto']);
        $descompuesto = $this->descomponerJerarquiaSucesiva($cantidad_total, $jerarquiaData['jerarquia'] ?? []);
        $fardos = $descompuesto['niveles']['fardos'] ?? 0;
        $cajas = $descompuesto['niveles']['cajas'] ?? 0;
        $unidades = $descompuesto['niveles']['unidades'] ?? 0;

        // Altura de esta fila
        $lineas = ceil(strlen($productoNombre) / 50);
        $altura = max(10, $lineas * 6);

        // Columna producto
        $pdf->SetXY($x, $y);
        $pdf->MultiCell(85, 6, utf8_decode($productoNombre), 0);

        // Posición para las demás celdas (fardos, cajas, unidades, total)
        $pdf->SetXY($x + 85, $y);
        $pdf->Cell(20, $altura, $fardos, 1, 0, 'C');
        $pdf->Cell(20, $altura, $cajas, 1, 0, 'C');
        $pdf->Cell(20, $altura, $unidades, 1, 0, 'C');
        $pdf->Cell(30, $altura, number_format($total_grupo, 2), 1, 1, 'C');

        $totalGeneral += $total_grupo;

        // Mover cursor Y para la siguiente fila
        $y += $altura;
    }
}


        // Total general
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(205, 10, 'Total General:', 1, 0, 'R');
        $pdf->Cell(30, 10, 'Bs ' . number_format($totalGeneral, 2), 1, 1, 'C');

        // Mostrar PDF
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output("reporte_ventas.pdf", "I");

    } else {
        return redirect()->to(base_url('ventas/reportes'))->with('error', 'Debe seleccionar un rango de fechas');
    }
}

private function generaReporteGanancias($fecha_inicio, $fecha_fin)
{
    $ventas = $this->ventas->obtenerDetalleGanancias($fecha_inicio, $fecha_fin);

    if (empty($ventas)) {
        return redirect()->back()->with('error', 'No hay datos de ganancias en el rango seleccionado.');
    }

    $pdf = new \FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    // Título
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Resumen de Ganancias por Producto'), 0, 1, 'C');

    // Fechas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Desde: ' . date('d/m/Y', strtotime($fecha_inicio)) . 
                     ' Hasta: ' . date('d/m/Y', strtotime($fecha_fin)), 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(60, 10, 'Producto', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Total Compra', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Total Venta', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Ganancia', 1, 1, 'C', true);

    // Contenido
    $pdf->SetFont('Arial', '', 10);
    $gananciaTotal = 0;

    foreach ($ventas as $venta) {
        $cantidad = $venta['cantidad_vendida'];
        $total_compra = $venta['precio_compra_total'];  // ya multiplicado en la consulta
        $total_venta = $venta['total_venta_real'];     // cantidad_mayor * precio de venta
        $ganancia = $total_venta - $total_compra;      // ganancia total considerando unidades

        $pdf->Cell(60, 8, utf8_decode($venta['producto']), 1);
        $pdf->Cell(25, 8, $cantidad, 1, 0, 'C');
        $pdf->Cell(35, 8, number_format($total_compra, 2), 1, 0, 'R');
        $pdf->Cell(35, 8, number_format($total_venta, 2), 1, 0, 'R');
        $pdf->Cell(25, 8, number_format($ganancia, 2), 1, 1, 'R');

        $gananciaTotal += $ganancia;
    }

    // Total general
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'Ganancia Total:', 1, 0, 'R', true);
    $pdf->Cell(25, 10, 'Bs ' . number_format($gananciaTotal, 2), 1, 1, 'R');

    // Generar PDF
    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output("resumen_ganancias.pdf", "I");
}

public function obtenerDatosGraficaMensual()
{
    $db = \Config\Database::connect();
    $db->query("SET lc_time_names = 'es_ES';");

    // Obtener año de la request GET, si no viene usar el año actual
    $anio = $this->request->getGet('anio') ?? date('Y');
    $anio = (int)$anio; // cast a entero por seguridad

    // Inicializar meses del año elegido
    $meses = [];
    for ($i = 1; $i <= 12; $i++) {
        $mes = $anio . '-' . str_pad($i, 2, "0", STR_PAD_LEFT);
        $meses[$mes] = [
            'ventas' => 0,
            'compras' => 0,
            'ganancias' => 0,
            'ventas_sin_stock' => 0,
            'ganancias_sin_stock' => 0,
        ];
    }

    // === Ventas normales ===
    $ventas = $db->query("
        SELECT DATE_FORMAT(fecha_alta, '%Y-%m') AS mes, SUM(total) AS total_ventas
        FROM ventas
        WHERE YEAR(fecha_alta) = $anio AND activo = 1
        GROUP BY mes
    ")->getResultArray();

    foreach ($ventas as $row) {
        $mes = $row['mes'];
        if (isset($meses[$mes])) {
            $meses[$mes]['ventas'] = (float)$row['total_ventas'];
        }
    }

    // === Compras ===
    $compras = $db->query("
        SELECT DATE_FORMAT(fecha_alta, '%Y-%m') AS mes, SUM(total) AS total_compras
        FROM compras
        WHERE YEAR(fecha_alta) = $anio AND activo = 1
        GROUP BY mes
    ")->getResultArray();

    foreach ($compras as $row) {
        $mes = $row['mes'];
        if (isset($meses[$mes])) {
            $meses[$mes]['compras'] = (float)$row['total_compras'];
        }
    }

    // === Ganancias normales ===
    $ganancias = $db->query("
        SELECT 
            DATE_FORMAT(dv.fecha_alta, '%Y-%m') AS mes,
            SUM(dv.cantidad * (dv.precio - (
                SELECT IFNULL(SUM(dc.precio * dc.cantidad) / NULLIF(SUM(dc.cantidad), 0), 0)
                FROM detalle_compra dc
                WHERE dc.id_producto = dv.id_producto
                  AND dc.id_presentacion = dv.id_presentacion
                  AND dc.fecha_alta <= dv.fecha_alta
            ))) AS ganancia_real
        FROM detalle_venta dv
        JOIN ventas v ON dv.id_venta = v.id
        WHERE YEAR(dv.fecha_alta) = $anio AND v.activo = 1
        GROUP BY mes
    ")->getResultArray();

    foreach ($ganancias as $row) {
        $mes = $row['mes'];
        if (isset($meses[$mes])) {
            $meses[$mes]['ganancias'] = (float)$row['ganancia_real'];
        }
    }

    // === Ventas sin stock ===
    $ventasSS = $db->query("
        SELECT 
            DATE_FORMAT(dv.fecha_alta, '%Y-%m') AS mes,
            SUM(dv.cantidad * dv.precio) AS total_ventas_sin_stock
        FROM detalle_venta dv
        JOIN ventas v ON dv.id_venta = v.id
        JOIN ventas_sin_stock vss ON vss.id_venta = dv.id_venta AND vss.id_producto = dv.id_producto
        WHERE YEAR(dv.fecha_alta) = $anio AND v.activo = 1
        GROUP BY mes
    ")->getResultArray();

    foreach ($ventasSS as $row) {
        $mes = $row['mes'];
        if (isset($meses[$mes])) {
            $meses[$mes]['ventas_sin_stock'] = (float)$row['total_ventas_sin_stock'];
        }
    }

    // === Ganancias sin stock ===
    $gananciasSS = $db->query("
        SELECT 
            DATE_FORMAT(dv.fecha_alta, '%Y-%m') AS mes,
            SUM(dv.cantidad * (dv.precio - (
                SELECT IFNULL(SUM(dc.precio * dc.cantidad) / NULLIF(SUM(dc.cantidad), 0), 0)
                FROM detalle_compra dc
                WHERE dc.id_producto = dv.id_producto
                  AND dc.id_presentacion = dv.id_presentacion
                  AND dc.fecha_alta <= dv.fecha_alta
            ))) AS ganancia_sin_stock
        FROM detalle_venta dv
        JOIN ventas v ON dv.id_venta = v.id
        JOIN ventas_sin_stock vss ON vss.id_venta = v.id AND dv.id_producto = vss.id_producto
        WHERE YEAR(dv.fecha_alta) = $anio AND v.activo = 1
        GROUP BY mes
    ")->getResultArray();

    foreach ($gananciasSS as $row) {
        $mes = $row['mes'];
        if (isset($meses[$mes])) {
            $meses[$mes]['ganancias_sin_stock'] = (float)$row['ganancia_sin_stock'];
        }
    }

    // Preparar respuesta JSON
    return $this->response->setJSON([
        'meses' => array_keys($meses),
        'ventas' => array_column($meses, 'ventas'),
        'ventas_sin_stock' => array_column($meses, 'ventas_sin_stock'),
        'compras' => array_column($meses, 'compras'),
        'ganancias' => array_column($meses, 'ganancias'),
        'ganancias_sin_stock' => array_column($meses, 'ganancias_sin_stock'),
    ]);
}





public function sinStock()
{
    $ventaSinStockModel = new \App\Models\VentasSinStockModel();
    $datos['titulo'] = 'Ventas realizadas sin stock';
    $datos['ventas'] = $ventaSinStockModel
                        ->select('ventas_sin_stock.*, productos.nombre AS nombre_real')
                        ->join('productos', 'productos.id = ventas_sin_stock.id_producto', 'left')
                        ->orderBy('ventas_sin_stock.fecha', 'DESC')
                        ->findAll();

    echo view('header');
    echo view('ventas/sin_stock', $datos);
    echo view('footer');
}


















}
?>