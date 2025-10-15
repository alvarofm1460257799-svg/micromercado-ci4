<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProveedoresModel;
use App\Models\ProductosModel;
use App\Models\DetalleRolesPermisosModel;
use PhpOffice\PhpSpreadsheet\IOFactory;


class Proveedores extends BaseController
{
    protected $proveedores, $productos,$detalleRoles;
    protected $reglas;

    public function __construct()
    {
        $this->proveedores = new ProveedoresModel();
        $this->productos = new ProductosModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();

        
        helper(['form']);
        $this->reglas = [
            'nombre' => [
                'rules' => 'required|alpha_space|max_length[20]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'alpha_space' => 'El campo {field} solo debe contener letras y espacios.',
                    'max_length' => 'El campo {field} no debe exceder los 20 caracteres.'
                ]
            ],
            
            'apellido' => [
                'rules' => 'required|alpha_space|max_length[20]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'alpha_space' => 'El campo {field} solo debe contener letras y espacios.',
                    'max_length' => 'El campo {field} no debe exceder los 20 caracteres.'
                ]
            ],
            
            'CI' => [
                'rules' => 'required|is_unique[proveedores.CI]|min_length[6]|max_length[10]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser único.',
                    'min_length' => 'El campo {field} debe tener al menos 6 dígitos.',
                    'max_length' => 'El campo {field} no debe exceder los 10 dígitos.'
                ]
            ],
            
            'cel_ref' => [
                'rules' => 'required|numeric|min_length[8]|max_length[10]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'numeric' => 'El campo {field} solo debe contener números.',
                    'min_length' => 'El campo {field} debe tener al menos 8 dígitos.',
                    'max_length' => 'El campo {field} no debe exceder los 10 dígitos.'
                ]
            ],
            
            'direccion' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'max_length' => 'El campo {field} no debe exceder los 50 caracteres.'
                ]
            ]
        ];
        
    }

    public function index($activo = 1)
    {
    
    
        $proveedores = $this->proveedores
                            ->where('activo', $activo)
                            ->orderBy('id', 'DESC') // Orden descendente
                            ->findAll();
    
        $data = ['titulo' => 'Proveedores', 'datos' => $proveedores];
        
        echo view('header');
        echo view('proveedores/proveedores', $data);
        echo view('footer');
    }
    

    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {   
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_proveedores');
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
        $proveedores = $this->proveedores->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Proveedores Eliminados', 'datos' => $proveedores];
        
       
        echo view('header');
        echo view('proveedores/eliminados', $data);
        echo view('footer');
    }

    public function nuevo()
    {
        $data = ['titulo' => 'Agregar proveedor'];
        
        echo view('header');
        echo view('proveedores/nuevo', $data);
        echo view('footer');
        
    }

    public function insertar()
    {
        if($this->request->getMethod()=="post" && $this->validate($this->reglas)){
        $this->proveedores->save([
        'nombre' => $this->request->getPost('nombre'),
        'apellido' => $this->request->getPost('apellido'),
        'CI' => $this->request->getPost('CI'),
        'cel_ref' => $this->request->getPost('cel_ref'),
        'direccion' => $this->request->getPost('direccion'),
         'empresa' => $this->request->getPost('empresa')

        ]);
        

        return redirect()->to(base_url().'/proveedores');
    }else{
        $data =['titulo'=> 'Agregar proveedor', 'validation' => $this->validator];
        echo view ('header');
        echo view ('proveedores/nuevo', $data);
        echo view('footer');
    }
    }
  
    //findAll = todos los registro
    //first = buscar el primero 
    
    //FUNCIONES
    public function editar($id, $valid=null)
    {
        $proveedor = $this->proveedores->where('id',$id)->first();
        if($valid != null){
            $data = ['titulo' => 'Editar proveedor', 'datos'=>$proveedor, 'validation'=> $valid];
        }else{
            $data = ['titulo' => 'Editar proveedor', 'datos'=>$proveedor];
        }
        
        
        echo view('header');
        echo view('proveedores/editar', $data);
        echo view('footer');
        
    }
    //FUNCION ACTUALIZAR
    public function actualizar()
{
    // Obtener el ID del proveedor que se está editando
    $id = $this->request->getPost('id');
    
    // Obtener los datos actuales del proveedor
    $proveedor = $this->proveedores->where('id', $id)->first();

    // Verificar si el CI ha cambiado
    if ($this->request->getPost('CI') == $proveedor['CI']) {
        // Si el CI no ha cambiado, no aplicar la regla 'is_unique'
        $this->reglas['CI']['rules'] = 'required';
    }

    // Validar el formulario y actualizar si es válido
    if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
        $this->proveedores->update($id, [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'CI' => $this->request->getPost('CI'),
            'cel_ref' => $this->request->getPost('cel_ref'),
            'direccion' => $this->request->getPost('direccion'),
            'empresa' => $this->request->getPost('empresa')
        ]);
        
        return redirect()->to(base_url() . '/proveedores');
    } else {
        // Si hay errores de validación, volver a cargar la vista de edición
        return $this->editar($id, $this->validator);
    }
}

    
    //FUNCION ELIMINAR
    public function eliminar($id)
{
    // Verificación de permisos
    $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_proveedores');
        if (!$permiso) {
            echo '<div style="text-align:center; margin-top: 50px;">
                    <h2 style="color: #e74c3c;">Acceso Denegado</h2>
                    <p>Lo sentimos, no tienes permiso para realizar esta Opccion</p>
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
                <p>No se puede eliminar este proveedor porque tiene productos asociados.</p>
                <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Volver atrás
                </button>
              </div>';
        exit;
    }

    // Si no hay productos asociados, desactivar el proveedor
    $this->proveedores->update($id, ['activo' => 0]);

    return redirect()->to(base_url().'/proveedores');
}
//buscar

