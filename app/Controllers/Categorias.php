<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriasModel;
use App\Models\ProductosModel;
use App\Models\DetalleRolesPermisosModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Categorias extends BaseController
{
    protected $categorias,$detalleRoles,$productos;
    protected $reglas;

    public function __construct()
    {
        $this->categorias = new CategoriasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->productos = new ProductosModel();
        helper(['form']);
        $this->reglas = [
            'nombre' => [
                'rules' => 'required|alpha_space|min_length[2]|max_length[30]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'alpha_space' => 'El campo {field} solo debe contener letras y espacios.',
                    'min_length' => 'El campo {field} debe tener al menos 2 caracteres.',
                    'max_length' => 'El campo {field} no debe exceder los 30 caracteres.'
                ]
                ],
                'dias_aviso' => ['rules' => 'required|numeric']
        ];
        
    }

    public function index($activo = 1)
{
    $categorias = $this->categorias->where('activo', $activo)->orderBy('id', 'DESC')->findAll();
    $data = ['titulo' => 'Categorizacion de Productos', 'datos' => $categorias];
    
    echo view('header');
    echo view('categorias/categorias', $data);
    echo view('footer');
}


    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_categorizacion');
        if (!$permiso) {
            echo '<div style="text-align:center; margin-top: 50px;">
                    <h2 style="color: #e74c3c;">Acceso Denegado</h2>
                    <p>Lo sentimos, no tienes permiso para acceder a esta sección.</p>
                    <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Volver atrás
                    </button>
                </div>';
            exit;
        }
        $categorias = $this->categorias->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Categorias eliminadas', 'datos' => $categorias];
        
       
        echo view('header');
        echo view('categorias/eliminados', $data);
        echo view('footer');
    }



    public function nuevo()
    {
        $data = ['titulo' => 'Agregar categoria'];
        
        echo view('header');
        echo view('categorias/nuevo', $data);
        echo view('footer');
        
    }
public function insertar()
{
    $validation = \Config\Services::validation();

    $validation->setRules([
        'id_producto' => 'required|numeric',
        'codigo_barra' => 'required|max_length[20]',
        'descripcion' => 'required|max_length[50]',
    ]);

    if (!$this->validate($validation->getRules())) {
        $data = [
            'titulo' => 'Agregar Variante',
            'productos' => (new \App\Models\ProductosModel())->where('activo', 1)->findAll(),
            'validation' => $this->validator
        ];
        echo view('header');
        echo view('variantesproducto/nuevo', $data);
        echo view('footer');
        return;
    }

    $id_producto = $this->request->getPost('id_producto');
    $codigo_barra = $this->request->getPost('codigo_barra');
    $descripcion = $this->request->getPost('descripcion');

    $variantesModel = new \App\Models\VariantesProductoModel();

    // Buscar **solo variante activa** con ese código de barra
    $varianteActiva = $variantesModel->where('codigo_barra', $codigo_barra)
                                     ->where('activo', 1)
                                     ->first();

    if ($varianteActiva) {
        return redirect()->back()->with('error', 'Ya existe una variante activa con ese código de barra.');
    }

    // Buscar variante inactiva para reactivar
    $varianteInactiva = $variantesModel->where('codigo_barra', $codigo_barra)
                                       ->where('activo', 0)
                                       ->first();

    if ($varianteInactiva) {
        // Reactivar
        $variantesModel->update($varianteInactiva['id'], [
            'id_producto' => $id_producto,
            'descripcion' => $descripcion,
            'activo' => 1,
            'fecha_edit' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url() . '/variantesproducto')->with('mensaje', 'Variante reactivada correctamente.');
    }

    // Si no existe → insertar nueva
    $variantesModel->save([
        'id_producto' => $id_producto,
        'codigo_barra' => $codigo_barra,
        'descripcion' => $descripcion,
        'activo' => 1
    ]);

    return redirect()->to(base_url() . '/variantesproducto')->with('mensaje', 'Variante agregada correctamente.');
}

    
    //FUNCION ELIMINAR
    public function eliminar($id)
    {
        // Verificación de permisos
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_categorizacion');
        if (!$permiso) {
            echo '<div style="text-align:center; margin-top: 50px;">
                    <h2 style="color: #e74c3c;">Acceso Denegado</h2>
                    <p>Lo sentimos, no tienes permiso para realizar esta Opccion.</p>
                    <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Volver atrás
                    </button>
                  </div>';
            exit;
        }
    
        // Verificar si hay productos asociados a este proveedor
        $productosAsociados = $this->productos->where('id', $id)->findAll();
    
        if (!empty($productosAsociados)) {
            // Si hay productos asociados, impedir la eliminación y mostrar un mensaje
            echo '<div style="text-align:center; margin-top: 50px;">
                    <h2 style="color: #e74c3c;">Error al eliminar</h2>
                    <p>No se puede eliminar esta Categoria porque tiene productos asociados.</p>
                    <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Volver atrás
                    </button>
                  </div>';
            exit;
        }
    
    
        // Si no hay productos asociados, desactivar la categoría
        $this->categorias->update($id, ['activo' => 0]);
    
        return redirect()->to(base_url().'/categorias');
    }
    





    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        $this->categorias->update($id, ['activo' => 1]);

        return redirect()->to(base_url().'/categorias');
    }

    public function listar_producto($id_categoria)
{
    $datos = $this->categorias->obtenerPorCategoria($id_categoria, 1); // Llama a la función con el ID de categoría
    $data = ['titulo' => 'Productos en Categoría', 'datos' => $datos];

    echo view('header');
    echo view('categorias/listar_producto', $data);
    echo view('footer');
}


