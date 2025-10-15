<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductosModel;
use App\Models\CategoriasModel;
use App\Models\DetalleRolesPermisosModel;
use App\Models\DetalleVentaModel;
use App\Models\LotesProductosModel;
use App\Models\ProveedoresModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Productos extends BaseController
{
    protected $productos, $detalleRoles, $session;
    protected $proveedores;
    protected $categorias;
    protected $lotesProductos; // Modelo para los lotes
    protected $reglas;

    public function __construct()
    {       
        $this->productos = new ProductosModel();
        $this->proveedores = new ProveedoresModel();
        $this->categorias = new CategoriasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->lotesProductos = new LotesProductosModel(); 
        $this->session = session();


        
        helper(['form']);
        // Reglas de validación
        $this->reglas = [
            'codigo' => [
                'rules' => 'required|is_unique[productos.codigo,id,{id}]|numeric',
                'errors' => [
                    'required' => 'El campo Código es obligatorio.',
                    'is_unique' => 'El código ya está registrado.',
                    'numeric' => 'El código debe contener solo números.'
                ]
            ],
            'nombre' => [
                'rules' => 'required|is_unique[productos.nombre,id,{id}]|max_length[100]',
                'errors' => [
                    'required' => 'El campo Nombre es obligatorio.',
                    'is_unique' => 'El nombre ya está registrado.',
                    'max_length' => 'El nombre no debe exceder los 100 caracteres.'
                ]
            ],
            'precio_venta' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'El campo Precio de venta es obligatorio.',
                    'numeric' => 'El precio de venta debe ser un número.',
                    'greater_than' => 'El precio de venta debe ser mayor que 0.'
                ]
            ],
            'precio_compra' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'El campo Precio de compra es obligatorio.',
                    'numeric' => 'El precio de compra debe ser un número.',
                    'greater_than' => 'El precio de compra debe ser mayor que 0.'
                ]
            ],
        
            'stock_minimo' => [
                'rules' => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'El campo Stock mínimo es obligatorio.',
                    'integer' => 'El stock mínimo debe ser un número entero.',
                    'greater_than_equal_to' => 'El stock mínimo debe ser 0 o mayor.'
                ]
            ],
            'id_proveedor' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo Proveedor es obligatorio.'
                ]
            ],
            'id_categoria' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo Categoría es obligatorio.'
                ]
            ]
        ];
    }
