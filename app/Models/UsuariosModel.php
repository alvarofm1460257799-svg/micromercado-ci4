<?php

namespace App\Models;

use CodeIgniter\Model;


class UsuariosModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['usuario','password', 'id_empleado','id_caja','id_rol','activo'];

    

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = 'fecha_modifica';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function obtenerUsuarios() {
        $builder = $this->db->table('usuarios');
        $builder->select('usuarios.id, usuarios.usuario, empleados.nombres AS empleado, roles.nombre AS rol, cajas.nombre AS caja');
        $builder->join('empleados', 'usuarios.id_empleado = empleados.id');
        $builder->join('roles', 'usuarios.id_rol = roles.id');
        $builder->join('cajas', 'usuarios.id_caja = cajas.id');
        $query = $builder->get();
        return $query->getResultArray();
    }
}
