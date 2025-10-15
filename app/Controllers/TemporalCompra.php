<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TemporalCompraModel;
use App\Models\ProductosModel;
use App\Models\LotesProductosModel;
use App\Models\PresentacionesModel;

class TemporalCompra extends BaseController
{
    protected $temporal_compra, $productos ;
    protected $LoteProductosModel,$PresentacionesModel;

    public function __construct()
    {
        $this->temporal_compra = new TemporalCompraModel();
        $this->productos    = new ProductosModel();
        $this->LoteProductosModel    = new LotesProductosModel();
        $this->PresentacionesModel    = new PresentacionesModel();
       
    }

public function inserta($id_presentacion, $cantidad, $id_compra) 
{
    try {
        $postData = $this->request->getPost();

        $id_producto = $postData['id_producto'] ?? null;
        if (!$id_producto) {
            throw new \Exception("No se recibió id_producto");
        }

        $fecha_vencimiento = $postData['fecha_vencimiento'] ?? null;
        $precio_compra = floatval($postData['precio_compra'] ?? 0);
        $precio_compra_m = floatval($postData['precio_compra_m'] ?? 0);
        $precio_venta = floatval($postData['precio_venta'] ?? 0);
        $precio_venta_m = floatval($postData['precio_venta_m'] ?? 0);
        $cantidad_unidades = intval($postData['cant_u'] ?? $cantidad);
        $cantidad_mayor = floatval($postData['cantidad_mayor'] ?? 0);

        // 🔹 Obtener la presentación escaneada
        $presentacionEscaneada = $this->PresentacionesModel->find($id_presentacion);
        if (!$presentacionEscaneada) {
            throw new \Exception("Presentación escaneada no encontrada");
        }

        // 🔹 Buscar la presentación base del producto (cantidad_unidades = 1)
        $presentacionBase = $this->PresentacionesModel
                                 ->where('id_producto', $id_producto)
                                 ->where('cantidad_unidades', 1)
                                 ->first();

        if (!$presentacionBase) {
            // Si no hay base, usar la presentación escaneada
            $presentacionBase = $presentacionEscaneada;
        }

        // 🔹 Nombre del producto
        $producto = $this->productos->find($id_producto);
        $nombreProducto = $producto['nombre'] ?? 'Producto';
        $tipoProducto = $presentacionBase['tipo'] ?? '';

        // 🔹 Calcular subtotal
        $subtotal = $cantidad_unidades * $precio_compra;

        // 🔹 Preparar datos para temporal_compra
        $saveData = [
            'folio' => $id_compra,
            'id_producto' => $id_producto,
            'id_presentacion' => $presentacionBase['id'],
            'codigo' => $presentacionBase['codigo'],
            'nombre' => trim($nombreProducto . ' (' . $tipoProducto . ')'),
            'cantidad' => $cantidad_unidades,
            'cantidad_mayor' => $cantidad_mayor,
            'precio_compra' => $precio_compra,
            'precio_venta' => $precio_venta,
            'precio_compra_m' => $precio_compra_m,
            'precio_venta_m' => $precio_venta_m,
            'subtotal' => $subtotal,
            'fecha_vence' => $fecha_vencimiento,
            'id_lote' => null,
        ];

        $this->temporal_compra->save($saveData);

        $res['datos'] = $this->cargaProductos($id_compra);
        $res['total'] = number_format($this->totalProductos($id_compra), 2, '.', ',');
        $res['error'] = '';

        echo json_encode($res);

    } catch (\Throwable $e) {
        echo json_encode(['error' => 'Error al insertar producto: ' . $e->getMessage()]);
    }
}

    
    
    
    
public function cargaProductos($id_compra) {
    $query = $this->temporal_compra->query("
        SELECT id, id_producto, codigo, nombre, cantidad, cantidad_mayor, precio_compra, precio_venta,
               precio_compra_m, precio_venta_m, subtotal, fecha_vence
        FROM temporal_compra
        WHERE folio = ?
    ", [$id_compra]);

    $resultados = $query->getResultArray();

    $fila = '';
    $numero = 1;
    foreach ($resultados as $row) {
        $subtotal_formato = number_format($row['subtotal'], 2, '.', ',');

        // Formato HTML para precios
        $precios_html = "<div><strong>Compra:</strong> Bs " . number_format($row['precio_compra'], 2) . "</div>";
        $precios_html .= "<div><strong>Venta:</strong> Bs " . number_format($row['precio_venta'], 2) . "</div>";
        $precios_html .= "<div style='font-size: 0.9em; color: #555;'><em>Compra x Mayor:</em> Bs " . number_format($row['precio_compra_m'], 2) . "</div>";
        $precios_html .= "<div style='font-size: 0.9em; color: #555;'><em>Venta x Mayor:</em> Bs " . number_format($row['precio_venta_m'], 2) . "</div>";

        // 🔹 Mostrar cantidad mayor solo si es mayor a 0
        $cantidad_mayor_display = ($row['cantidad_mayor'] ?? 0) > 0 ? number_format($row['cantidad_mayor'], 2, '.', ',') : '';

        $fila .= "<tr>";
        $fila .= "<td>{$numero}</td>";
        $fila .= "<td>{$row['codigo']}</td>";
        $fila .= "<td>{$row['nombre']}</td>";
        $fila .= "<td>{$precios_html}</td>"; // Columna de precios detallados
        $fila .= "<td>{$cantidad_mayor_display}</td>"; // Cantidad Mayor
        $fila .= "<td>{$row['cantidad']}</td>";         // Cantidad unidad
        $fila .= "<td>{$subtotal_formato}</td>";
        $fila .= "<td>{$row['fecha_vence']}</td>";
        $fila .= "<td>
                    <button class='btn btn-danger btn-sm btn-eliminar' type='button' onclick='eliminaProductoUno({$row['id']}, \"{$id_compra}\")'>
                        <i class='fas fa-trash-alt'></i>
                    </button>
                  </td>";
        $fila .= "</tr>";

        $numero++;
    }
    return $fila;
}



    
  public function totalProductos($id_compra) {
    $resultado = $this->temporal_compra->where('folio', $id_compra)->findAll();
    $total = 0;

    foreach ($resultado as $row) {
        $total += $row['subtotal'];
    }

    return $total;
}

    
public function eliminarUno($id, $id_compra) {
    $producto = $this->temporal_compra->find($id);

    if ($producto) {
        if ($producto['cantidad'] > 1) {
            $nuevaCantidad = $producto['cantidad'] - 1;
            $nuevoSubtotal = $nuevaCantidad * $producto['precio_compra']; // Usamos el precio de compra

            // Actualizar solo la cantidad y el subtotal
            $this->temporal_compra->update($id, [
                'cantidad' => $nuevaCantidad,
                'subtotal' => $nuevoSubtotal
            ]);
        } else {
            // Si la cantidad es 1, eliminamos el registro
            $this->temporal_compra->delete($id);
        }
    } else {
        echo json_encode([
            'error' => 'No se encontró el producto para eliminar.'
        ]);
        return;
    }

    // Devolver la tabla actualizada y el nuevo total
    $res['datos'] = $this->cargaProductos($id_compra);
    $res['total'] = number_format($this->totalProductos($id_compra), 2, '.', ',');
    $res['error'] = '';

    echo json_encode($res);
}
public function buscarPorCodigo($codigo)
{
    try {
        // ----------------------------
        // 1️⃣ Buscar en presentaciones (coincidencia exacta con código de barra de presentación)
        // ----------------------------
        $presentacion = $this->PresentacionesModel
            ->select('id, codigo, tipo, precio_compra, precio_venta, cantidad_unidades, id_producto')
            ->where('activo', 1)
            ->where('codigo', $codigo)
            ->first();

        // ----------------------------
        // 2️⃣ Si no encontró en presentaciones, buscar en variantes
        // ----------------------------
        if (!$presentacion) {
            $variante = $this->temporal_compra->db->table('variantes_producto v')
                ->select('v.id, v.codigo_barra, v.descripcion, v.id_producto')
                ->where('v.activo', 1)
                ->where('v.codigo_barra', $codigo)
                ->get()
                ->getRowArray();

            if ($variante) {
                // Buscar la presentación base del producto (unidad mínima)
                $presentacionBase = $this->PresentacionesModel
                    ->select('id, tipo, precio_compra, precio_venta, cantidad_unidades')
                    ->where('id_producto', $variante['id_producto'])
                    ->orderBy('cantidad_unidades', 'ASC') // la más pequeña
                    ->first();

                if (!$presentacionBase) {
                    return $this->response->setJSON([
                        'existe' => false,
                        'error'  => 'No se encontró ninguna presentación válida para este producto.'
                    ]);
                }

                // Construimos como si fuera una presentación
                $presentacion = [
                    'id'               => $presentacionBase['id'],   // 👈 usamos id de presentacion
                    'codigo'           => $variante['codigo_barra'], // se mantiene el código escaneado
                    'descripcion'      => $variante['descripcion'],  // descripción de la variante
                    'id_producto'      => $variante['id_producto'],
                    'tipo'             => $presentacionBase['tipo'],
                    'precio_compra'    => $presentacionBase['precio_compra'],
                    'precio_venta'     => $presentacionBase['precio_venta'],
                    'cantidad_unidades'=> $presentacionBase['cantidad_unidades']
                ];
            }
        }

        // ----------------------------
        // 3️⃣ Si no encontró nada, devolver error
        // ----------------------------
        if (!$presentacion) {
            return $this->response->setJSON([
                'existe' => false,
                'error'  => 'No se encontró ninguna presentación o variante con ese código.'
            ]);
        }

        // ----------------------------
        // 4️⃣ Calcular stock disponible
        // ----------------------------
        $stockReal = $this->LoteProductosModel->obtenerStockTotalPorProducto($presentacion['id_producto']);
        $idCompra  = $this->request->getPost('id_compra') ?? 0;

        $stockTemporal = $this->temporal_compra
            ->where('id_producto', $presentacion['id_producto'])
            ->where('folio', $idCompra)
            ->selectSum('cantidad')
            ->first();

        $cantidadEnTemporal = $stockTemporal['cantidad'] ?? 0;
        $stockDisponible    = max(0, $stockReal - $cantidadEnTemporal);

        // ----------------------------
        // 5️⃣ Determinar tipo de presentación
        // ----------------------------
        $tipo = $presentacion['tipo'] ?? 'unidad';

        // ----------------------------
        // 6️⃣ Nombre y descripción del producto
        // ----------------------------
        $producto       = $this->productos->find($presentacion['id_producto']);
        $nombreProducto = $producto['nombre'] ?? '';
        $descripcion    = $presentacion['descripcion'] ?? null;

        // ----------------------------
        // 7️⃣ Respuesta final
        // ----------------------------
        return $this->response->setJSON([
            'existe' => true,
            'datos'  => [
                'id'            => $presentacion['id'],  // id_presentacion válido
                'id_producto'   => $presentacion['id_producto'],
                'codigo'        => $presentacion['codigo'],
                'nombre'        => $nombreProducto,
                'descripcion'   => $descripcion,
                'tipo'          => $tipo,
                'precio_compra' => $presentacion['precio_compra'] ?? null,
                'precio_venta'  => $presentacion['precio_venta'] ?? null,
                'cantidad_total'=> $stockDisponible
            ]
        ]);

    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'existe' => false,
            'error'  => 'Error al buscar el producto: ' . $e->getMessage()
        ]);
    }
}


    
    public function cargarProductos($id_compra)
{
    $res['datos'] = $this->cargaProductos($id_compra);
    $res['total'] = number_format($this->totalProductos($id_compra), 2, '.', ',');
    echo json_encode($res);
}

