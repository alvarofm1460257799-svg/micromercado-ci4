<?php

namespace App\Models;

use CodeIgniter\Model;

class ComprasModel extends Model
{
    protected $table      = 'compras';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio','total','id_usuario','activo'];

    //protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    //protected $dateFormat    = 'datetime';
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = '';

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
    public function insertaCompra($id_compra, $total, $id_usuario){
    $this->insert([
        'folio' => $id_compra,
        'total' => $total,
        'id_usuario' => $id_usuario
    ]);
     return $this->insertID();

     
}



public function generarFolio() {
    $query = "SELECT folio 
              FROM compras 
              WHERE folio LIKE 'NC-%' 
              ORDER BY CAST(SUBSTRING(folio, 4) AS UNSIGNED) DESC 
              LIMIT 1";

    $ultimoFolio = $this->db->query($query)->getRow();

    if (empty($ultimoFolio)) {
        // Si no hay registros previos, iniciar desde NC-7
        $nuevoNumero = 7;
    } else {
        // Extraer el número del último folio y calcular el siguiente
        $ultimoNumero = intval(substr($ultimoFolio->folio, 3));
        $nuevoNumero = max(7, $ultimoNumero + 1);
    }

    return 'NC-' . $nuevoNumero;
}

public function insertar($datos) {
    $datos['folio'] = $this->generarFolio(); // Generar folio automáticamente
    return $this->insert($datos); // Insertar en la base de datos
}

public function obtener($activo_proveedor = 1, $activo_compra = 1) {
    $this->select('com.id AS compra_id, prove.nombre AS proveedor, prove.cel_ref, com.folio, com.total, com.fecha_alta');
    $this->from('compras com');
    $this->join('detalle_compra de_com', 'de_com.id_compra = com.id');
    $this->join('productos pro', 'de_com.id_producto = pro.id');
    $this->join('proveedores prove', 'pro.id_proveedor = prove.id');
    $this->where('prove.activo', $activo_proveedor);
    $this->where('com.activo', $activo_compra);
    $this->groupBy('com.id');
    $this->orderBy('com.fecha_alta', 'DESC');

    return $this->findAll();
}




public function obtenerKardexPorCodigoYRango($codigo, $fechaInicio, $fechaFin) {
    // Entradas (Compras)
    $compras = $this->db->table('detalle_compra dc')
        ->select('c.fecha_alta AS fecha, p.nombre AS producto, dc.cantidad AS entrada, 0 AS salida, dc.cantidad AS existencias, "Compra" AS tipo_movimiento')
        ->join('compras c', 'c.id = dc.id_compra')
        ->join('productos p', 'dc.id_producto = p.id')
        ->where('p.codigo', $codigo)
        ->where('c.fecha_alta >=', $fechaInicio)
        ->where('c.fecha_alta <=', $fechaFin)
        ->where('c.activo', 1)  // Solo compras activas
        ->where('p.activo', 1);  // Solo productos activos

    // Salidas (Ventas)
    $ventas = $this->db->table('detalle_venta dv')
        ->select('v.fecha_alta AS fecha, p.nombre AS producto, 0 AS entrada, dv.cantidad AS salida, dv.cantidad AS existencias, "Venta" AS tipo_movimiento')
        ->join('ventas v', 'v.id = dv.id_venta')
        ->join('productos p', 'dv.id_producto = p.id')
        ->where('p.codigo', $codigo)
        ->where('v.fecha_alta >=', $fechaInicio)
        ->where('v.fecha_alta <=', $fechaFin)
        ->where('v.activo', 1)  // Solo ventas activas
        ->where('p.activo', 1);  // Solo productos activos

    // Combinar entradas y salidas con UNION
    $query = $compras->union($ventas)->orderBy('fecha', 'ASC')->get();

    // Obtener los resultados
    $resultados = $query->getResultArray();

    return $resultados;
}



public function obtenerComprasAgrupadas($fecha_inicio, $fecha_fin, $activo = 1) {
    $this->select(
        'DATE(compras.fecha_alta) AS fecha, proveedores.nombre AS proveedor, ' .
        'detalle_compra.nombre AS producto, detalle_compra.id_producto, ' .  // <-- Agregado id_producto
        'SUM(detalle_compra.cantidad) AS cantidad_total, ' .
        'SUM(detalle_compra.cantidad * detalle_compra.precio) AS total_producto'
    );
    $this->join('detalle_compra', 'detalle_compra.id_compra = compras.id');
    $this->join('productos', 'productos.id = detalle_compra.id_producto');
    $this->join('proveedores', 'proveedores.id = productos.id_proveedor');
    $this->where('compras.activo', $activo);
    $this->where('DATE(compras.fecha_alta) >=', $fecha_inicio);
    $this->where('DATE(compras.fecha_alta) <=', $fecha_fin);
    $this->groupBy(['fecha', 'proveedores.id', 'detalle_compra.nombre', 'detalle_compra.id_producto']);  // <-- Añadido id_producto al groupBy
    $this->orderBy('fecha', 'DESC');

    return $this->findAll();
}







    
}

?>