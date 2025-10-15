<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriasModel extends Model
{
    protected $table      = 'categorias';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nombre','dias_aviso', 'activo'];

    //protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    //protected $dateFormat    = 'datetime';
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = 'fecha_edit';
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


    public function obtenerPorCategoria($id_categoria, $activo = 1) {
        $this->select('pro.id, pro.codigo AS codigo, pro.nombre AS nombre, pro.precio_venta AS venta, categorias.nombre AS categoria');
        $this->join('productos AS pro', 'categorias.id = pro.id_categoria');
        $this->where('pro.activo', $activo);
        $this->where('categorias.id', $id_categoria); // Filtrar por la categoría específica
        return $this->findAll();
    }
    


}

?>