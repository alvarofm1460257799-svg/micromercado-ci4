<?php

namespace App\Models;

use CodeIgniter\Model;

class PresentacionesModel extends Model
{
    protected $table      = 'presentaciones_productos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_producto', 'tipo','codigo', 'cantidad_unidades', 'precio_venta', 'precio_compra','id_padre', 'activo'
    ];

    protected $returnType = 'array';
    protected $useTimestamps = false;




public function obtenerPresentacionConProducto($id_presentacion)
{
    return $this->select('presentaciones_productos.id as id_presentacion, presentaciones_productos.id_producto, presentaciones_productos.tipo, presentaciones_productos.codigo, productos.nombre as nombre_producto')
        ->join('productos', 'productos.id = presentaciones_productos.id_producto')
        ->where('presentaciones_productos.id', $id_presentacion)
        ->first();
}
public function obtenerPresentacionConNombreProductoPorCodigo($codigo)
{
    return $this->select('presentaciones_productos.*, productos.nombre as nombre_producto')
        ->join('productos', 'presentaciones_productos.id_producto = productos.id')
        ->where('presentaciones_productos.codigo', $codigo)
        ->first();
}



public function obtenerJerarquiaHaciaUnidad($id_presentacion)
{
    $jerarquia = [];
    $actual = $this->find($id_presentacion);

    while ($actual) {
        array_unshift($jerarquia, $actual); // en lugar de $jerarquia[] = $actual
        if (empty($actual['id_padre'])) break;
        $actual = $this->find($actual['id_padre']);
    }

    return $jerarquia; // ahora va desde padre (ej: caja) hasta hijo (unidad)
}



// En el modelo PresentacionesModel:
public function obtenerJerarquiaCompleta($id_presentacion)
{
    $db = \Config\Database::connect();

    // Obtener presentación actual
    $presentacion = $db->table('presentaciones_productos')
        ->select('id, tipo, cantidad_unidades, id_producto, id_padre')
        ->where('id', $id_presentacion)
        ->get()
        ->getRowArray();

    if (!$presentacion) {
        return [];
    }

    $id_producto = $presentacion['id_producto'];

    // SUBIR a padres
    $ascendentes = [];
    $padre = $presentacion;
    while ($padre && $padre['id_padre']) {
        $padre = $db->table('presentaciones_productos')
            ->select('id, tipo, cantidad_unidades, id_padre')
            ->where('id', $padre['id_padre'])
            ->get()
            ->getRowArray();

        if ($padre) {
            array_unshift($ascendentes, $padre); // insert al inicio para mantener orden padre -> hijo
        }
    }

    // BAJAR a hijos
    $descendentes = [];
    $actual = $presentacion;
    while ($actual) {
        $descendentes[] = $actual;

        $hijo = $db->table('presentaciones_productos')
            ->select('id, tipo, cantidad_unidades, id_padre')
            ->where('id_padre', $actual['id'])
            ->where('id_producto', $id_producto)
            ->get()
            ->getRowArray();

        $actual = $hijo;
    }

    // Construir jerarquía completa ordenada de mayor a menor presentación
    // ascendente padres + presentación + descendente hijos (sin repetir presentación actual)
    $jerarquia = array_merge($ascendentes, $descendentes);

    return $jerarquia;
}

// En tu modelo PresentacionesModel.php
public function calcularFactorDesdeUnidadHasta($id_presentacion)
{
    $db = \Config\Database::connect();

    $presentacion = $db->table('presentaciones_productos')
        ->select('id, cantidad_unidades, id_padre, id_producto')
        ->where('id', $id_presentacion)
        ->get()
        ->getRowArray();

    if (!$presentacion) return 1;

    $factor = 1;
    $actual = $presentacion;

    while ($actual) {
        if ($actual['cantidad_unidades'] > 0) {
            $factor *= $actual['cantidad_unidades'];
        }

        // Buscar hijo descendente
        $hijo = $db->table('presentaciones_productos')
            ->select('id, cantidad_unidades, id_padre')
            ->where('id_padre', $actual['id'])
            ->get()
            ->getRowArray();

        if ($hijo) {
            $actual = $hijo;
        } else {
            break;
        }
    }

    return $factor;
}





}