public function index($activo = 1)
{
    // Obtener los productos activos en orden descendente
    $productos = $this->productos->where('activo', $activo)
                                 ->orderBy('id', 'DESC')
                                 ->findAll();

    foreach ($productos as &$producto) {
        // Obtener la fecha de vencimiento más próxima, ignorando fechas inválidas
        $lote = $this->lotesProductos
                     ->where('id_producto', $producto['id'])
                     ->where('fecha_vencimiento IS NOT NULL')
                     ->where('fecha_vencimiento !=', '0000-00-00')
                     ->orderBy('fecha_vencimiento', 'asc')
                     ->first();

        $producto['fecha_vencimiento'] = $lote ? $lote['fecha_vencimiento'] : 'Sin vencimiento';

        // Calcular la cantidad de productos vencidos filtrando fechas inválidas
        $vencidos = $this->lotesProductos
                         ->selectSum('cantidad', 'productos_vencidos')
                         ->where('id_producto', $producto['id'])
                         ->where('activo', 1)
                         ->where('fecha_vencimiento IS NOT NULL')
                         ->where('fecha_vencimiento !=', '0000-00-00')
                         ->where('fecha_vencimiento <=', date('Y-m-d'))
                         ->get()
                         ->getRow();

        $producto['productos_vencidos'] = $vencidos ? $vencidos->productos_vencidos : 0;

        // Calcular las existencias basadas en los lotes activos
        $existencias = $this->lotesProductos
                            ->selectSum('cantidad', 'total_existencias')
                            ->where('id_producto', $producto['id'])
                            ->where('activo', 1) // Solo lotes activos
                            ->get()
                            ->getRow();

        $producto['existencias'] = $existencias ? $existencias->total_existencias : 0;
    }

    $data = [
        'titulo' => 'Productos',
        'datos'  => $productos
    ];

    echo view('header');
    echo view('productos/productos', $data);
    echo view('footer');
}

    


    public function reportes($activo = 1)
    {
  

        $productos = $this->productos->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Productos', 'datos' => $productos];
        
        echo view('header');
        echo view('productos/reportes', $data);
        echo view('footer');
        
    }

    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {
       
        $productos = $this->productos->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Productos eliminadas', 'datos' => $productos];
       
       
        echo view('header');
        echo view('productos/eliminados', $data);
        echo view('footer');
    }

       //FUNCION ELIMINAR
       public function eliminar($id)
       {
           // Obtener el producto por su ID
           $producto = $this->productos->find($id);
       
           if ($producto && $producto['existencias'] > 0) {
               // Si tiene existencias, no se puede eliminar
               return redirect()->to(base_url() . '/productos')
                   ->with('error', 'No se puede eliminar el producto porque tiene existencias.');
           }
       
           // Marcar el producto como inactivo (borrado lógico)
           $this->productos->update($id, ['activo' => 0]);
       
           // Desactivar los lotes relacionados
           $this->actualizarEstadoLotes($id, 0);
       
           // Redirigir con mensaje de éxito
           return redirect()->to(base_url() . '/productos')
               ->with('success', 'Producto eliminado correctamente.');
       }
       


       // Método para actualizar el estado de los lotes según el producto
       private function actualizarEstadoLotes($id_producto, $estado)
       {
           $loteModel = new \App\Models\LotesProductosModel();
           $loteModel->where('id_producto', $id_producto)->set(['activo' => $estado])->update();
       
           // Después de actualizar el estado de los lotes, actualizamos las existencias del producto
           $this->actualizarExistenciasDesdeLotes($id_producto);
       }



       public function actualizarExistenciasDesdeLotes($id_producto)
       {
           $loteModel = new \App\Models\LotesProductosModel();
       
           // Sumar las cantidades de los lotes activos
           $cantidadTotal = $loteModel->selectSum('cantidad', 'total')
               ->where('id_producto', $id_producto)
               ->where('activo', 1) // Solo lotes activos
               ->get()
               ->getRow()
               ->total ?? 0;
       
           // Actualizar las existencias del producto
           $this->productos->update($id_producto, ['existencias' => $cantidadTotal]);
       }
       
       


       private function calcularExistenciasDesdeLotes($id_producto)
       {
           $loteModel = new \App\Models\LotesProductosModel();
       
           // Sumar las cantidades de los lotes activos
           $cantidadTotal = $loteModel->selectSum('cantidad', 'total')
               ->where('id_producto', $id_producto)
               ->where('activo', 1) // Solo lotes activos
               ->get()
               ->getRow()
               ->total ?? 0;
       
           return $cantidadTotal;
       }




    public function nuevo()
    {
       
        $proveedores = $this->proveedores->where('activo',1)->findAll();
        $categorias = $this->categorias->where('activo',1)->findAll();
        $data = ['titulo' => 'Agregar producto','proveedores' =>$proveedores,'categorias' =>$categorias];
        
        echo view('header');
        echo view('productos/nuevo', $data);
        echo view('footer');
        
    }
   public function insertar()
{
    $precio_compra = $this->request->getPost('precio_compra');
    $precio_venta = $this->request->getPost('precio_venta');

    // Validar precio de compra vs. venta
    if ($precio_compra > $precio_venta) {
        return redirect()->back()->withInput()->with('error', 'El precio de compra no puede ser mayor que el precio de venta.');
    }

    if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
        // 1️⃣ Insertar producto en la tabla productos
        $this->productos->save([
            'codigo'        => $this->request->getPost('codigo'),
            'nombre'        => $this->request->getPost('nombre'),
            'precio_venta'  => $precio_venta,
            'precio_compra' => $precio_compra,
            'stock_minimo'  => $this->request->getPost('stock_minimo'),
            'fecha_vence'   => $this->request->getPost('fecha_vence'),
            'existencias'   => $this->request->getPost('existencias'),
            'id_proveedor'  => $this->request->getPost('id_proveedor'),
            'id_categoria'  => $this->request->getPost('id_categoria'),
        ]);

        $id_producto = $this->productos->insertID();

        // 2️⃣ Insertar lote inicial
        $this->lotesProductos->save([
            'id_producto' => $id_producto,
            'cantidad'    => 0,
            'fecha_vence' => null,
            'activo'      => 1,
        ]);

        // 3️⃣ Manejo de imagen principal y miniatura
        $img = $this->request->getFile('img_producto');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $ruta_principal = './images/productos/' . $id_producto . '.jpg';
            $ruta_thumb     = './images/productos/thumbs/' . $id_producto . '.jpg';

            // Imagen principal (máx 800x800)
            $this->procesarImagen($img, $ruta_principal, 800, 800);

            // Miniatura para tabla (máx 150x150)
            $this->procesarImagen($img, $ruta_thumb, 150, 150);
        }

        return redirect()->to(base_url('productos'))->with('success', 'Producto registrado correctamente.');
    } else {
        $proveedores = $this->proveedores->where('activo', 1)->findAll();
        $categorias = $this->categorias->where('activo', 1)->findAll();

        $data = [
            'titulo'      => 'Agregar producto',
            'proveedores' => $proveedores,
            'categorias'  => $categorias,
            'validation'  => $this->validator,
            'error'       => session()->getFlashdata('error'),
        ];

        echo view('header');
        echo view('productos/nuevo', $data);
        echo view('footer');
    }
}

