<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedoresModel extends Model
{
    protected $table      = 'proveedores';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['empresa','nombre', 'apellido','CI','cel_ref','direccion','activo'];

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


    public function obtenerPorProveedor($id_proveedor, $activo = 1) {
        $this->select('pro.id, pro.codigo AS codigo, pro.nombre AS nombre, pro.precio_compra AS compra, proveedores.nombre AS proveedor');
        $this->join('productos AS pro', 'proveedores.id = pro.id_proveedor');
        $this->where('pro.activo', $activo);
        $this->where('proveedores.id', $id_proveedor); // Filtrar por la categoría específica
        return $this->findAll();
    }
    
}

?>