public function eliminarTodo($folio) {
    $this->temporal_compra->eliminarCompra($folio);

    $res['datos'] = '';
    $res['total'] = '0.00';
    $res['error'] = '';
    
    echo json_encode($res);
}

public function autocompletarPresentaciones()
{
    try {
        $term = $this->request->getGet('term');
        $db = \Config\Database::connect();

        $builder = $db->table('presentaciones_productos p');
        $builder->select('
            p.id, 
            p.codigo, 
            p.tipo, 
            p.precio_compra, 
            p.precio_venta, 
            pr.id as id_producto, 
            pr.nombre as nombre_producto
        ');
        $builder->join('productos pr', 'p.id_producto = pr.id');
        $builder->where('p.activo', 1);
        $builder->groupStart()
            ->like('pr.nombre', $term)
            ->orLike('p.tipo', $term)
            ->orLike('p.codigo', $term)
            ->groupEnd();

        $presentaciones = $builder->get()->getResult();

        // ✅ Cargar modelo para calcular stock real desde lotes_productos
        $LotesModel = new \App\Models\LotesProductosModel();
        $datos = [];

        foreach ($presentaciones as $row) {
            $stockReal = $LotesModel->obtenerStockTotalPorProducto($row->id_producto); // suma de lotes activos
            $datos[] = [
                'id' => $row->id,
                'id_producto' => $row->id_producto,
                'nombre_producto' => $row->nombre_producto,
                'tipo' => $row->tipo,
                'codigo' => $row->codigo,
                'precio_compra' => $row->precio_compra,
                'precio_venta' => $row->precio_venta,
                'existencias' => $stockReal
            ];
        }

        return $this->response->setJSON($datos);

    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'error' => true,
            'mensaje' => $e->getMessage()
        ]);
    }
}