/**
 * Convierte la imagen a JPG, redimensiona y guarda en la ruta especificada.
 * @param UploadedFile $img
 * @param string $ruta_destino
 * @param int $maxWidth
 * @param int $maxHeight
 */
private function procesarImagen($img, $ruta_destino, $maxWidth = 800, $maxHeight = 800)
{
    $extension = $img->getClientExtension();

    switch ($extension) {
        case 'png':  $imagen = imagecreatefrompng($img->getTempName()); break;
        case 'gif':  $imagen = imagecreatefromgif($img->getTempName()); break;
        case 'webp': $imagen = imagecreatefromwebp($img->getTempName()); break;
        case 'jpeg':
        case 'jpg':  $imagen = imagecreatefromjpeg($img->getTempName()); break;
        default: return false;
    }

    if ($imagen !== false) {
        $width  = imagesx($imagen);
        $height = imagesy($imagen);

        // Mantener proporción y no ampliar si es más pequeña
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $newWidth  = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        $nuevaImagen = imagecreatetruecolor($newWidth, $newHeight);

        // Fondos transparentes en PNG/GIF
        if (in_array($extension, ['png', 'gif', 'webp'])) {
            imagecolortransparent($nuevaImagen, imagecolorallocatealpha($nuevaImagen, 0, 0, 0, 127));
            imagealphablending($nuevaImagen, false);
            imagesavealpha($nuevaImagen, true);
        }

        imagecopyresampled($nuevaImagen, $imagen, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Guardar JPG con calidad 90
        imagejpeg($nuevaImagen, $ruta_destino, 90);

        imagedestroy($imagen);
        imagedestroy($nuevaImagen);
    }
}



  
    //findAll = todos los registro
    //first = buscar el primero 
    
    //FUNCIONES
    public function editar($id, $valid = null)
{
    $proveedores = $this->proveedores->where('activo', 1)->findAll();
    $categorias = $this->categorias->where('activo', 1)->findAll();
    $producto = $this->productos->where('id', $id)->first();
    
    // Si hay errores de validación, los incluimos en los datos.
    if ($valid != null) {
        $data = [
            'titulo' => 'Editar producto',
            'proveedores' => $proveedores,
            'categorias' => $categorias,
            'producto' => $producto,
            'validation' => $valid // Pasar los errores de validación.
        ];
    } else {
        $data = [
            'titulo' => 'Editar producto',
            'proveedores' => $proveedores,
            'categorias' => $categorias,
            'producto' => $producto
        ];
    }
    
    // Renderizar las vistas con los datos correspondientes.
    echo view('header');
    echo view('productos/editar', $data);
    echo view('footer');
}

public function vista($id, $valid = null)
{
    $proveedores = $this->proveedores->where('activo', 1)->findAll();
    $categorias = $this->categorias->where('activo', 1)->findAll();
    $producto = $this->productos->where('id', $id)->first();
    
    // Obtener la fecha de vencimiento más cercana para el producto
    $lote = $this->lotesProductos
                 ->where('id_producto', $id)
                 ->where('cantidad >', 0) // Solo lotes con stock
                 ->orderBy('fecha_vencimiento', 'ASC') // El más próximo
                 ->first();

    // Agregar la fecha de vencimiento al producto
    if ($lote) {
        $producto['fecha_vencimiento'] = $lote['fecha_vencimiento'];
    } else {
        $producto['fecha_vencimiento'] = 'No disponible';
    }

    // Preparar los datos para la vista
    $data = [
        'titulo' => $valid != null ? 'Editar producto' : 'Ver producto',
        'proveedores' => $proveedores,
        'categorias' => $categorias,
        'producto' => $producto,
    ];

    if ($valid != null) {
        $data['validation'] = $valid; // Pasar errores de validación si los hay.
    }

    // Renderizar la vista
    echo view('header');
    echo view('productos/vista', $data);
    echo view('footer');
}


public function actualizar()
{
    $id = $this->request->getPost('id');
    $producto = $this->productos->where('id', $id)->first();

    if (!$producto) {
        return redirect()->back()->with('error', 'El producto no existe.');
    }

    // Reglas de validación dinámicas
    $this->reglas['codigo']['rules'] = ($this->request->getPost('codigo') == $producto['codigo']) 
        ? 'required|numeric' 
        : 'required|is_unique[productos.codigo,id,' . $id . ']|numeric';

    $this->reglas['nombre']['rules'] = ($this->request->getPost('nombre') == $producto['nombre']) 
        ? 'required|max_length[100]' 
        : 'required|is_unique[productos.nombre,id,' . $id . ']|max_length[100]';

    $precio_compra = $this->request->getPost('precio_compra');
    $precio_venta  = $this->request->getPost('precio_venta');

    if ($precio_compra > $precio_venta) {
        return redirect()->back()->withInput()->with('error', 'El precio de compra no puede ser mayor que el precio de venta.');
    }

    if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {
        // Manejar imagen si se subió una nueva
        $img = $this->request->getFile('img_producto');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $ruta_principal = './images/productos/' . $id . '.jpg';
            $ruta_thumb     = './images/productos/thumbs/' . $id . '.jpg';

            // Borrar imágenes antiguas si existen
            if (file_exists($ruta_principal)) unlink($ruta_principal);
            if (file_exists($ruta_thumb)) unlink($ruta_thumb);

            // Procesar imagen principal y miniatura
            $this->procesarImagen($img, $ruta_principal, 800, 800);
            $this->procesarImagen($img, $ruta_thumb, 150, 150);
        }

        // Actualiza los datos del producto
        $this->productos->update($id, [
            'codigo'        => $this->request->getPost('codigo'),
            'nombre'        => $this->request->getPost('nombre'),
            'precio_venta'  => $precio_venta,
            'precio_compra' => $precio_compra,
            'stock_minimo'  => $this->request->getPost('stock_minimo'),
            'id_proveedor'  => $this->request->getPost('id_proveedor'),
            'id_categoria'  => $this->request->getPost('id_categoria')
        ]);

        return redirect()->to(base_url('/productos'))->with('success', 'Producto modificado correctamente.');
    } else {
        return $this->editar($id, $this->validator);
    }
}



