<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasModel extends Model
{
    protected $table      = 'ventas';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio','total','id_usuario','id_caja','id_cliente','forma_pago','activo'];

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

public function insertaVenta($id_venta, $total, $id_usuario, $id_caja, $id_cliente, $forma_pago){
    $this ->insert([
        'folio' => $id_venta,
        'total' => $total,
        'id_usuario' => $id_usuario,
        'id_caja' => $id_caja,
        'id_cliente' => $id_cliente,
        'forma_pago' => $forma_pago
        
    ]);
    return $this->insertID();
}

public function obtener($activo =1){
    $this->select('ventas.*, u.usuario As cajero, c.nombre AS cliente');
    $this->join('usuarios AS u','ventas.id_usuario = u.id');
    $this->join('clientes AS c','ventas.id_cliente = c.id');
    $this->where('ventas.activo',$activo);
    $this->orderBy('ventas.fecha_alta','DESC');
    $datos =$this->findAll();

   // print_r($this->getLastQuery()); //sirve para hacer pruevas de transacciones
    return $datos;

     
}

public function obtenerUltimoFolio() {
    $query = $this->db->query("
        SELECT folio 
        FROM ventas 
        WHERE folio LIKE 'NV-%' 
        ORDER BY CAST(REGEXP_REPLACE(folio, '[^0-9]', '') AS UNSIGNED) DESC 
        LIMIT 1
    ");
    return $query->getRow();
}



 public function totalDia($fecha){
    $this->select("sum(total) AS total");
    $where= "activo = 1 AND DATE(fecha_alta)='$fecha'";
    return $this->where($where)->first();

 }
 public function CantidadVentasDia($fecha) {
    $this->select("COUNT(*) AS cantidad_ventas");
    $where = "activo = 1 AND DATE(fecha_alta) = '$fecha'";
    return $this->where($where)->first();
}



public function obtenerVentasAgrupadas($fecha_inicio, $fecha_fin, $activo = 1) {
    $this->select(
        'DATE(ventas.fecha_alta) AS fecha, clientes.nombre AS cliente, ' .
        'detalle_venta.nombre AS producto, ' .
        'detalle_venta.id_producto, ' . // 👈 ¡esto es lo que faltaba!
        'SUM(detalle_venta.cantidad) AS cantidad_total, ' .
        'SUM(detalle_venta.cantidad * detalle_venta.precio) AS total_producto'
    );
    $this->join('detalle_venta', 'detalle_venta.id_venta = ventas.id');
    $this->join('clientes', 'clientes.id = ventas.id_cliente');
    $this->where('ventas.activo', $activo);
    $this->where('DATE(ventas.fecha_alta) >=', $fecha_inicio);
    $this->where('DATE(ventas.fecha_alta) <=', $fecha_fin);
    $this->groupBy(['fecha', 'clientes.id', 'detalle_venta.id_producto', 'detalle_venta.nombre']);
    $this->orderBy('fecha', 'DESC');

    return $this->findAll();
}

public function obtenerDetalleGanancias($fecha_inicio, $fecha_fin)
{
    $fecha_inicio .= ' 00:00:00';
    $fecha_fin .= ' 23:59:59';

    $builder = $this->db->table('detalle_venta dv');
    $builder->select("
        dv.nombre AS producto,
        dv.cantidad AS cantidad_vendida,
        dv.precio AS precio_venta,

        -- Precio de compra total
        dv.cantidad * COALESCE(
            (SELECT dc.precio 
             FROM detalle_compra dc
             INNER JOIN lotes_productos lp ON lp.id_detalle_compra = dc.id
             WHERE lp.id = dv.id_lote
             LIMIT 1),
            dv.precio_compra
        ) AS precio_compra_total,

        -- Precio total de venta (condición con presentaciones_productos)
        CASE 
            WHEN pp.cantidad_unidades = 1 
                THEN dv.cantidad * dv.precio
            ELSE COALESCE(dv.cantidad_mayor, dv.cantidad) * dv.precio
        END AS total_venta_real
    ");
    $builder->join('ventas v', 'v.id = dv.id_venta');
    $builder->join('presentaciones_productos pp', 'pp.id = dv.id_presentacion', 'left');
    $builder->where('v.fecha_alta >=', $fecha_inicio);
    $builder->where('v.fecha_alta <=', $fecha_fin);
    $builder->orderBy('dv.nombre', 'ASC');

    return $builder->get()->getResultArray();
}


public function obtenerCantidadesParaTicket($id_venta)
{
    $builder = $this->db->table('detalle_venta dv');
    
    $builder->select("
        dv.id AS id_detalle,
        dv.id_producto,
        dv.id_presentacion,
        dv.cantidad,
        dv.cantidad_mayor,
        pp.cantidad_unidades,
        CASE
            WHEN pp.cantidad_unidades = 1 THEN dv.cantidad
            ELSE dv.cantidad_mayor
        END AS cantidad_a_usar
    ");
    
    $builder->join('presentaciones_productos pp', 'pp.id = dv.id_presentacion', 'left');
    $builder->where('dv.id_venta', $id_venta);
    
    return $builder->get()->getResultArray();
}




}

?>