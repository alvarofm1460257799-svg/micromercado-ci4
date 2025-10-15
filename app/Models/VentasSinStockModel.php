<?php namespace App\Models;

use CodeIgniter\Model;

class VentasSinStockModel extends Model
{
    protected $table            = 'ventas_sin_stock';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_venta', 'id_producto', 'nombre_producto', 'cantidad_faltante'];

    protected $useTimestamps = false; // 🔴 DESACTIVA created_at y updated_at
}
