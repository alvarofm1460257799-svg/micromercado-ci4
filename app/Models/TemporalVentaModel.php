<?php

namespace App\Models;

use CodeIgniter\Model;

class TemporalVentaModel extends Model
{
    protected $table      = 'temporal_venta';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio','id_producto','id_lote','id_presentacion','codigo','nombre','cantidad','cantidad_mayor','precio','subtotal'];

    //protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
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
    
public function porIdPresentacionVenta($id_presentacion, $folio)
{
    return $this->where('folio', $folio)
                ->where('id_presentacion', $id_presentacion)
                ->first();
}


public function actualizarPresentacionVenta($id_presentacion, $folio, $nuevaCantidad, $nuevoSubtotal, $nuevaCantidadUnidades)
{
    $this->where('id_presentacion', $id_presentacion)
         ->where('folio', $folio)
         ->update([
             'cantidad' => $nuevaCantidad,
             'subtotal' => $nuevoSubtotal,
             'cantidad_unidades' => $nuevaCantidadUnidades
         ]);
}

public function eliminarPresentacionVenta($id_presentacion, $folio){
    return $this->where('id_presentacion', $id_presentacion)
                ->where('folio', $folio)
                ->delete();
}

    public function eliminarVenta($folio){
        $this->where('folio', $folio);
        $this->delete();
    
    } 
    public function porVenta($id_venta)
    {
        return $this->where('folio', $id_venta)->findAll();
    }
    

    
}

?>