public function limpiar($idProducto)
{
    $db = \Config\Database::connect();
    $fechaHoy = date('Y-m-d'); // Fecha actual
    $fechaActual = date('Y-m-d H:i:s'); // Fecha y hora actuales

    // Consultar los lotes vencidos activos de un producto específico
    // 🔹 Excluir los que no tienen fecha de vencimiento (NULL o '0000-00-00')
    $query = $db->query("
        SELECT id 
        FROM lotes_productos 
        WHERE activo = 1
          AND id_producto = ?
          AND fecha_vencimiento IS NOT NULL
          AND fecha_vencimiento != '0000-00-00'
          AND fecha_vencimiento <= ?
    ", [$idProducto, $fechaHoy]);

    $lotesVencidos = $query->getResultArray();

    if (!empty($lotesVencidos)) {
        foreach ($lotesVencidos as $lote) {
            // Cambiar el estado del lote y registrar el movimiento
            $sql = "UPDATE lotes_productos 
                    SET activo = 0, movimiento = 'DESECHADO', fecha_registro = ? 
                    WHERE id = ?";
            $db->query($sql, [$fechaActual, $lote['id']]);
        }

        return redirect()->to(base_url('productos'))->with('success', 'Se descartaron los productos vencidos correctamente.');
    } else {
        return redirect()->back()->with('error', 'No se encontraron productos vencidos o hubo un error al actualizar.');
    }
}






    
 

    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        // Activar el producto y establecer existencias en 0
        $this->productos->update($id, ['activo' => 1, 'existencias' => 0]);
    
        // Actualizar los lotes asociados al producto para establecer la fecha de vencimiento en null, la cantidad en 0 y activar los lotes
        $this->lotesProductos->where('id_producto', $id)->set([
            'fecha_vencimiento' => null,
            'cantidad' => 0,
            'activo' => 1 // Activar los lotes
        ])->update();
    
        return redirect()->to(base_url().'/productos');
    }
    
    
    
    
    public function buscarPorCodigo($codigo) {
        // Suponiendo que $this->productos es tu modelo de productos
        $this->productos->select('*');
        $this->productos->where('codigo', $codigo);
        $this->productos->where('activo', 1);
        $datos = $this->productos->get()->getRow();
    
        $res = [
            'existe' => false,
            'datos' => '',
            'error' => ''
        ];
    
        if ($datos) {
            $res['datos'] = $datos;
            $res['existe'] = true;
        } else {
            $res['error'] = 'No existe el producto';
        }
    
        echo json_encode($res);
    }
    

    public function  autocompleteData(){
        $returnData = array();
        $valor = $this->request->getGet('term');
        $productos =$this->productos->like('codigo',$valor)->where('activo', 1)->findAll();
        if(!empty($productos)){
            foreach($productos as $row){
                $data['id']=$row['id'];
                $data['value']=$row['codigo'];
                $data['label']=$row['codigo']. ' - '.$row['nombre'];
                array_push($returnData, $data);

            }
        }
         echo json_encode($returnData);
     }


     function muestraCodigos(){
  
        echo view('header');
        echo view('productos/ver_codigos');
        echo view('footer');
    
    
     }


     public function generaBarras(){

        $pdf =new \FPDF('P', 'mm','letter');
        $pdf->AddPage();
        $pdf->SetMargins(10,10,10);
        $pdf->SetTitle("Codigos de Barras");

        $productos = $this->productos->where('activo',1)->findAll();
        foreach($productos as $producto){
            $codigo =$producto['codigo'];
      

        $generaBarcode = new \barcode_genera();
        $generaBarcode ->barcode("images/barcode/".$codigo.".png",$codigo,20,"horizontal","code128",true);
        $pdf->Image("images/barcode/".$codigo.".png");
        //unlink("images/barcode/".$codigo.".png"); para eliminar las imagenes generadas de codigo de barras
        }
    
     $this->response->setHeader('Content-Type','application/pdf');
     $pdf->Output('Codigos.pdf', 'I');
     }

     function mostrarMinimos(){
    
  
        echo view('header');
        echo view('productos/ver_minimos');
        echo view('footer');
    
     
     }
     function productosPorExpirar(){
   
  
        echo view('header');
        echo view('productos/ver_por_expirar');
        echo view('footer');
    
    
     }
 




