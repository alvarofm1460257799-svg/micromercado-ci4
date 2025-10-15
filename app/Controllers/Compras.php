<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComprasModel;
use App\Models\TemporalCompraModel;
use App\Models\DetalleVentaModel;
use App\Models\ProductosModel;
use App\Models\ConfiguracionModel;
use App\Models\DetalleRolesPermisosModel;
use App\Models\CajasModel;
use App\Models\DetalleCompraModel;
use App\Models\LotesProductosModel;
use App\Models\PresentacionesModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Compras extends BaseController
{
    protected $compras, $temporal_compra, $detalle_compra, $productos, $configuracion,$detalleRoles, $cajas, $detalle_venta, $lote_productos,$presentaciones_model ;
    protected $reglas;

    public function __construct()
    {
        $this->compras = new ComprasModel();
        $this->productos = new ProductosModel();
        $this->detalle_compra = new DetalleCompraModel();
        $this->configuracion = new ConfiguracionModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->lote_productos = new LotesProductosModel();
        $this->temporal_compra = new TemporalCompraModel();
        $this->presentaciones_model = new PresentacionesModel();
        
        $this->cajas =new CajasModel();
        helper(['form']);
       
    }

    public function index($activo_proveedor = 1, $activo_compra = 1) {
        $compras = $this->compras->obtener($activo_proveedor, $activo_compra);

        $data = [
            'titulo' => 'Compras',
            'compras' => $compras
        ];

        echo view('header');
        echo view('compras/compras', $data);
        echo view('footer');
    }

    public function reportes()
    {
     
    
        // Verifica si el usuario está autenticado
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
    
        // Verificar si se enviaron las fechas
        $fecha_inicio = $this->request->getPost('fecha_inicio');
        $fecha_fin = $this->request->getPost('fecha_fin');
    
        // Si se enviaron fechas, filtrar las ventas por ese rango
        if ($fecha_inicio && $fecha_fin) {
            $datos = $this->compras->obtenerComprasPorFechas($fecha_inicio, $fecha_fin);
        } else {
            // Mostrar todas las ventas activas si no se especifican fechas
            $datos = $this->compras->obtener(1);
        }
    
        $data = ['titulo' => 'Ventas', 'datos' => $datos];
    
        echo view('header');
        echo view('compras/reportes', $data);
        echo view('footer');
    }
    
    public function kardex($activo_proveedor = 1, $activo_compra = 1) {

        $compras = $this->compras->obtener($activo_proveedor, $activo_compra);
    
        $data = ['titulo' => 'Compras', 'compras' => $compras];
        
        echo view('header');
        echo view('compras/kardex', $data);
        echo view('footer');
    }
  
    //TABLA ELIMINADOS
    public function eliminar($id)
    {
     
        // Obtener los productos de esta compra
        $productosCompra = $this->detalle_compra->where('id_compra', $id)->findAll();
    
        if (empty($productosCompra)) {
            session()->setFlashdata('error', 'No se encontraron productos en esta compra.');
            return redirect()->back();
        }
    
        // Verificar si hay ventas activas relacionadas a productos de esta compra
        $db = \Config\Database::connect();
    
        foreach ($productosCompra as $producto) {
            $query = $db->query("
                SELECT dv.id_venta 
                FROM detalle_venta dv
                JOIN ventas v ON dv.id_venta = v.id
                WHERE dv.id_producto = ? 
                AND v.fecha_alta >= (
                    SELECT c.fecha_alta 
                    FROM compras c 
                    WHERE c.id = ?
                )
                AND v.activo = 1
            ", [$producto['id_producto'], $id]);
    
            $ventaExistente = $query->getResult();
    
            if (!empty($ventaExistente)) {
                session()->setFlashdata(
                    'error', 
                    'No se puede eliminar esta compra porque ya tiene ventas posteriores registradas para el producto "' . 
                    $producto['nombre'] . '".'
                );
                return redirect()->to(base_url() . '/compras');
            }
        }
    
        // Reducir el stock si no hay ventas posteriores
        foreach ($productosCompra as $producto) {
            $this->productos->actualizaStock($producto['id_producto'], $producto['cantidad'], '-');
        }
    
        // Marcar la compra como eliminada (borrado lógico)
        $this->compras->update($id, ['activo' => 0]);
    
        session()->setFlashdata('success', 'Compra eliminada correctamente.');
        return redirect()->to(base_url() . '/compras');
    }
    
    

    
    
    public function eliminados($activo = 0)
    {
        $compras = $this->compras->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Compras eliminadas', 'datos' => $compras];
        
       
        echo view('header');
        echo view('compras/eliminados', $data);
        echo view('footer');
    }

public function nuevo()
{
    $session = session();

    // Si ya existe un id_compra_tmp en la sesión, no lo regeneres
    if (!$session->has('id_compra_tmp')) {
        $session->set('id_compra_tmp', uniqid());
    }

    echo view('header');
    echo view('compras/nuevo');
    echo view('footer');
}


public function guarda() {
    $id_compra = $this->request->getPost('id_compra');
    $total = preg_replace('/[\$,]/', '', $this->request->getPost('total'));
    $session = session();

    // Obtener productos de la tabla temporal
    $productos = $this->temporal_compra->where('folio', $id_compra)->findAll();

    if (empty($productos)) {
        return redirect()->back()->with('error', 'No hay productos para completar la compra.');
    }

    // Generar nuevo folio tipo NC-##
    $ultimoFolio = $this->compras->query("SELECT folio FROM compras WHERE folio LIKE 'NC-%' 
        ORDER BY CAST(SUBSTRING(folio, 4) AS UNSIGNED) DESC LIMIT 1")->getRow();

    $nuevoFolioNumero = 7;
    if (!empty($ultimoFolio)) {
        $ultimoNumero = intval(substr($ultimoFolio->folio, 3));
        $nuevoFolioNumero = $ultimoNumero + 1;
    }

    $folio_formateado = 'NC-' . $nuevoFolioNumero;

    // Insertar cabecera de compra
    $resultadoId = $this->compras->insertaCompra($folio_formateado, $total, $session->id_usuario);

    if ($resultadoId) {
        foreach ($productos as $producto) {
            $id_producto = $producto['id_producto'];
            $id_presentacion = $producto['id_presentacion'];

            // --- TRAER PRECIO COMPRA REAL DESDE DETALLE_COMPRA EXISTENTE ---
            $precioCompraReal = $producto['precio_compra']; // por defecto del temporal
            if (!isset($producto['precio_compra']) || $producto['precio_compra'] == 0) {
                $ultimoDetalleCompra = $this->detalle_compra
                    ->where('id_producto', $id_producto)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($ultimoDetalleCompra) {
                    $precioCompraReal = $ultimoDetalleCompra['precio'];
                }
            }
            // --- FIN TRAER PRECIO REAL ---

            // Guardar detalle de compra
            $this->detalle_compra->save([
                'id_compra' => $resultadoId,
                'id_producto' => $id_producto,
                'id_presentacion' => $id_presentacion,
                'nombre' => $producto['nombre'],
                'cantidad' => $producto['cantidad'],
                'cantidad_mayor' => isset($producto['cantidad_mayor']) ? floatval($producto['cantidad_mayor']) : 1.00,
                'precio' => $precioCompraReal,
                'precio_mayor' => $producto['precio_compra_m'],
                'precio_venta' => $producto['precio_venta'],
                'precio_venta_mayor' => $producto['precio_venta_m'],
                'subtotal' => $producto['subtotal'],
                'fecha_vencimiento' => $producto['fecha_vence']
            ]);

            // Obtener ID del detalle recién insertado
            $id_detalle_compra = $this->detalle_compra->getInsertID();

            // --- Actualizar tabla productos ---
            $datosProducto = [
                'precio_compra' => $precioCompraReal
            ];
            if (isset($producto['precio_venta']) && $producto['precio_venta'] > 0) {
                $datosProducto['precio_venta'] = $producto['precio_venta'];
            }
            $this->productos->update($id_producto, $datosProducto);

            // Obtener presentación seleccionada
            $presentacionSeleccionada = $this->presentaciones_model->find($id_presentacion);

            if ($presentacionSeleccionada) {
                // 1. Buscar la presentación base (unidad) del producto
                $presentacionUnidad = $this->presentaciones_model
                    ->where('id_producto', $id_producto)
                    ->where('cantidad_unidades', 1)
                    ->first();

                if ($presentacionUnidad) {
                    $datosUnidad = [
                        'precio_compra' => $precioCompraReal
                    ];
                    if (isset($producto['precio_venta']) && $producto['precio_venta'] > 0) {
                        $datosUnidad['precio_venta'] = $producto['precio_venta'];
                    }
                    $this->presentaciones_model->update($presentacionUnidad['id'], $datosUnidad);
                }

                // 2. Guardar precio por mayor
                if (
                    $presentacionSeleccionada['cantidad_unidades'] == 1
                    || strtolower(trim($presentacionSeleccionada['tipo'])) === 'unidad'
                ) {
                    $id_padre = $presentacionSeleccionada['id_padre'];
                    if ($id_padre) {
                        $datosPadre = [];
                        if (isset($producto['precio_compra_m']) && $producto['precio_compra_m'] > 0) {
                            $datosPadre['precio_compra'] = $producto['precio_compra_m'];
                        }
                        if (isset($producto['precio_venta_m']) && $producto['precio_venta_m'] > 0) {
                            $datosPadre['precio_venta'] = $producto['precio_venta_m'];
                        }
                        if (!empty($datosPadre)) {
                            $this->presentaciones_model->update($id_padre, $datosPadre);
                        }
                    }
                } else {
                    $datosPres = [];
                    if (isset($producto['precio_compra_m']) && $producto['precio_compra_m'] > 0) {
                        $datosPres['precio_compra'] = $producto['precio_compra_m'];
                    }
                    if (isset($producto['precio_venta_m']) && $producto['precio_venta_m'] > 0) {
                        $datosPres['precio_venta'] = $producto['precio_venta_m'];
                    }
                    if (!empty($datosPres)) {
                        $this->presentaciones_model->update($id_presentacion, $datosPres);
                    }
                }
            }

            // Manejo de lotes con id_detalle_compra
            $loteExistente = $this->lote_productos
                ->where('id_producto', $id_producto)
                ->where('fecha_vencimiento', $producto['fecha_vence'])
                ->first();

            if ($loteExistente) {
                $nuevaCantidad = $loteExistente['cantidad'] + $producto['cantidad'];
                $this->lote_productos->update($loteExistente['id'], ['cantidad' => $nuevaCantidad]);
            } else {
                $this->lote_productos->save([
                    'id_producto' => $id_producto,
                    'id_detalle_compra' => $id_detalle_compra,
                    'fecha_vencimiento' => $producto['fecha_vence'],
                    'cantidad' => $producto['cantidad'],
                    'activo' => 1,
                    'movimiento' => 'COMPRA', // nuevo campo opcional para diferenciar compras, ventas, ajustes
                    'fecha_registro' => date('Y-m-d H:i:s')
                ]);
            }

            // Actualizar stock
            $this->productos->actualizaStock($id_producto, $producto['cantidad']);
        }

        // Limpiar la tabla temporal
        $this->temporal_compra->eliminarCompra($id_compra);
        $session->remove('id_compra_tmp');

        // Redirigir al PDF generado
        return redirect()->to(base_url() . "/compras/muestraCompraPdf/" . $resultadoId);
    }

    return redirect()->back()->with('error', 'Ocurrió un error al guardar la compra.');
}








 function muestraCompraPdf($id_compra){
    $data['id_compra'] = $id_compra;
    echo view('header');
    echo view('compras/ver_compra_pdf',$data);
    echo view('footer');


 }
public function generaCompraPdf($id_compra) {
    $datosCompra = $this->compras->where('id', $id_compra)->first();
    $detalleCompra = $this->detalle_compra->select('*')->where('id_compra', $id_compra)->findAll();
    $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;
    $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor;

    // Generar el PDF
    $pdf = new \FPDF('P', 'mm', 'letter');
    $pdf->AddPage();
    $pdf->SetMargins(5, 10, 5);
    $pdf->SetTitle("Compra");
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell(195, 5, "Entrada de Productos", 0, 1, 'C');
    $pdf->Image(base_url() . '/images/logotipo.png', 163, 12, 23, 20, 'PNG');
    $pdf->Cell(50, 5, $nombreTienda, 0, 1, 'L');
    $pdf->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 5, $direccionTienda, 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 5, utf8_decode('Fecha y Hora: '), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 5, $datosCompra['fecha_alta'], 0, 1, 'L');

    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(206, 5, 'Detalle de Productos', 1, 1, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(7, 5, 'No', 1, 0, 'C');
    $pdf->Cell(80, 5, 'Nombre Producto', 1, 0, 'C');
    $pdf->Cell(27, 5, 'Proveedor', 1, 0, 'C');
    $pdf->Cell(22, 5, 'Precio Compra', 1, 0, 'C');
    $pdf->Cell(22, 5, 'Cantidad Unid.', 1, 0, 'C');
    $pdf->Cell(23, 5, 'Cantidad Mayor', 1, 0, 'C');
    $pdf->Cell(25, 5, 'SubTotal', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 8);
    $contador = 1;

    foreach ($detalleCompra as $row) {
        // Calcular subtotal directamente
        $subtotal = $row['cantidad'] * $row['precio'];
        $cantidad_mayor = $row['cantidad_mayor'] ?? 0;

        // Obtener proveedor si existe
        $proveedor = $this->productos
            ->join('proveedores', 'proveedores.id = productos.id_proveedor')
            ->select('proveedores.nombre AS proveedor')
            ->where('productos.id', $row['id_producto'])
            ->get()
            ->getRow()
            ->proveedor ?? '';

        $pdf->Cell(7, 5, $contador, 1, 0, 'C');
        $pdf->Cell(80, 5, utf8_decode($row['nombre']), 1, 0, 'L');
        $pdf->Cell(27, 5, utf8_decode($proveedor), 1, 0, 'L');
        $pdf->Cell(22, 5, number_format($row['precio'], 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(22, 5, $row['cantidad'], 1, 0, 'C');
        $pdf->Cell(23, 5, $cantidad_mayor, 1, 0, 'C');
        $pdf->Cell(25, 5, 'Bs ' . number_format($subtotal, 2, '.', ','), 1, 1, 'R');
        $contador++;
    }

    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(190, 5, 'Total Bs ' . number_format($datosCompra['total'], 2, '.', ','), 0, 1, 'R');

    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output("compra_pdf.pdf", "I");
}


  public function stockActual()
  {

      // Obtener los movimientos de productos activos
      $movimientos = $this->detalle_compra->obtenerMovimientosActivos();
  
      // Preparar los datos para la vista
      $data = [
          'titulo' => 'Movimientos de Inventario',
          'movimientos' => $movimientos
      ];
  
      // Cargar las vistas
      echo view('header');
      echo view('compras/stock', $data);
      echo view('footer');
  }
  
  
  

///////////////////////////////////////////////////////////////////////////

  public function generarKardexPdf()
  {
      $codigo = $this->request->getPost('codigo');
      $fechaInicio = $this->request->getPost('fecha_inicio');
      $fechaFin = $this->request->getPost('fecha_fin');
  
      // Ajustar la fecha final para incluir todas las horas del día
      if ($fechaFin) {
          $fechaFin .= ' 23:59:59';  // Extender al último segundo del día
      }
  
      $conFechas = $this->request->getGet('conFechas');  // Verificar si es por rango
  
      // Consultar el producto por código
      $producto = $this->productos->where('codigo', $codigo)->where('activo', 1)->first();
  
      if (!$producto) {
          echo '<div style="text-align:center; margin-top: 50px;">
              <h2 style="color: #e74c3c;">Producto no encontrado</h2>
              <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                  Volver atrás
              </button>
          </div>';
          exit;
      }
  
      // Obtener el stock actual de la tabla `lotes_productos` (solo lotes activos)
      $stockActual = $this->lote_productos
          ->where('id_producto', $producto['id'])
          ->where('activo', 1)
          ->selectSum('cantidad')
          ->first()['cantidad'];
  
      // Obtener el Kardex según el rango de fechas
      if ($conFechas) {
          $kardex = $this->compras->obtenerKardexPorCodigoYRango($codigo, $fechaInicio, $fechaFin);
          $textoFechas = "Desde: $fechaInicio Hasta: " . substr($fechaFin, 0, 10);
      } else {
          $kardex = $this->compras->obtenerKardexPorCodigoYRango($codigo, '1900-01-01', date('Y-m-d 23:59:59'));
          $textoFechas = "Fecha de Generación: " . date('Y-m-d');
      }
  
      if (empty($kardex)) {
          echo '<div style="text-align:center; margin-top: 50px;">
              <h2 style="color: #e74c3c;">No hay movimientos registrados</h2>
              <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                  Volver atrás
              </button>
          </div>';
          exit;
      }
  
      // Obtener todos los lotes desechados y añadirlos como movimientos de desecho en el Kardex
      $lotesDesechados = $this->lote_productos
          ->where('id_producto', $producto['id'])
          ->where('movimiento', 'DESECHADO')
          ->where('activo', 0)
          ->select('fecha_registro AS fecha, "Desecho" AS tipo_movimiento, cantidad AS salida, 0 AS entrada')
          ->findAll();
  
      // Combinar los movimientos de compra/venta con los de desecho
      $kardex = array_merge($kardex, $lotesDesechados);
  
      // Ordenar el Kardex por fecha para que los movimientos estén en orden cronológico
      usort($kardex, function($a, $b) {
          return strtotime($a['fecha']) - strtotime($b['fecha']);
      });
  
      // Crear el PDF
      $pdf = new \FPDF('P', 'mm', 'letter');
      $pdf->AddPage();
      $pdf->SetMargins(10, 10, 10);
      $pdf->SetTitle("REPORTE KARDEX");
      $pdf->SetFont("Arial", 'B', 10);
      $pdf->Image("images/logotipo.png", 10, 5, 20);
      $pdf->Cell(0, 5, utf8_decode("REPORTE KARDEX"), 0, 1, 'C');
      $pdf->Ln(10);
  
      // Mostrar la información del producto, fecha y stock actual
      $pdf->SetFont('Arial', '', 10);
      $pdf->Cell(100, 5, "Producto: " . utf8_decode($producto['nombre']), 0, 0, 'L');
      $pdf->Cell(0, 5, utf8_decode($textoFechas), 0, 1, 'R');
      $pdf->Cell(0, 5, "Stock Actual: " . $stockActual, 0, 1, 'R');
      $pdf->Ln(3);
  
      // Encabezado de la tabla
      $pdf->SetFont('Arial', 'B', 8);
      $pdf->Cell(40, 5, 'Fecha', 1, 0, 'L');
      $pdf->Cell(60, 5, 'Producto', 1, 0, 'L');
      $pdf->Cell(30, 5, 'Movimiento', 1, 0, 'L');
      $pdf->Cell(20, 5, 'Entrada', 1, 0, 'L');
      $pdf->Cell(20, 5, 'Salida', 1, 0, 'L');
      $pdf->Cell(20, 5, 'Existencias', 1, 1, 'L');
  
      // Mostrar los registros del Kardex
      $existencias = 0; // Comenzar con existencias en 0 para reflejar la historia completa
      $pdf->SetFont('Arial', '', 8);
  
      foreach ($kardex as $row) {
          // Calcular existencias basándose en entradas y salidas
          $existencias += $row['entrada'] - $row['salida'];
          $pdf->Cell(40, 5, $row['fecha'], 1, 0, 'L');
          $pdf->Cell(60, 5, utf8_decode($producto['nombre']), 1, 0, 'L');
          $pdf->Cell(30, 5, utf8_decode($row['tipo_movimiento']), 1, 0, 'L');
          $pdf->Cell(20, 5, $row['entrada'], 1, 0, 'L');
          $pdf->Cell(20, 5, $row['salida'], 1, 0, 'L');
          $pdf->Cell(20, 5, $existencias, 1, 1, 'L');
      }
  
      // Generar el PDF
      $this->response->setHeader('Content-Type', 'application/pdf');
      $pdf->Output("kardex_pdf.pdf", "I");
  }
  





public function obtenerDatosGraficaProveedores() {
    $db = \Config\Database::connect();

    // Consulta para obtener los 10 proveedores con mayores compras
    $query = $db->query("
        SELECT proveedores.nombre AS proveedor, SUM(detalle_compra.cantidad * detalle_compra.precio) AS total_compras
        FROM compras
        JOIN detalle_compra ON detalle_compra.id_compra = compras.id
        JOIN productos ON productos.id = detalle_compra.id_producto
        JOIN proveedores ON proveedores.id = productos.id_proveedor
        WHERE compras.activo = 1
        GROUP BY proveedores.id
        ORDER BY total_compras DESC
        LIMIT 10;
    ");

    // Convertir los resultados en arrays para la gráfica
    $proveedores = [];
    $compras_totales = [];

    foreach ($query->getResultArray() as $row) {
        $proveedores[] = $row['proveedor'];
        $compras_totales[] = (float)$row['total_compras'];
    }

    // Asegúrate de que no haya salida adicional antes de enviar el JSON
    ob_clean();
    echo json_encode(['proveedores' => $proveedores, 'compras' => $compras_totales]);
    exit;
}



public function importarVista()
{
    echo view('header');
    echo view('compras/importar_excel');
    echo view('footer');
}


public function importarExcelMasivo()
    {
        $archivo = $this->request->getFile('archivo_excel');

        if ($archivo->isValid() && $archivo->getExtension() === 'xlsx') {
            $spreadsheet = IOFactory::load($archivo->getTempName());
            $datos = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $db = \Config\Database::connect();
            $folioYaInsertado = [];

            foreach ($datos as $i => $fila) {
                if ($i == 1) continue; // Saltar encabezado

                $folio   = trim($fila['A']);
                $usuario = trim($fila['B']);

                // Buscar usuario por nombre
                $usuarioData = $db->table('usuarios')->where('usuario', $usuario)->get()->getRow();
                if (!$usuarioData) continue;

                // Obtener lotes activos con cantidad
                $lotes = $db->table('lotes_productos')
                    ->where('cantidad >', 0)
                    ->where('activo', 1)
                    ->get()
                    ->getResult();

                $totalCompra = 0;
                $detalleDatos = [];

                foreach ($lotes as $lote) {
                    // Buscar producto
                    $producto = $db->table('productos')->where('id', $lote->id_producto)->get()->getRow();
                    if (!$producto || $producto->precio_compra == null) continue;

                    // Buscar presentación básica
                    $presentacion = $db->table('presentaciones_productos')
                        ->where('id_producto', $lote->id_producto)
                        ->orderBy('id', 'asc')
                        ->get()
                        ->getRow();

                    if (!$presentacion) continue;

                    $subtotal = $producto->precio_compra * $lote->cantidad;
                    $totalCompra += $subtotal;

                    $detalleDatos[] = [
                        'id_producto' => $lote->id_producto,
                        'id_presentacion' => $presentacion->id,
                        'nombre' => $producto->nombre,
                        'cantidad' => $lote->cantidad,
                        'precio' => $producto->precio_compra
                    ];
                }

                // Registrar la compra y sus detalles
                if ($totalCompra > 0) {
                    $db->table('compras')->insert([
                        'folio' => $folio,
                        'total' => $totalCompra,
                        'id_usuario' => $usuarioData->id
                    ]);
                    $id_compra = $db->insertID();

                    foreach ($detalleDatos as $detalle) {
                        $db->table('detalle_compra')->insert([
                            'id_compra' => $id_compra,
                            'id_producto' => $detalle['id_producto'],
                            'id_presentacion' => $detalle['id_presentacion'],
                            'nombre' => $detalle['nombre'],
                            'cantidad' => $detalle['cantidad'],
                            'precio' => $detalle['precio'],
                            'movimiento' => 'COMPRAS'
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Importación completada correctamente.');
        }

        return redirect()->back()->with('error', 'Archivo inválido o no válido.');
    }





}
?>