<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TemporalVentaModel;
use App\Models\ProductosModel;
use App\Models\PresentacionesModel;

class TemporalVenta extends BaseController
{
    protected $temporal_venta, $productos, $presentaciones_model ;
  

    public function __construct()
    {
        $this->temporal_venta = new TemporalVentaModel();
        $this->productos    = new ProductosModel();
        $this->presentaciones_model    = new PresentacionesModel();
       
    }



// Esta es la función interna que retorna array limpio
public function obtenerProductosArray($id_venta)
{
    $productos = $this->temporal_venta->porVenta($id_venta);

    $total = 0;
    $datosHTML = '';
    $contador = 1;

    foreach ($productos as $producto) {
        $totalProducto = $producto['cantidad'] * $producto['precio'];
        $total += $totalProducto;

        $datosHTML .= '<tr>';
        $datosHTML .= '<td>' . $contador++ . '</td>';
        $datosHTML .= '<td>' . htmlspecialchars($producto['codigo']) . '</td>';
        $datosHTML .= '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
        $datosHTML .= '<td>' . number_format($producto['precio'], 2) . '</td>';
        $datosHTML .= '<td>' . $producto['cantidad'] . '</td>';
         $datosHTML .= '<td>' . $producto['cantidad_mayor'] . '</td>';
        $datosHTML .= '<td>' . number_format($totalProducto, 2) . '</td>';
   $datosHTML .= '<td><button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="' . $producto['id'] . '" data-folio="' . addslashes($producto['folio']) . '"><i class="fas fa-trash"></i></button></td>';

        $datosHTML .= '</tr>';
    }

    return [
        'datos' => $datosHTML,
        'total' => number_format($total, 2, '.', ',')
    ];
}



// Esta sigue siendo la pública que retorna JSON cuando se accede directamente desde AJAX
public function obtenerProductos($id_venta)
{
    $res = $this->obtenerProductosArray($id_venta);
    return $this->response->setJSON($res);
}




public function eliminar($id_temporal, $folio)
{
    try {
        log_message('debug', "Eliminar producto temporal id: $id_temporal, folio: $folio");
        $registro = $this->temporal_venta->find($id_temporal);

        if (!$registro) {
            log_message('error', "Producto no encontrado para id_temporal: $id_temporal");
            return $this->response->setJSON(['error' => 'Producto no encontrado']);
        }

        $presentaciones = new \App\Models\PresentacionesModel();

        if ($registro['cantidad'] > 1) {
            // Restar una unidad
            $nuevaCantidad = $registro['cantidad'] - 1;

            // --- Recalcular cantidad_mayor según la jerarquía ---
            $presentacion = $presentaciones->find($registro['id_presentacion']);
            if ($presentacion) {
                if ($presentacion['cantidad_unidades'] == 1 && $presentacion['id_padre'] != null) {
                    $padre = $presentaciones->find($presentacion['id_padre']);
                    $nuevaCantidadMayor = $padre ? $nuevaCantidad / $padre['cantidad_unidades'] : $nuevaCantidad;
                } else {
                    $factor = $presentaciones->calcularFactorDesdeUnidadHasta($registro['id_presentacion']);
                    $nuevaCantidadMayor = $factor > 0 ? $nuevaCantidad / $factor : $nuevaCantidad;
                }
            } else {
                $nuevaCantidadMayor = $nuevaCantidad;
            }

            $nuevoSubtotal = $nuevaCantidadMayor * $registro['precio']; // subtotal según cantidad_mayor

            $this->temporal_venta->update($id_temporal, [
                'cantidad'       => $nuevaCantidad,
                'cantidad_mayor' => $nuevaCantidadMayor,
                'subtotal'       => $nuevoSubtotal
            ]);
        } else {
            $this->temporal_venta->delete($id_temporal);
        }

        $res = $this->obtenerProductosArray($folio);
        $res['error'] = '';
        return $this->response->setJSON($res);

    } catch (\Exception $e) {
        log_message('error', "Error al eliminar: " . $e->getMessage());
        return $this->response->setJSON([
            'error' => 'Error al eliminar: ' . $e->getMessage()
        ]);
    }
}








public function totalProductos ($id_venta){
    $resultado = $this->temporal_venta->porVenta($id_venta);
  
    $total = 0;

    foreach( $resultado as $row){
        $total += $row['subtotal'];
    }
    return $total;


}

public function buscarPorCodigoPresentacion()
{
    $codigo = $this->request->getPost('codigo');
    $cantidad = (int) $this->request->getPost('cantidad');
    $folio = $this->request->getPost('id_venta');
    $permitirSinStock = $this->request->getPost('permitir_sin_stock');

    $presentaciones = new \App\Models\PresentacionesModel();
    $temporal = new \App\Models\TemporalVentaModel();
    $lotesModel = new \App\Models\LotesProductosModel();
    $detalleCompraModel = new \App\Models\DetalleCompraModel();
    $productosModel = new \App\Models\ProductosModel();

    if (!$folio) {
        return $this->response->setJSON(['error' => 'Folio de venta temporal no proporcionado']);
    }

    // -------------------------
    // 1️⃣ Buscar presentación exacta
    // -------------------------
    $presentacion = $presentaciones->obtenerPresentacionConNombreProductoPorCodigo($codigo);
    $codigoEscaneado = $codigo; // 👈 siempre guardamos el código real que escaneó el usuario
    $descripcionVariante = null;

    // -------------------------
    // 2️⃣ Si no encontró, buscar variante
    // -------------------------
    if (!$presentacion) {
        $db = \Config\Database::connect();
        $variante = $db->table('variantes_producto v')
                       ->select('v.id, v.id_producto, v.codigo_barra, v.descripcion')
                       ->where('v.activo', 1)
                       ->where('v.codigo_barra', $codigo)
                       ->get()
                       ->getRowArray();

        if ($variante) {
            // Guardamos datos de la variante
            $codigoEscaneado = $variante['codigo_barra'];
            $descripcionVariante = $variante['descripcion'];

            // Buscar presentación base (mínima)
            $presentacionBase = $presentaciones->where('id_producto', $variante['id_producto'])
                                               ->orderBy('cantidad_unidades', 'ASC')
                                               ->first();

            if ($presentacionBase) {
                $presentacion = $presentacionBase;
                $presentacion['id_producto'] = $variante['id_producto'];
            }
        }
    }

    if (!$presentacion) {
        return $this->response->setJSON(['error' => 'Código no encontrado']);
    }

    // -------------------------
    // 3️⃣ Nombre del producto
    // -------------------------
    $producto = $productosModel->find($presentacion['id_producto']);
    $nombreProducto = $producto['nombre'] ?? '';

    // -------------------------
    // 4️⃣ Calcular stock
    // -------------------------
    $lotes = $lotesModel->where('id_producto', $presentacion['id_producto'])
                        ->where('cantidad >', 0)
                        ->where('activo', 1)
                        ->orderBy('fecha_vencimiento', 'ASC')
                        ->findAll();

    $stockDisponibleUnidades = array_sum(array_column($lotes, 'cantidad'));
    $primerLote = $lotes[0] ?? null;
    $id_lote_real = $primerLote['id'] ?? ($permitirSinStock === 'on' ? 0 : null);

    $precio_compra_real = 0;
    if ($primerLote && isset($primerLote['id_detalle_compra'])) {
        $detalleCompra = $detalleCompraModel->find($primerLote['id_detalle_compra']);
        if ($detalleCompra) {
            $precio_compra_real = $detalleCompra['precio'];
        }
    }

    // -------------------------
    // 5️⃣ Factor y cantidades
    // -------------------------
    $factor = $presentaciones->calcularFactorDesdeUnidadHasta($presentacion['id']);
    $cantidadEnUnidades = $cantidad * $factor;

    // Cantidad mayor (ejemplo: cajas, paquetes, etc.)
    $cantidadMayor = $cantidad;
    if ($presentacion['id_padre']) {
        $padre = $presentaciones->find($presentacion['id_padre']);
        $cantidadMayor = $padre ? $cantidad / $padre['cantidad_unidades'] : $cantidad;
    }

    // -------------------------
    // 6️⃣ Validar stock
    // -------------------------
    $totalUnidadesProducto = $temporal
        ->select('SUM(cantidad) AS total')
        ->where('id_producto', $presentacion['id_producto'])
        ->where('folio', $folio)
        ->first();

    $cantidadTotalUnidades = ($totalUnidadesProducto['total'] ?? 0) + $cantidadEnUnidades;

    if ($permitirSinStock !== 'on' && $cantidadTotalUnidades > $stockDisponibleUnidades) {
        return $this->response->setJSON([
            'error' => 'Stock insuficiente: se requieren ' . $cantidadTotalUnidades .
                       ' unidades simples para agregar esta presentación, pero solo hay ' .
                       $stockDisponibleUnidades
        ]);
    }

    // -------------------------
    // 7️⃣ Registrar en temporal
    // -------------------------
    $idPresentacion = $presentacion['id'];

   try {
    // -------------------------
    // 6️⃣ Registrar en temporal
    // -------------------------

    // Asegurarse de que $presentacion o $presentacionBase existan
    if (!isset($presentacion) && !isset($presentacionBase)) {
        throw new \Exception('No se encontró presentación ni presentación base.');
    }

    // Determinar presentación a usar
    $presentacionActual = $presentacion ?? $presentacionBase;

    // Determinar id_producto seguro
    $id_producto = $presentacionActual['id_producto'] ?? null;
    if (!$id_producto) {
        throw new \Exception('No se pudo determinar el id del producto.');
    }

    // Buscar dato existente en temporal
    $datoExistente = $temporal->where([
        'folio' => $folio,
        'id_presentacion' => $presentacionActual['id']
    ])->first();

    // Calcular cantidades totales
    $cantidadTotalMayor = ($datoExistente['cantidad_mayor'] ?? 0) + $cantidadMayor;
    $cantidadTotalUnidades = ($datoExistente['cantidad'] ?? 0) + $cantidadEnUnidades;

    // Calcular subtotal según tipo de presentación
    $cantidadUnidadesPres = $presentacionActual['cantidad_unidades'] ?? 1;
    $precioVenta = $presentacionActual['precio_venta'] ?? 0;

    if ($cantidadUnidadesPres == 1) {
        $subtotal = $precioVenta * $cantidadTotalUnidades;
    } else {
        $subtotal = $precioVenta * $cantidadTotalMayor;
    }

    // Obtener nombre del producto base
    $producto = $this->productos->find($id_producto);
    $nombre_producto = $producto['nombre'] ?? 'Nombre desconocido';

    // Construir nombre completo
    $nombre_completo = $nombre_producto .
                       (isset($presentacionActual['descripcion']) ? ' - ' . $presentacionActual['descripcion'] : '') .
                       ' [' . ($presentacionActual['tipo'] ?? 'Unidad') . ']';

    $idPresentacion = $presentacionActual['id'];

    // Actualizar o insertar en temporal
    if ($datoExistente) {
        $temporal->update($datoExistente['id'], [
            'cantidad_mayor' => $cantidadTotalMayor,
            'cantidad'       => $cantidadTotalUnidades,
            'subtotal'       => $subtotal,
            'id_lote'        => $id_lote_real,
            'precio_compra'  => $precio_compra_real
        ]);
    } else {
        $temporal->insert([
            'folio'           => $folio,
            'id_producto'     => $id_producto,
            'id_presentacion' => $idPresentacion,
            'id_lote'         => $id_lote_real,
            'codigo'          => $presentacionActual['codigo'] ?? '',
            'nombre'          => $nombre_completo,
            'cantidad_mayor'  => $cantidadMayor,
            'cantidad'        => $cantidadEnUnidades,
            'precio'          => $precioVenta,
            'precio_compra'   => $precio_compra_real,
            'subtotal'        => $subtotal
        ]);
    }

} catch (\Throwable $e) {
    return $this->response->setJSON([
        'error'   => 'Excepción: ' . $e->getMessage(),
        'linea'   => $e->getLine(),
        'archivo' => $e->getFile()
    ]);
}




    // -------------------------
    // 8️⃣ Mostrar tabla
    // -------------------------
    $productos = $temporal->where('folio', $folio)->findAll();
    $total = 0;
    $filas = "";
    foreach ($productos as $index => $prod) {
        $total += $prod['subtotal'];
        $filas .= "<tr>
            <td>" . ($index + 1) . "</td>
            <td>{$prod['codigo']}</td>
            <td>{$prod['nombre']}</td>
            <td>" . number_format($prod['precio'], 2) . "</td>
            <td>{$prod['cantidad']}</td>
            <td>{$prod['cantidad_mayor']}</td>
            <td>" . number_format($prod['subtotal'], 2) . "</td>
            <td><button class='btn btn-danger btn-sm' onclick='eliminaProducto({$prod['id']}, \"{$folio}\")'><i class='fas fa-trash'></i></button></td>
        </tr>";
    }

    return $this->response->setJSON([
        'error' => '',
        'datos' => $filas,
        'total' => number_format($total, 2, '.', '')
    ]);
}




public function eliminarTodo($folio)
{
    $this->temporal_venta->where('folio', $folio)->delete();

    $res['datos'] = '';
    $res['total'] = '0.00';
    $res['error'] = '';

    echo json_encode($res);
}


}
?>