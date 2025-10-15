<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleAjusteModel extends Model
{
    protected $table      = 'detalle_ajuste_inventario';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

protected $allowedFields = [
        'id_ajuste', 
        'id_producto', 
        'id_lote', 
        'cantidad_antes', 
        'cantidad_despues', 
        'diferencia', 
        'observacion'
    ];

    protected bool $allowEmptyInserts = true;

    // Dates
    protected $useTimestamps = false;
    //protected $dateFormat    = 'datetime';


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