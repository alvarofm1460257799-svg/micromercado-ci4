<?php

namespace App\Models;

use CodeIgniter\Model;

class VariantesProductoModel extends Model
{
    protected $table      = 'variantes_producto';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_producto',
        'codigo_barra',
        'descripcion',
        'activo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = 'fecha_edit';

    // Validation
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

 public function obtenerConProducto()
{
    return $this->where('variantes_producto.activo', 1)
                ->join('productos', 'productos.id = variantes_producto.id_producto')
                ->where('productos.activo', 1)
                ->select('variantes_producto.*, productos.nombre AS producto_nombre')
                ->findAll();
}

 
}
?>
