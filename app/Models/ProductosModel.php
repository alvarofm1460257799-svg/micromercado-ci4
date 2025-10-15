<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductosModel extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'codigo', 'nombre', 'precio_venta', 'precio_compra',
        'existencias', 'stock_minimo', 'movimiento', 
        'id_proveedor', 'id_categoria', 'activo'
    ];

    //protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    //protected $dateFormat    = 'datetime';
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    //protected $cleanValidationRules = true;

    // Callbacks
    //protected $allowCallbacks = true;
    //protected $beforeInsert   = [];
    //protected $afterInsert    = [];
    //protected $beforeUpdate   = [];
    //protected $afterUpdate    = [];
    //protected $beforeFind     = [];
    //protected $afterFind      = [];
    //protected $beforeDelete   = [];
    //protected $afterDelete    = [];


//ojo puede parece borrarse
    public function obtenerProductosConVencidos()
{
    $fechaHoy = date('Y-m-d');

    $builder = $this->db->table('productos p')
        ->select('p.id, p.codigo, p.nombre, p.precio_venta, p.precio_compra, p.existencias, 
                  (SELECT SUM(lp.cantidad) 
                   FROM lotesProductos lp 
                   WHERE lp.id_producto = p.id 
                     AND lp.fecha_vencimiento <= "' . $fechaHoy . '" 
                     AND lp.activo = 1) AS productos_vencidos');

    return $builder->get()->getResultArray();
}


    public function actualizaStock($id_producto, $cantidad, $operador= '+'){
        $this->set('existencias', "existencias $operador $cantidad", FALSE);
        $this->where('id',$id_producto);
        $this->update();
    }

    public function totalProductos(){
        return $this->where('activo', 1)->countAllResults();
    }

   //CONSULTA PARA REPORTES

   public function productosMinimos() {
    // Devuelve los productos con existencias menores o iguales al stock mínimo y que estén activos
    return $this->where('existencias <= stock_minimo')
                ->where('activo', 1)
                ->findAll();
}

// Devuelve la cantidad total de productos con stock mínimo
public function contarProductosMinimos() {
    $builder = $this->db->table('productos');
    $builder->select('productos.id, productos.stock_minimo, SUM(lotes_productos.cantidad) AS total_cantidad')
            ->join('lotes_productos', 'productos.id = lotes_productos.id_producto', 'inner')
            ->where('productos.activo', 1) // Solo productos activos
            ->where('lotes_productos.activo', 1) // Solo lotes activos
            ->groupBy('productos.id')
            ->having('total_cantidad <= stock_minimo'); // Usar el alias total_cantidad

    return $builder->countAllResults(false); // Retorna el conteo de productos
}


public function getProductosMinimos() {
    // Construye una subconsulta para sumar las cantidades por id_producto desde lotes_productos
    $builder = $this->db->table('productos');
    $builder->select('productos.id, productos.codigo, productos.nombre, SUM(lotes_productos.cantidad) AS total_cantidad, productos.stock_minimo')
            ->join('lotes_productos', 'productos.id = lotes_productos.id_producto', 'inner')
            ->where('productos.activo', 1) // Solo productos activos
            ->where('lotes_productos.activo', 1) // Solo lotes activos
            ->groupBy('productos.id')
            ->having('total_cantidad <= productos.stock_minimo'); // Productos con stock mínimo o inferior

    return $builder->get()->getResultArray();
}


public function getProductosPorExpirar($fecha_actual, $fecha_limite) {
    $lotesProductosModel = new \App\Models\LotesProductosModel();

    // Llama a la consulta del modelo LotesProductosModel
    return $lotesProductosModel->getProductosPorExpirar($fecha_actual, $fecha_limite);
}



    public function getProductosMasVendidos() {
        $sql = "SELECT p.codigo, dv.id_producto, dv.nombre, SUM(dv.cantidad) AS total_vendido
                FROM detalle_venta dv
                JOIN productos p ON dv.id_producto = p.id
                JOIN ventas v ON dv.id_venta = v.id
                WHERE v.activo = ? 
                AND p.activo = ? 
                GROUP BY dv.id_producto, dv.nombre
                ORDER BY total_vendido DESC";
    
        // Usamos binding para proteger contra inyecciones SQL
        $query = $this->db->query($sql, [1, 1]);  // Ambos activos = 1
        return $query->getResult();
    }
    



    public function getMargenGanancia() {
        $sql = "SELECT p.codigo, dv.nombre, SUM((dv.precio - p.precio_compra) * dv.cantidad) AS margen_ganancia
                FROM detalle_venta dv
                JOIN productos p ON dv.id_producto = p.id
                JOIN ventas v ON dv.id_venta = v.id
                WHERE v.activo = ?
                GROUP BY dv.id_producto, dv.nombre
                ORDER BY margen_ganancia DESC";
    
        // Usamos binding de parámetros para evitar inyecciones SQL
        $query = $this->db->query($sql, [1]);  // Activo = 1
        return $query->getResult();
    }
    
    public function actualizarExistenciasDesdeLotes($id_producto)
{
    $loteModel = new \App\Models\LotesProductosModel();

    // Obtener la cantidad total desde los lotes activos
    $cantidadTotal = $loteModel->obtenerCantidadTotalPorProducto($id_producto);

    // Actualizar las existencias del producto
    $this->update($id_producto, ['existencias' => $cantidadTotal]);
}

    

}
?>