//REPORTES DESDE AQUI



public function generaMinimosPdf() {
    $pdf = new \FPDF('P', 'mm', 'letter');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetTitle("productos con stock minimo");
    $pdf->SetFont("Arial", 'B', 10);
    $pdf->Image("images/logotipo.png", 10, 5, 20);
    $pdf->SetTitle(utf8_decode("Reporte de Productos con Stock minimo"));
    $pdf->Cell(0, 5, utf8_decode("REPORTE DE PRODUCTOS CON STOCK MINIMO"), 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->Cell(40, 5, utf8_decode("Código"), 1, 0, 'C');
    $pdf->Cell(85, 5, utf8_decode("Nombre Producto"), 1, 0, 'C');
    $pdf->Cell(30, 5, utf8_decode("Existencias"), 1, 0, 'C');
    $pdf->Cell(30, 5, utf8_decode("Stock mínimo"), 1, 1, 'C');
    
    $datosProductos = $this->productos->getProductosMinimos();

    foreach ($datosProductos as $producto) {
        $pdf->Cell(40, 5, $producto["codigo"], 1, 0, 'C');
        $pdf->Cell(85, 5, utf8_decode($producto["nombre"]), 1, 0);
        $pdf->Cell(30, 5, $producto["total_cantidad"], 1, 0, 'C'); // Cambiado de 'existencias' a 'total_cantidad'
        $pdf->Cell(30, 5, $producto["stock_minimo"], 1, 1, 'C');
    }

    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output('ProductoMinimo.pdf', 'I');
}







public function generaPorExpirar()
{
    $db = \Config\Database::connect();

    $fecha_hoy = date('Y-m-d');

    $productos = $db->query("
        SELECT 
            p.id AS id_producto,
            p.codigo,
            p.nombre,
            c.dias_aviso,
            lp.fecha_vencimiento,
            SUM(lp.cantidad) AS cantidad_total
        FROM lotes_productos lp
        JOIN productos p ON p.id = lp.id_producto
        JOIN categorias c ON c.id = p.id_categoria
        WHERE lp.activo = 1
        GROUP BY p.id, lp.fecha_vencimiento, c.dias_aviso
        ORDER BY lp.fecha_vencimiento ASC
    ")->getResultArray();

    $porExpirar = [];

    foreach ($productos as $p) {
        $fecha_vencimiento = $p['fecha_vencimiento'];
        $dias_aviso = (int)$p['dias_aviso'];
        $fecha_limite = date('Y-m-d', strtotime("+$dias_aviso days", strtotime($fecha_hoy)));

        if ($fecha_vencimiento > $fecha_hoy && $fecha_vencimiento <= $fecha_limite) {
            $datosJerarquia = $this->obtenerJerarquiaConFactores($p['id_producto']);
            if (empty($datosJerarquia)) continue;

            $desglose = $this->descomponerJerarquiaSucesiva($p['cantidad_total'], $datosJerarquia['jerarquia']);

            $porExpirar[] = array_merge($p, $desglose);
        }
    }

    if (empty($porExpirar)) {
        echo 'No hay productos por expirar según el aviso de cada categoría.';
        exit();
    }

    // Generar PDF
    $pdf = new \FPDF('P', 'mm', 'letter');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetTitle("Reporte de Productos por Expirar");
    $pdf->SetFont("Arial", 'B', 10);
    $pdf->Image("images/logotipo.png", 10, 5, 20);
    $pdf->Cell(0, 5, utf8_decode("REPORTE DE PRODUCTOS POR EXPIRAR"), 0, 1, 'C');
    $pdf->Ln(10);

    // Encabezado
    $pdf->SetFont("Arial", 'B', 10);
      $pdf->Cell(30, 5, utf8_decode("Código"), 1, 0, 'C');
    $pdf->Cell(80, 5, "Nombre", 1, 0, 'C');
    $pdf->Cell(30, 5, "Vence", 1, 0, 'C');
    $pdf->Cell(20, 5, "Cajas", 1, 0, 'C');
    $pdf->Cell(20, 5, "Paquete", 1, 0, 'C');
    $pdf->Cell(20, 5, "Unidades", 1, 1, 'C');

    // Cuerpo
    foreach ($porExpirar as $p) {
        $pdf->SetFont("Arial", '', 9);

        if (!empty($p['exacto'])) {
            $pdf->SetTextColor(0, 128, 0); // verde si exacto
        } else {
            $pdf->SetTextColor(0, 0, 0); // negro si no exacto
        }

        $pdf->Cell(30, 5, $p['codigo'], 1);
        $pdf->Cell(80, 5, utf8_decode($p['nombre']), 1);
        $pdf->Cell(30, 5, $p['fecha_vencimiento'], 1);
        $pdf->Cell(20, 5, $p['niveles']['fardos'], 1, 0, 'C');
        $pdf->Cell(20, 5, $p['niveles']['cajas'], 1, 0, 'C');
        $pdf->Cell(20, 5, $p['niveles']['unidades'], 1, 1, 'C');

        $pdf->SetTextColor(0, 0, 0);
    }

    $this->response->setHeader('Content-Type', 'application/pdf');
    $pdf->Output('ProductosPorExpirar.pdf', 'I');
}



private function obtenerJerarquiaConFactores($id_producto)
{
    $db = \Config\Database::connect();

    $presentaciones = $db->table('presentaciones_productos')
        ->where('id_producto', $id_producto)
        ->get()
        ->getResultArray();

    if (empty($presentaciones)) return [];

    $presentacionesPorId = [];
    foreach ($presentaciones as $p) {
        $presentacionesPorId[$p['id']] = $p;
    }

    // Buscar la unidad base (cantidad_unidades = 1)
    $unidad = null;
    foreach ($presentaciones as $p) {
        if ((int)$p['cantidad_unidades'] === 1) {
            $unidad = $p;
            break;
        }
    }

    if (!$unidad) return [];

    $jerarquia = [];
    $actual = $unidad;
    $jerarquia[] = $actual;

    while (!empty($actual['id_padre']) && isset($presentacionesPorId[$actual['id_padre']])) {
        $padre = $presentacionesPorId[$actual['id_padre']];
        $jerarquia[] = $padre;
        $actual = $padre;
    }

    return ['jerarquia' => $jerarquia];
}

private function descomponerJerarquiaSucesiva($cantidad_total, $jerarquia)
{
    $niveles = [
        'unidades' => $cantidad_total,
        'cajas' => 0,
        'fardos' => 0
    ];

    // Paso 1: indexar por ID
    $porId = [];
    foreach ($jerarquia as $p) {
        $porId[$p['id']] = $p;
    }

    // Paso 2: encontrar unidad (la que no tiene hijos)
    $unidad = null;
    foreach ($jerarquia as $p) {
        $esHijo = false;
        foreach ($jerarquia as $q) {
            if ($q['id_padre'] == $p['id']) {
                $esHijo = true;
                break;
            }
        }
        if (!$esHijo) {
            $unidad = $p;
            break;
        }
    }

    if (!$unidad) return [
        'niveles' => $niveles,
        'exacto' => true
    ];
    //'Este producto tiene más de 3 niveles jerárquicos. Se ignorarán niveles superiores.
    // Paso 3: subir jerarquía desde unidad
    $caja = isset($porId[$unidad['id_padre']]) ? $porId[$unidad['id_padre']] : null;
    $fardo = ($caja && isset($porId[$caja['id_padre']])) ? $porId[$caja['id_padre']] : null;

    // Calcular cajas
    if ($caja) {
        $unidades_por_caja = (float) $caja['cantidad_unidades'];
        $niveles['cajas'] = round($cantidad_total / $unidades_por_caja, 2);
    }

    // Calcular fardos
    if ($fardo && $caja) {
        $cajas_por_fardo = (float) $fardo['cantidad_unidades'];
        $niveles['fardos'] = round($niveles['cajas'] / $cajas_por_fardo, 2);
    }

    // Comprobación exacta
    $es_exacto = true;
    if (
        isset($niveles['cajas']) && fmod($niveles['cajas'], 1) !== 0 ||
        isset($niveles['fardos']) && fmod($niveles['fardos'], 1) !== 0
    ) {
        $es_exacto = false;
    }

    return [
        'niveles' => $niveles,
        'exacto' => $es_exacto
    ];
}

    


     public function generaProductosMasVendidosPdf() {
        // Instancia de FPDF
        $pdf = new \FPDF('P', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Productos mas vendidos");
        $pdf->SetFont("Arial", 'B', 10);
        $pdf->Image("images/logotipo.png", 10, 5, 20);
        $pdf->Cell(0, 5, utf8_decode("REPORTE DE PRODUCTOS  VENDIDOS"), 0, 1, 'C');
        $pdf->Ln(10);
    
        // Encabezado de la tabla
        $pdf->Cell(40, 5, utf8_decode("Codigo"), 1, 0, 'C');
        $pdf->Cell(100, 5, utf8_decode("Nombre Producto"), 1, 0, 'C');
        $pdf->Cell(40, 5, utf8_decode("Cantidad  Vendida"), 1, 1, 'C');
    
        // Obtener los datos de los productos más vendidos
        $datosProductos = $this->productos->getProductosMasVendidos();
    
        // Rellenar la tabla con los datos
        foreach ($datosProductos as $producto) {
            $pdf->Cell(40, 5, $producto->codigo, 1, 0, 'C');
            $pdf->Cell(100, 5, utf8_decode($producto->nombre), 1, 0);
            $pdf->Cell(40, 5,  $producto->total_vendido.' u.', 1, 1,'C');
        }
    
        // Enviar el PDF al navegador
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('ProductosMasVendidos.pdf', 'I');
    }
    


    
    public function generaMargenGananciaPdf() {
        // Instancia de FPDF
        $pdf = new \FPDF('P', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Margen de Ganancia por Producto");
        $pdf->SetFont("Arial", 'B', 10);
        $pdf->Image("images/logotipo.png", 10, 5, 20);
        $pdf->Cell(0, 5, utf8_decode("REPORTE  GANANCIA POR PRODUCTO"), 0, 1, 'C');
        $pdf->Ln(10);
    
        // Encabezado de la tabla
        $pdf->Cell(40, 5, utf8_decode("Codigo"), 1, 0, 'C');
        $pdf->Cell(100, 5, utf8_decode("Nombre Producto"), 1, 0, 'C');
        $pdf->Cell(45, 5, utf8_decode("Margen de Ganancia Bs "), 1, 1, 'C');
    
        // Obtener los datos de margen de ganancia
        $datosProductos = $this->productos->getMargenGanancia();
    
        // Rellenar la tabla con los datos
        foreach ($datosProductos as $producto) {
            $pdf->Cell(40, 5, $producto->codigo, 1, 0, 'C');
            $pdf->Cell(100, 5, utf8_decode($producto->nombre), 1, 0);
            $pdf->Cell(45, 5,  'Bs '.number_format($producto->margen_ganancia, 2), 1, 1,'R');
        }
    
        // Enviar el PDF al navegador
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('MargenGanancia.pdf', 'I');
    }
    


     public function mostrarMinimosExcel(){
        
      $phpExcel=  new Spreadsheet();
      $phpExcel->getProperties()->setCreator("Alvaro FLores")->setTitle("Reporte Micromercado");
      $hoja =$phpExcel->getActiveSheet();

        $drawing =new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setPath('images/logotipo.png');
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($hoja);

      $hoja->mergeCells('A3:D3');
      $hoja->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
      $hoja->getStyle('A3')->getFont()->setSize(14);
      $hoja->getStyle('A3')->getFont()->setName('Arial');
      $hoja->setCellValue('A3','REPORTE DE PRODUCTOS CON STOCK MINIMO');
      
      $hoja->setCellValue('A5','Codigo');
      $hoja->getColumnDimension('A')->setWidth(20);
      $hoja->setCellValue('B5','Nombre');
      $hoja->getColumnDimension('B')->setWidth(40);
      $hoja->setCellValue('C5','Existencias');
      $hoja->getColumnDimension('C')->setWidth(20);
      $hoja->setCellValue('D5','Stock');
      $hoja->getColumnDimension('D')->setWidth(20);
    $hoja->getStyle('A5:D5')->getFont()->setBold(true);


      $datosProductos= $this->productos->getProductosMinimos();

      $fila= 6;
      foreach($datosProductos as $producto){
        $hoja->setCellValue('A'.$fila,$producto['codigo']);
        $hoja->setCellValue('B'.$fila,$producto['nombre']);
        $hoja->setCellValue('C'.$fila,$producto['existencias']);
        $hoja->setCellValue('D'.$fila,$producto['stock_minimo']);
        $fila++;
      }
      $ultimaFila=$fila -1;

      $styleArray =[
        'borders'=>[
            'allBorders'=>[
                'borderStyle'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color'=>['rgb'=>'000000'],
            ]
        ]
      ];
      $hoja->getStyle('A5:D'.$ultimaFila)->applyFromArray($styleArray);

      $hoja->setCellValueExplicit('C'.$fila,'=SUMA(C5:C'.$ultimaFila.')',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA);
  
  



      $writer=new Xlsx($phpExcel);
      $writer->save("reporte_min.xlsx");

     }

     public function autocompletar()
{
    $term = $this->request->getGet('term');
    $db = \Config\Database::connect();

    $builder = $db->table('presentaciones_productos p');
    $builder->select('p.id, pr.nombre, p.tipo, p.codigo');
    $builder->join('productos pr', 'p.id_producto = pr.id');
    $builder->where('p.activo', 1);
    $builder->like("CONCAT(pr.nombre, ' ', p.tipo)", $term);

    $resultados = $builder->get()->getResult();

    $productos = array_map(function($row) {
        return [
            'id' => $row->id,
            'nombre' => $row->nombre,
            'tipo' => $row->tipo,
            'codigo' => $row->codigo
        ];
    }, $resultados);

    return $this->response->setJSON($productos);
}

    







public function importarVista()
{
    echo view('header');
    echo view('productos/importar'); // Asegúrate de tener la vista en app/Views/productos/importar.php
    echo view('footer');
}
public function importarExcelProductos()
{
    $archivo = $this->request->getFile('archivo_excel');

    if ($archivo->isValid() && $archivo->getExtension() === 'xlsx') {
        $spreadsheet = IOFactory::load($archivo->getTempName());
        $datos = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        unset($datos[1]); // Quitar encabezado (fila 1)

        $model = new ProductosModel();
        $proveedorModel = new ProveedoresModel();
        $categoriaModel = new CategoriasModel();

        $insertados = 0;
        $errores = [];
        $linea = 2;

        $codigos_excel = [];
        $nombres_excel = [];

        foreach ($datos as $fila) {
            $codigo             = trim($fila['A'] ?? '');
            $nombre             = trim($fila['B'] ?? '');
            $precio_venta       = trim($fila['C'] ?? '');
            $precio_compra      = trim($fila['D'] ?? '');
            $stock_minimo       = trim($fila['E'] ?? '');
            $nombre_proveedor   = trim($fila['F'] ?? '');
            $apellido_proveedor = trim($fila['G'] ?? '');
            $nombre_categoria   = trim($fila['H'] ?? '');

            if (!$codigo || !$nombre || !$nombre_proveedor || !$apellido_proveedor || !$nombre_categoria) {
                $linea++;
                continue;
            }

            // Verificar duplicado en el mismo archivo
            if (in_array(strtolower($codigo), $codigos_excel)) {
                $errores[] = "Fila $linea: Código '$codigo' duplicado dentro del Excel.";
                $linea++;
                continue;
            }

            if (in_array(strtolower($nombre), $nombres_excel)) {
                $errores[] = "Fila $linea: Nombre '$nombre' duplicado dentro del Excel.";
                $linea++;
                continue;
            }

            $codigos_excel[] = strtolower($codigo);
            $nombres_excel[] = strtolower($nombre);

            // Buscar proveedor
            $proveedor = $proveedorModel
                ->where('nombre', $nombre_proveedor)
                ->where('apellido', $apellido_proveedor)
                ->first();

            if (!$proveedor) {
                $errores[] = "Fila $linea: Proveedor '$nombre_proveedor $apellido_proveedor' no encontrado.";
                $linea++;
                continue;
            }

            // Buscar categoría
            $categoria = $categoriaModel
                ->where('nombre', $nombre_categoria)
                ->first();

            if (!$categoria) {
                $errores[] = "Fila $linea: Categoría '$nombre_categoria' no encontrada.";
                $linea++;
                continue;
            }

            // Ignorar si ya existe en la base (sin error, como tú querías)
            $existe = $model
                ->where('codigo', $codigo)
                ->orWhere('nombre', $nombre)
                ->first();

            if ($existe) {
                $linea++;
                continue;
            }

            // Guardar producto
            $model->save([
                'codigo'         => $codigo,
                'nombre'         => $nombre,
                'precio_venta'   => (float)$precio_venta,
                'precio_compra'  => (float)$precio_compra,
                'existencias'    => 0,
                'stock_minimo'   => (int)$stock_minimo,
                'id_proveedor'   => $proveedor['id'],
                'id_categoria'   => $categoria['id'],
                'activo'         => 1
            ]);

            $insertados++;
            $linea++;
        }

        $mensaje = "✅ <strong>$insertados productos importados correctamente.</strong>";

        if (count($errores) > 0) {
            $mensaje .= "<br><strong>⚠️ Errores encontrados:</strong><ul>";
            foreach ($errores as $error) {
                $mensaje .= "<li>$error</li>";
            }
            $mensaje .= "</ul>";
        }

        return redirect()->to(base_url('productos/importarVista'))->with('mensaje', $mensaje);
    }

    return redirect()->back()->with('mensaje', '❌ Archivo inválido o no compatible.');
}


}

