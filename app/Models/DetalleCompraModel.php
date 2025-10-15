<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleCompraModel extends Model
{
    protected $table      = 'detalle_compra';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_compra','id_producto','id_presentacion','nombre','cantidad','cantidad_mayor','precio','movimiento'];

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


public function obtenerMovimientosActivos()
{
    $sql = "
        SELECT * FROM (
            -- Compras
            SELECT 
                CONVERT('Compra' USING utf8) COLLATE utf8_general_ci AS movimiento, 
                c.fecha_alta, 
                CONVERT(p.nombre USING utf8) COLLATE utf8_general_ci AS producto, 
                dc.cantidad
            FROM detalle_compra dc
            JOIN compras c ON c.id = dc.id_compra
            JOIN productos p ON p.id = dc.id_producto
            WHERE c.activo = 1 AND p.activo = 1

            UNION ALL

            -- Ventas
            SELECT 
                CONVERT('Venta' USING utf8) COLLATE utf8_general_ci AS movimiento, 
                v.fecha_alta, 
                CONVERT(p.nombre USING utf8) COLLATE utf8_general_ci AS producto, 
                dv.cantidad
            FROM detalle_venta dv
            JOIN ventas v ON v.id = dv.id_venta
            JOIN productos p ON p.id = dv.id_producto
            WHERE v.activo = 1 AND p.activo = 1

            UNION ALL

            -- Ventas sin stock
            SELECT 
                CONVERT('Venta (sin stock)' USING utf8) COLLATE utf8_general_ci AS movimiento,
                v.fecha_alta,
                CONVERT(vs.nombre_producto USING utf8) COLLATE utf8_general_ci AS producto,
                vs.cantidad_faltante AS cantidad
            FROM ventas_sin_stock vs
            JOIN ventas v ON v.id = vs.id_venta
            WHERE v.activo = 1

            UNION ALL

            -- Ajustes
            SELECT
                CONVERT('Ajuste' USING utf8) COLLATE utf8_general_ci AS movimiento,
                lp.fecha_registro AS fecha_alta,
                CONVERT(p.nombre USING utf8) COLLATE utf8_general_ci AS producto,
                lp.cantidad
            FROM lotes_productos lp
            JOIN productos p ON p.id = lp.id_producto
            WHERE lp.activo = 1 AND lp.movimiento = 'ajuste'

            UNION ALL

            -- Desechados
            SELECT
                CONVERT('Desechado' USING utf8) COLLATE utf8_general_ci AS movimiento,
                lp.fecha_registro AS fecha_alta,
                CONVERT(p.nombre USING utf8) COLLATE utf8_general_ci AS producto,
                lp.cantidad
            FROM lotes_productos lp
            JOIN productos p ON p.id = lp.id_producto
            WHERE lp.activo = 0 AND lp.movimiento = 'desechado'
        ) AS movimientos
        ORDER BY fecha_alta DESC
    ";

    $query = $this->db->query($sql);
    return $query->getResultArray();
}


    

}

?>