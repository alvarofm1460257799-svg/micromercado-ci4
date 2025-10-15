<?php

namespace App\Models;

use CodeIgniter\Model;

class LotesProductosModel extends Model
{
    protected $table = 'lotes_productos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'id_producto','id_detalle_compra', 'fecha_vencimiento', 'cantidad', 'activo', 'fecha_registro', 'movimiento'
    ];

    public function getProductosPorExpirar($fechaActual, $fechaLimite)
    {
        return $this->db->table('lotes_productos as lp')
                        ->select('p.codigo, p.nombre, lp.fecha_vencimiento, SUM(lp.cantidad) as cantidad_total')
                        ->join('productos p', 'lp.id_producto = p.id')
                        ->where('lp.fecha_vencimiento >=', $fechaActual)
                        ->where('lp.fecha_vencimiento <=', $fechaLimite)
                        ->where('lp.activo', 1) // Solo lotes activos
                        ->groupBy(['p.codigo', 'p.nombre', 'lp.fecha_vencimiento']) // Agrupar por código, nombre y fecha
                        ->having('cantidad_total >', 0) // Solo incluir productos con cantidad > 0
                        ->orderBy('lp.fecha_vencimiento', 'ASC')
                        ->get()
                        ->getResultArray();
    }
    
    

    public function obtenerPorCodigo($codigo)
    {
        $query = $this->db->query("
            SELECT p.id, p.nombre, p.precio_compra, p.precio_venta, 
                   SUM(lp.cantidad) AS cantidad_total, 
                   MIN(lp.fecha_vencimiento) AS fecha_vencimiento
            FROM productos p
            JOIN lotes_productos lp ON p.id = lp.id_producto
            WHERE p.codigo = ? AND p.activo = 1 AND lp.activo = 1
            GROUP BY p.id, p.nombre, p.precio_compra, p.precio_venta
            ORDER BY fecha_vencimiento ASC
            LIMIT 1;
        ", [$codigo]);
    
        $resultado = $query->getRowArray();
        log_message('info', 'Resultado de la consulta: ' . json_encode($resultado)); // Log para verificar
        return $resultado;
    }
    

    
    
    public function verificaPermisos($id_usuario, $permiso)
    {
        // Aquí podrías implementar la lógica para verificar los permisos
        // según tus reglas de negocio. Este es solo un ejemplo de retorno.
        if ($id_usuario == 1 && $permiso == 'Caja') {
            return true; // El usuario tiene permisos
        }
        return false; // No tiene permisos
    }

    public function obtenerLotesOrdenados($id_producto)
    {
        return $this->where('id_producto', $id_producto)
                    ->where('cantidad >', 0)
                    ->orderBy('fecha_vencimiento', 'ASC')
                    ->findAll();
    }


    public function guardarLote(array $loteData)
    {
        // Obtener el estado 'activo' del producto
        $productoModel = new \App\Models\ProductosModel();
        $producto = $productoModel->find($loteData['id_producto']);
    
        if ($producto) {
            $loteData['activo'] = $producto['activo']; // Sincronizar estado
        }
    
        return $this->save($loteData);
    }


    public function obtenerProductosConExistencias()
    {
        $builder = $this->db->table('productos p')
            ->select('p.id, p.codigo, p.nombre, p.precio_venta, p.precio_compra, 
                      p.activo, 
                      (SELECT SUM(lp.cantidad) 
                       FROM lotesProductos lp 
                       WHERE lp.id_producto = p.id 
                         AND lp.activo = 1) AS existencias')
            ->where('p.activo', 1); // Solo productos activos
    
        return $builder->get()->getResultArray();
    }
    


public function productosPorVencer() {
    return $this->db->table('lotes_productos as lp')
        ->select('p.codigo, p.nombre, lp.fecha_vencimiento, SUM(lp.cantidad) as cantidad_total')
        ->join('productos p', 'lp.id_producto = p.id')
        ->join('categorias c', 'p.id_categoria = c.id')
        ->where('lp.activo', 1)              // Solo lotes activos
        ->where('p.activo', 1)               // Solo productos activos
        ->where('c.activo', 1)               // Solo categorías activas
        ->where('lp.fecha_vencimiento IS NOT NULL')      // Excluir nulos
        ->where('lp.fecha_vencimiento !=', '0000-00-00') // Excluir fechas inválidas
        ->where('DATEDIFF(lp.fecha_vencimiento, CURDATE()) <= c.dias_aviso')  // Dentro del rango definido por dias_aviso
        ->where('DATEDIFF(lp.fecha_vencimiento, CURDATE()) >=', 0)            // Que aún no esté vencido
        ->groupBy(['p.codigo', 'p.nombre', 'lp.fecha_vencimiento'])
        ->having('cantidad_total >', 0)
        ->orderBy('lp.fecha_vencimiento', 'ASC')
        ->get()
        ->getResultArray();
}

public function productosVencidos() {
    $hoy = date('Y-m-d');

    return $this->db->table('lotes_productos as lp')
        ->select('p.codigo, p.nombre, lp.fecha_vencimiento, lp.cantidad')
        ->join('productos p', 'lp.id_producto = p.id')
        ->where('lp.fecha_vencimiento <', $hoy)
        ->where('lp.cantidad >', 0)           // Solo productos con cantidad mayor a 0
        ->where('lp.activo', 1)               // Solo lotes activos
        ->where('p.activo', 1)                // Solo productos activos
        ->where('lp.fecha_vencimiento IS NOT NULL')      // Excluir nulos
        ->where('lp.fecha_vencimiento !=', '0000-00-00') // Excluir fechas inválidas
        ->orderBy('lp.fecha_vencimiento', 'ASC')
        ->get()
        ->getResultArray();
}

    
  public function obtenerStockTotalPorProducto($id_producto)
    {
        $lotes = $this->where('id_producto', $id_producto)
                      ->where('cantidad >', 0)
                      ->where('activo', 1)
                      ->orderBy('fecha_vencimiento', 'ASC')
                      ->findAll();

        $stockTotalDisponible = array_sum(array_column($lotes, 'cantidad'));

        return $stockTotalDisponible;
    }
    
    
    

}
