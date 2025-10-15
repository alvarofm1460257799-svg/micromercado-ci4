<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleVentaModel extends Model
{
    protected $table      = 'detalle_venta';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_venta','id_producto','id_lote','id_presentacion','nombre','cantidad','cantidad_mayor','precio','precio_compra','movimiento'];

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

    

    
}

?>