public function buscar($id_proveedor)
{
    $datos = $this->proveedores->obtenerPorProveedor($id_proveedor, 1); // Llama a la función con el ID de categoría
    $data = ['titulo' => 'Productos en Categoría', 'datos' => $datos];

    echo view('header');
    echo view('proveedores/buscar', $data);
    echo view('footer');
}





    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        $this->proveedores->update($id, ['activo' => 1]);

        return redirect()->to(base_url().'/proveedores');
    }

    public function listar_productos($id_proveedor)
    {
        $datos = $this->proveedores->obtenerPorProveedor($id_proveedor, 1); // Llama a la función con el ID de categoría
        $data = ['titulo' => 'Productos en Categoría', 'datos' => $datos];
    
        echo view('header');
        echo view('proveedores/listar_productos', $data);
        echo view('footer');
    }










public function importarVista()
{
    echo view('header');
    echo view('proveedores/importar');
    echo view('footer');
}


 public function importarExcel()
{
    $archivo = $this->request->getFile('archivo_excel');

    if ($archivo->isValid() && $archivo->getExtension() === 'xlsx') {
        $spreadsheet = IOFactory::load($archivo->getTempName());
        $datos = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        unset($datos[1]); // Quitar encabezado (fila 1)

        $model = new ProveedoresModel();

        $insertados = 0;
        $errores = [];
        $linea = 2;

        $ci_en_excel = [];
        $nombre_apellido_en_excel = [];

        foreach ($datos as $fila) {
            $empresa   = trim($fila['A'] ?? '');
            $nombre    = trim($fila['B'] ?? '');
            $apellido  = trim($fila['C'] ?? '');
            $ci        = trim($fila['D'] ?? '');
            $cel_ref   = trim($fila['E'] ?? '');
            $direccion = trim($fila['F'] ?? '');

            if (!$nombre || !$apellido || !$ci) {
                $errores[] = "Fila $linea: Campos obligatorios vacíos (nombre, apellido o CI).";
                $linea++;
                continue;
            }

            // Verificar repetido en el Excel mismo (por CI)
            if (in_array($ci, $ci_en_excel)) {
                $errores[] = "Fila $linea: CI '$ci' duplicado en el archivo.";
                $linea++;
                continue;
            }

            // Verificar repetido en el Excel mismo (por nombre + apellido)
            $nombre_apellido_key = strtolower($nombre . '_' . $apellido);
            if (in_array($nombre_apellido_key, $nombre_apellido_en_excel)) {
                $errores[] = "Fila $linea: Nombre y apellido '$nombre $apellido' duplicados en el archivo.";
                $linea++;
                continue;
            }

            // Agregar a listas de control
            $ci_en_excel[] = $ci;
            $nombre_apellido_en_excel[] = $nombre_apellido_key;

            // Verificar existencia en la BD (pero sin mostrar error si ya existe)
            $yaExisteCI = $model->where('CI', $ci)->first();
            $yaExisteNombre = $model->where('nombre', $nombre)->where('apellido', $apellido)->first();

            if ($yaExisteCI || $yaExisteNombre) {
                $linea++;
                continue; // Ignorar sin error
            }

            // Insertar
            $model->save([
                'empresa'   => $empresa,
                'nombre'    => $nombre,
                'apellido'  => $apellido,
                'CI'        => $ci,
                'cel_ref'   => $cel_ref,
                'direccion' => $direccion,
                'activo'    => 1
            ]);

            $insertados++;
            $linea++;
        }

        // Generar mensaje
        $mensaje = "✅ <strong>$insertados proveedor(es) importado(s) correctamente.</strong>";
        if (count($errores) > 0) {
            $mensaje .= "<br><strong>⚠️ Errores encontrados en el archivo:</strong><ul>";
            foreach ($errores as $e) {
                $mensaje .= "<li>$e</li>";
            }
            $mensaje .= "</ul>";
        }

        return redirect()->to(base_url('proveedores/importarVista'))->with('mensaje', $mensaje);
    }

    return redirect()->back()->with('mensaje', '❌ Archivo inválido o no compatible.');
}




}
?>