public function importarVista()
    {
        echo view('header');
        echo view('categorias/importar');
        echo view('footer');
    }

    // Procesar Excel
    public function importarExcel()
    {
        $archivo = $this->request->getFile('archivo_excel');

        if ($archivo->isValid() && $archivo->getExtension() === 'xlsx') {
            $spreadsheet = IOFactory::load($archivo->getTempName());
            $datos = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            unset($datos[1]); // quitamos encabezados

            $insertados = 0;
            $errores = [];
            $linea = 2;

            $nombres_excel = [];

            foreach ($datos as $fila) {
                $nombre     = trim($fila['A'] ?? '');
                $dias_aviso = trim($fila['B'] ?? '');

                if (!$nombre || !$dias_aviso) {
                    $errores[] = "Fila $linea: faltan datos obligatorios (nombre o días aviso).";
                    $linea++;
                    continue;
                }

                // Verificar duplicado en el mismo archivo
                if (in_array(strtolower($nombre), $nombres_excel)) {
                    $errores[] = "Fila $linea: Categoría '$nombre' duplicada dentro del Excel.";
                    $linea++;
                    continue;
                }

                $nombres_excel[] = strtolower($nombre);

                // Verificar si ya existe en BD
                $existe = $this->categorias
                    ->where('nombre', $nombre)
                    ->first();

                if ($existe) {
                    $linea++;
                    continue; // ignoramos duplicados de BD
                }

                // Insertar categoría
                $this->categorias->save([
                    'nombre' => $nombre,
                    'dias_aviso' => (int)$dias_aviso,
                    'activo' => 1
                ]);

                $insertados++;
                $linea++;
            }

            $mensaje = "✅ <strong>$insertados categorías importadas correctamente.</strong>";

            if (count($errores) > 0) {
                $mensaje .= "<br><strong>⚠️ Errores encontrados:</strong><ul>";
                foreach ($errores as $error) {
                    $mensaje .= "<li>$error</li>";
                }
                $mensaje .= "</ul>";
            }

            return redirect()->to(base_url('categorias/importarVista'))->with('mensaje', $mensaje);
        }

        return redirect()->back()->with('mensaje', '❌ Archivo inválido o no compatible.');
    }

    
}
?>