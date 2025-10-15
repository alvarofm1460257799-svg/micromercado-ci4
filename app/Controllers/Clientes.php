<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientesModel;
use PhpParser\Node\Expr\Empty_;
use App\Models\DetalleRolesPermisosModel;

class Clientes extends BaseController
{
    protected $clientes,$detalleRoles;
    protected $unidades;
    protected $categorias;
    protected $reglas;

    public function __construct()
    {
        $this->clientes = new ClientesModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
     
        helper(['form']);
        $this->reglas = [
            'nombre' => [
                'rules' => 'required|alpha_space|min_length[2]|max_length[50]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'alpha_space' => 'El campo {field} solo debe contener letras y espacios.',
                    'min_length' => 'El campo {field} debe tener al menos 2 caracteres.',
                    'max_length' => 'El campo {field} no debe exceder los 50 caracteres.'
                ]
            ],
            'CI' => [
                'rules' => 'required|is_unique[clientes.CI]|min_length[6]|max_length[10]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser único.',
                    'min_length' => 'El campo {field} debe tener al menos 6 dígitos.',
                    'max_length' => 'El campo {field} no debe exceder los 10 dígitos.'
                ]
            ],
            'direccion' => [
                'rules' => 'max_length[100]',
                'errors' => [
                    'max_length' => 'El campo {field} no debe exceder los 100 caracteres.'
                ]
            ],
            'telefono' => [
                'rules' => 'numeric|min_length[8]|max_length[10]',
                'errors' => [
                    'numeric' => 'El campo {field} solo debe contener números.',
                    'min_length' => 'El campo {field} debe tener al menos 8 dígitos.',
                    'max_length' => 'El campo {field} no debe exceder los 10 dígitos.'
                ]
            ],
            'correo' => [
                'rules' => 'valid_email|max_length[50]',
                'errors' => [
                    'valid_email' => 'El campo {field} debe contener un correo electrónico válido.',
                    'max_length' => 'El campo {field} no debe exceder los 50 caracteres.'
                ]
            ]
        ];
        
     
    }

    public function index($activo = 1)
    {
      
    
        $clientes = $this->clientes
                         ->where('activo', $activo)
                         ->orderBy('id', 'DESC') // Orden descendente
                         ->findAll();
    
        $data = ['titulo' => 'Clientes', 'datos' => $clientes];
        
        echo view('header');
        echo view('clientes/clientes', $data);
        echo view('footer');
    }
    
    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_clientes');
        if (!$permiso) {
            echo '<div style="text-align:center; margin-top: 50px;">
                    <h2 style="color: #e74c3c;">Acceso Denegado</h2>
                    <p>Lo sentimos, no tienes permiso para Acceder a esta Seccion</p>
                    <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Volver atrás
                    </button>
                </div>';
            exit;
        }
        $clientes = $this->clientes->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Clientes eliminadas', 'datos' => $clientes];
        
       
        echo view('header');
        echo view('clientes/eliminados', $data);
        echo view('footer');
    }

    public function nuevo()
    {
       
     
        $data = ['titulo' => 'Agregar cliente','unidades'];
        
        echo view('header');
        echo view('clientes/nuevo', $data);
        echo view('footer');
        
    }

    public function insertar()
    {
        if($this->request->getMethod()=="post" && $this->validate($this->reglas)){
        $this->clientes->save([
            'nombre' => $this->request->getPost('nombre'),
            'CI' => $this->request->getPost('CI'),
            'direccion' => $this->request->getPost('direccion'),
            'telefono' => $this->request->getPost('telefono'),
            'correo' => $this->request->getPost('correo')]);

        return redirect()->to(base_url().'/clientes');
        }else{
            
          
            $data = ['titulo' => 'Agregar cliente','validation' => $this->validator];

             echo view('header');
             echo view('clientes/nuevo', $data);
             echo view('footer');
        }
    }
  
    //findAll = todos los registro
    //first = buscar el primero 
    
    //FUNCIONES
    public function editar($id, $valid=null)
    {
        $cliente = $this->clientes->where('id',$id)->first();
        if($valid != null){
            $data = ['titulo' => 'Editar cliente', 'datos'=>$cliente, 'validation'=> $valid];
        }else{
            $data = ['titulo' => 'Editar cliente', 'datos'=>$cliente];
        }
        
        
        echo view('header');
        echo view('clientes/editar', $data);
        echo view('footer');
        
    }
 
    //FUNCION ACTUALIZAR

    public function actualizar()
    {
        // Obtener el ID del cliente que se está editando
        $id = $this->request->getPost('id');
        
        // Obtener los datos actuales del cliente
        $cliente = $this->clientes->where('id', $id)->first();
    
        // Verificar si el CI ha cambiado
        if ($this->request->getPost('CI') == $cliente['CI']) {
            // Si el CI no ha cambiado, no aplicar la regla 'is_unique'
            $this->reglas['CI']['rules'] = 'required';
        }
    
        // Validar el formulario y actualizar si es válido
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->clientes->update($id, [
                'nombre' => $this->request->getPost('nombre'),
                'CI' => $this->request->getPost('CI'),
                'direccion' => $this->request->getPost('direccion'),
                'telefono' => $this->request->getPost('telefono'),
                'correo' => $this->request->getPost('correo')
            ]);
            
            return redirect()->to(base_url() . '/clientes');
        } else {
            // Si hay errores de validación, volver a cargar la vista de edición
            return $this->editar($id, $this->validator);
        }
    }
    
    //FUNCION ELIMINAR
    public function eliminar($id)
    {
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_clientes');
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
        $this->clientes->update($id, ['activo' => 0]);

        return redirect()->to(base_url().'/clientes');
    }

    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        $this->clientes->update($id, ['activo' => 1]);

        return redirect()->to(base_url().'/clientes');
    }

    public function autocompleteData() {
        $returnData = array();
        $valor = $this->request->getGet('term');
    
        // Buscar por CI o nombre
        $clientes = $this->clientes
                         ->groupStart()
                            ->like('CI', $valor)
                            ->orLike('nombre', $valor)
                         ->groupEnd()
                         ->where('activo', 1)
                         ->findAll();
    
        if (!empty($clientes)) {
            foreach ($clientes as $row) {
                $data['id'] = $row['id'];
                $data['label'] = $row['CI'] . ' - ' . $row['nombre']; // Mostrar CI y nombre en la lista
                $data['value'] = $row['nombre']; // Insertar solo el nombre en el campo
                array_push($returnData, $data);
            }
        }
    
        echo json_encode($returnData);
    }
    
    
}
?>