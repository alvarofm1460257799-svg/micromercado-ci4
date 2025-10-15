<?php

namespace App\Models;

use CodeIgniter\Model;

class ArqueoCajaModel extends Model
{
    protected $table      = 'arqueo_caja';
    protected $primaryKey = 'id';

    //protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_caja', 'id_usuario','fecha_inicio','fecha_fin','monto_inicial','monto_final','total_ventas','estatus'];

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

   public function  getDatos($idCaja,$activo_usuario, $activo_caja){
    $this->select('arqueo_caja.*, cajas.nombre , usuarios.usuario AS nombre');
    $this->join('cajas','arqueo_caja.id_caja=cajas.id');
    $this->join('usuarios','usuarios.id=arqueo_caja.id_usuario');
    $this->where('arqueo_caja.id_caja',$idCaja);
    $this->where('cajas.activo', $activo_usuario);
    $this->where('usuarios.activo', $activo_caja);
    $this->orderBy('arqueo_caja.id','DESC');
    $datos=$this->findAll();
    return $datos;
   }

   
   public function getDatosArqueo($id_arqueo_caja)
   {
       // Construimos la consulta
       $builder = $this->db->table($this->table . ' a');
       $builder->select('
           c.nombre AS nombre_caja, 
           u.usuario AS nombre_usuario, 
           a.monto_inicial, 
           a.monto_final, 
           a.total_ventas,
           a.fecha_inicio, 
           a.fecha_fin
       ');
       
       // Realizamos las uniones
       $builder->join('usuarios u', 'a.id_usuario = u.id');
       $builder->join('cajas c', 'a.id_caja = c.id');
       
       // Aplicamos los filtros
       $builder->where('a.id', $id_arqueo_caja);
       $builder->where('u.activo', 1);  // Solo usuarios activos
       $builder->where('c.activo', 1);  // Solo cajas activas

       // Ejecutamos la consulta y retornamos el resultado
       return $builder->get()->getResultArray();
   }



   
}

?>