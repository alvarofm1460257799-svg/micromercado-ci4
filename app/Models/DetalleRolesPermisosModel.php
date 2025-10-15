<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleRolesPermisosModel extends Model
{
    protected $table      = 'detalle_roles_permisos';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_rol', 'id_permiso'];

    //protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    //protected $dateFormat    = 'datetime';
    protected $createdField  = '';
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


    public function verificaPermisos ($idRol, $permiso)
    {
        $tieneAcceso =false;
        $this->select('*');
        $this->join('permisos', 'detalle_roles_permisos.id_permiso=permisos.id');
        $existe= $this->where(['id_rol'=> $idRol, 'permisos.nombre'=>$permiso])->first(); 
        //echo $this->getLastQuery();
        if($existe!=null){
            $tieneAcceso =true;
        }
        return $tieneAcceso;
       }
}


?>