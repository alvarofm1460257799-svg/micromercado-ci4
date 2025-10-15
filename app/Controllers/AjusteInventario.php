<?php

namespace App\Controllers;

use App\Models\AjustesInventarioModel;
use App\Models\DetalleAjusteModel;
use App\Models\ProductosModel;
use App\Models\LotesProductosModel;

class AjusteInventario extends BaseController
{
    protected $ajusteModel;
    protected $detalleModel;
    protected $productoModel;
    protected $loteModel;

    public function __construct()
    {
        $this->ajusteModel  = new AjustesInventarioModel();
        $this->detalleModel = new DetalleAjusteModel();
        $this->productoModel = new ProductosModel();
        $this->loteModel = new LotesProductosModel();
    }

    // 📌 Vista principal (lista de ajustes)
public function index()
{
    // Obtener ajustes junto con el nombre del usuario
    $ajustes = $this->ajusteModel
                    ->select('ajustes_inventario.*, usuarios.usuario AS nombre_usuario')
                    ->join('usuarios', 'usuarios.id = ajustes_inventario.id_usuario')
                    ->orderBy('fecha', 'DESC')
                    ->findAll();

    $data = [
        'titulo'  => 'Ajustes de Inventario',
        'ajustes' => $ajustes
    ];

    echo view('header');
    echo view('lotesproductos/ajuste', $data);
    echo view('footer');
}



    // 📌 Formulario para nuevo ajuste
   public function crear()
    {
        $productos = $this->productoModel->where('activo', 1)->findAll();

        $data = [
            'titulo' => 'Nuevo Ajuste de Inventario',
            'productos' => $productos
        ];

        echo view('header');
        echo view('lotesproductos/nuevo_ajuste', $data);
        echo view('footer');
    }
public function guardar()
{
    $post = $this->request->getPost();
    $idUsuario = session()->id_usuario ?? null;

    $productos = $post['id_producto'] ?? [];
    $cantidadesAntes = $post['cantidad_antes'] ?? [];
    $cantidadesDespues = $post['cantidad_despues'] ?? [];
    $motivo = $post['motivo'] ?? '';
    $observaciones = $post['observaciones'] ?? '';

    if (empty($productos) || empty($motivo)) {
        return redirect()->back()->with('mensaje', '❌ Complete los campos obligatorios');
    }

    // Insertar ajuste general
    $idAjuste = $this->ajusteModel->insert([
        'fecha' => date('Y-m-d H:i:s'),
        'motivo' => $motivo,
        'observaciones' => $observaciones,
        'id_usuario' => $idUsuario
    ], true); // true para obtener el ID

    // Recorrer productos y ajustar lotes
    foreach ($productos as $i => $idProducto) {
        $antes = isset($cantidadesAntes[$i]) ? (float)$cantidadesAntes[$i] : 0;
        $despues = isset($cantidadesDespues[$i]) ? (float)$cantidadesDespues[$i] : 0;
        $diferencia = $despues - $antes;

        if ($diferencia === 0) continue;

        // Aumentar stock
        if ($diferencia > 0) {
            $this->loteModel->insert([
                'id_producto' => $idProducto,
                'fecha_vencimiento' => date('Y-m-d'),
                'cantidad' => $diferencia,
                'activo' => 1,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'movimiento' => 'AJUSTE',
                'id_detalle_compra' => null
            ]);
        } else {
            // Disminuir stock
            $restante = abs($diferencia);
            $lotes = $this->loteModel
                          ->where('id_producto', $idProducto)
                          ->where('activo', 1)
                          ->orderBy('fecha_vencimiento', 'ASC')
                          ->findAll();

            foreach ($lotes as $lote) {
                if ($restante <= 0) break;

                $cantidadLote = $lote['cantidad'];
                $restar = min($cantidadLote, $restante);

                $this->loteModel->update($lote['id'], [
                    'cantidad' => $cantidadLote - $restar,
                    'activo' => ($cantidadLote - $restar <= 0) ? 0 : 1
                ]);

                $restante -= $restar;
            }
        }

        // Insertar detalle del ajuste
        $this->detalleModel->insert([
            'id_ajuste' => $idAjuste,
            'id_producto' => $idProducto,
            'id_lote' => null,
            'cantidad_antes' => $antes,
            'cantidad_despues' => $despues,
            'diferencia' => $diferencia,
            'observacion' => $observaciones
        ]);
    }

    return redirect()->to(base_url('ajusteInventario'))->with('mensaje', '✅ Ajuste guardado correctamente');
}


public function detalle($id)
{
    // 1️⃣ Obtener el ajuste
    $ajuste = $this->ajusteModel->find($id);

    if (!$ajuste) {
        return redirect()->to(base_url('ajustesInventario'))
                         ->with('mensaje', '❌ Ajuste no encontrado');
    }

    // 2️⃣ Obtener los detalles de los productos ajustados
    $detalles = $this->detalleModel
                     ->select('detalle_ajuste_inventario.*, productos.nombre')
                     ->join('productos', 'productos.id = detalle_ajuste_inventario.id_producto')
                     ->where('id_ajuste', $id)
                     ->findAll();

    $data = [
        'titulo' => "Detalle del ajuste #{$id}",
        'ajuste' => $ajuste,
        'detalles' => $detalles
    ];

    echo view('header');
    echo view('lotesproductos/detalles_ajuste', $data);
    echo view('footer');
}



public function obtenerStock($idProducto)
{
    $db = \Config\Database::connect();
    $builder = $db->table('lotes_productos');
    $builder->selectSum('cantidad', 'stock');
    $builder->where('id_producto', $idProducto);
    $builder->where('activo', 1); // solo lotes activos
    $query = $builder->get()->getRowArray();

    // Si no hay stock, devuelve 0
    $stock = $query['stock'] ?? 0;

    return $this->response->setJSON(['stock' => $stock]);
}


}