<?php

namespace App\Models;

use CodeIgniter\Model;

class AjustesInventarioModel  extends Model
{
    protected $table      = 'ajustes_inventario';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

      protected $allowedFields = ['fecha','motivo','observaciones','id_usuario'];

    //protected bool $allowEmptyInserts = false;

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