public function jerarquiaUnidades($id_presentacion)
{
    $db = \Config\Database::connect();

    try {
        // Obtener presentación actual
        $presentacion = $db->table('presentaciones_productos')
            ->select('id, tipo, cantidad_unidades, id_producto, id_padre')
            ->where('id', $id_presentacion)
            ->get()
            ->getRow();

        if (!$presentacion) {
            return $this->response->setJSON(['success' => false]);
        }

        $id_producto = $presentacion->id_producto;

        // 🔺 SUBIR a padres
        $ascendentes = [];
        $padre = $presentacion;
        while ($padre && $padre->id_padre) {
            $padre = $db->table('presentaciones_productos')
                ->select('id, tipo, cantidad_unidades, id_padre')
                ->where('id', $padre->id_padre)
                ->get()
                ->getRow();

            if ($padre) {
                $ascendentes[] = [
                    'id' => $padre->id,
                    'tipo' => $padre->tipo,
                    'cantidad_unidades' => $padre->cantidad_unidades
                ];
            }
        }

        // 🔻 BAJAR a hijos
        $descendentes = [];
        $actual = $presentacion;
        while ($actual) {
            $descendentes[] = [
                'id' => $actual->id,
                'tipo' => $actual->tipo,
                'cantidad_unidades' => $actual->cantidad_unidades
            ];

            $hijo = $db->table('presentaciones_productos')
                ->select('id, tipo, cantidad_unidades')
                ->where('id_padre', $actual->id)
                ->where('id_producto', $id_producto)
                ->get()
                ->getRow();

            $actual = $hijo;
        }

        // Resultado combinado: de mayor a menor
        $jerarquia = array_reverse($ascendentes);
        $jerarquia[] = [
            'id' => $presentacion->id,
            'tipo' => $presentacion->tipo,
            'cantidad_unidades' => $presentacion->cantidad_unidades
        ];
        $jerarquia = array_merge($jerarquia, array_slice($descendentes, 1));

        return $this->response->setJSON([
            'success' => true,
            'jerarquia' => $jerarquia
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}




  
}
?>