<?php namespace App\Controllers;
use App\Models\ProductosModel;
use App\Models\VentasModel;
use App\Models\LotesProductosModel;
use App\PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Inicio extends BaseController
{
    protected $productoModel, $ventasModel, $session, $lotesProductos;
    public function __construct()
    {
        $this->productoModel =new ProductosModel();
        $this->ventasModel =new VentasModel();
        $this->lotesProductos =new LotesProductosModel();
        $this->session=session();

    }
    public function index() {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }
    
        // Obtener el total de productos
        $total = $this->productoModel->totalProductos();
    
        // Obtener la cantidad de productos con stock mínimo
        $minimos = $this->productoModel->contarProductosMinimos();
    


        

        // Obtener productos por vencer en 20 días
        $productosPorVencer = array_filter($this->lotesProductos->productosPorVencer(), function($producto) {
            // Validar que la cantidad_total exista y sea mayor a 0
            return isset($producto['cantidad_total']) && $producto['cantidad_total'] > 0;
        });
        $cantidadPorExpirar = count($productosPorVencer);
    




        // Obtener productos ya vencidos
        $productosExpirados = array_filter($this->lotesProductos->productosVencidos(), function($producto) {
            return isset($producto['cantidad']) && $producto['cantidad'] > 0;
        });
    
        // Obtener total de ventas del día
        $hoy = date('Y-m-d');
        $totalVentas = $this->ventasModel->totalDia($hoy);
    
        // Obtener los productos más vendidos
        $productosMasVendidos = $this->productoModel->getProductosMasVendidos();
    
        // Obtener margen de ganancia
        $margenGanancia = $this->productoModel->getMargenGanancia();
    
        // Pasar los datos a la vista
        $datos = [
            'total' => $total,
            'totalVentas' => $totalVentas,
            'minimos' => $minimos,
            'cantidadPorExpirar' => $cantidadPorExpirar,
            'productosPorVencer' => $productosPorVencer,
            'productosExpirados' => $productosExpirados,
            'productosMasVendidos' => $productosMasVendidos,
            'margenGanancia' => $margenGanancia
        ];
    
        // Cargar las vistas
        echo view('header');
        echo view('inicio', $datos);
        echo view('footer');
    }
    
    
    
    
}
