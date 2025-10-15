<?php

namespace App\Controllers;
use App\Models\UsuariosModel;
use App\Controllers\BaseController;
use App\Models\EmpleadosModel;
use App\Models\DetalleRolesPermisosModel;

class Empleados extends BaseController
{
    protected $empleados,$detalleRoles,$usuarios;
    protected $reglas;

    public function __construct()
    {
        $this->empleados = new EmpleadosModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->usuarios = new UsuariosModel();
        helper(['form']);
        $this->reglas = [
            'ci' => [
                'rules' => 'required|is_unique[empleados.ci]|min_length[6]|max_length[10]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser único.',
                    'min_length' => 'El campo {field} debe tener al menos 6 dígitos.',
                    'max_length' => 'El campo {field} no debe exceder los 10 dígitos.'
                ]
            ],
            'nombres' => [
                'rules' => 'required|alpha_space|min_length[2]|max_length[25]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'alpha_space' => 'El campo {field} solo debe contener letras y espacios.',
                    'min_length' => 'El campo {field} debe tener al menos 2 caracteres.',
                    'max_length' => 'El campo {field} no debe exceder los 25 caracteres.'
                ]
            ],
            'ap' => [
                'rules' => 'required|alpha_space|max_length[25]',
                'errors' => [
                    'required' => 'El campo Apellido Paterno es obligatorio.',
                    'alpha_space' => 'El campo Apellido Paterno solo debe contener letras y espacios.',
                    'max_length' => 'El campo Apellido Paterno no debe exceder los 25 caracteres.'
                ]
            ],
            'am' => [
                'rules' => 'required|alpha_space|max_length[25]',
                'errors' => [
                    'required' => 'El campo Apellido Materno es obligatorio.',
                    'alpha_space' => 'El campo Apellido Materno solo debe contener letras y espacios.',
                    'max_length' => 'El campo Apellido Materno no debe exceder los 25 caracteres.'
                ]
            ],
            'cel_ref' => [
                'rules' => 'required|numeric|min_length[8]|max_length[10]',
                'errors' => [
                    'required' => 'El campo Celular Referencia es obligatorio.',
                    'numeric' => 'El campo Celular Referencia solo debe contener números.',
                    'min_length' => 'El campo Celular Referencia debe tener al menos 8 dígitos.',
                    'max_length' => 'El campo Celular Referencia no debe exceder los 10 dígitos.'
                ]
            ],
            'direccion' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'max_length' => 'El campo {field} no debe exceder los 50 caracteres.'
                ]
            ],
            'genero' => [
                'rules' => 'required|in_list[Masculino,Femenino]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'in_list' => 'El campo {field} debe ser Masculino o Femenino.'
                ]
            ]
        ];
        
    }

    public function index($activo = 1)
    {   
      
        $empleados = $this->empleados
                          ->where('activo', $activo)
                          ->orderBy('id', 'DESC') // Orden descendente
                          ->findAll();
    
        $data = ['titulo' => 'Personal', 'datos' => $empleados];
        
        echo view('header');
        echo view('empleados/empleados', $data);
        echo view('footer');
    }
    

    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {
        $empleados = $this->empleados->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Empleados Eliminados', 'datos' => $empleados];
        
       
        echo view('header');
        echo view('empleados/eliminados', $data);
        echo view('footer');
    }

    public function nuevo()
    {
        $data = ['titulo' => 'Agregar empleado'];
        
        echo view('header');
        echo view('empleados/nuevo', $data);
        echo view('footer');
        
    }

    public function insertar()
    {
        if($this->request->getMethod()=="post" && $this->validate($this->reglas)){
        $this->empleados->save([
        'ci' => $this->request->getPost('ci'),
        'nombres' => $this->request->getPost('nombres'),
        'ap' => $this->request->getPost('ap'),
        'am' => $this->request->getPost('am'),
        'cel_ref' => $this->request->getPost('cel_ref'),
        'direccion' => $this->request->getPost('direccion'),
        'genero' => $this->request->getPost('genero')


        ]);
        

        return redirect()->to(base_url().'/empleados');
    }else{
        $data =['titulo'=> 'Agregar empleado', 'validation' => $this->validator];
        echo view ('header');
        echo view ('empleados/nuevo', $data);
        echo view('footer');
    }
    }
  
    //findAll = todos los registro
    //first = buscar el primero 
    
    //FUNCIONES
    public function editar($id, $valid=null)
    {
        $empleado = $this->empleados->where('id',$id)->first();
        if($valid != null){
            $data = ['titulo' => 'Editar empleado', 'datos'=>$empleado, 'validation'=> $valid];
        }else{
            $data = ['titulo' => 'Editar empleado', 'datos'=>$empleado];
        }
        
        echo view('header');
        echo view('empleados/editar', $data);
        echo view('footer');
        
    }
    //FUNCION ACTUALIZAR
   

    public function actualizar()
    {
        // Obtener el ID del empleado que se está editando
        $id = $this->request->getPost('id');
        
        // Obtener los datos actuales del empleado
        $empleado = $this->empleados->where('id', $id)->first();
    
        // Verificar si el ci ha cambiado
        if ($this->request->getPost('ci') == $empleado['ci']) {
            // Si el ci no ha cambiado, no aplicar la regla 'is_unique'
            $this->reglas['ci']['rules'] = 'required';
        }
    
        // Validar el formulario y actualizar si es válido
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->empleados->update($id, [
                'ci' =>$this->request->getPost('ci'),
                'nombres' =>$this->request->getPost('nombres'),
                'ap' => $this->request->getPost('ap'),
                'am' => $this->request->getPost('am'),
                'cel_ref' => $this->request->getPost('cel_ref'),
                'direccion' => $this->request->getPost('direccion'),
                'genero' => $this->request->getPost('genero')
            ]);
            
            return redirect()->to(base_url() . '/empleados');
        } else {
            // Si hay errores de validación, volver a cargar la vista de edición
            return $this->editar($id, $this->validator);
        }
    }
    
    //FUNCION ELIMINAR
    public function eliminar($id)
    {
               // Verificación de permisos
               $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_usuarios');
               if (!$permiso) {
                   echo '<div style="text-align:center; margin-top: 50px;">
                           <h2 style="color: #e74c3c;">Acceso Denegado</h2>
                           <p>Lo sentimos, no tienes permiso para Eliminar Roles.</p>
                           <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                               Volver atrás
                           </button>
                         </div>';
                   exit;
               }
           

     
    
        // Verificar si hay productos asociados a este proveedor
        $UsuariosAsociados = $this->usuarios->where('id_empleado', $id)->findAll();

    
        if (!empty($UsuariosAsociados)) {
            // Si hay productos asociados, impedir la eliminación y mostrar un mensaje
            echo '<div style="text-align:center; margin-top: 50px;">
                    <h2 style="color: #e74c3c;">Error al eliminar</h2>
                    <p>No se puede eliminar este Empleado porque tiene Usuarios asociados.</p>
                    <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Volver atrás
                    </button>
                  </div>';
            exit;
        }
        
        $this->empleados->update($id, ['activo' => 0]);

        return redirect()->to(base_url().'/empleados');
    }

    
    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        $this->empleados->update($id, ['activo' => 1]);

        return redirect()->to(base_url().'/empleados');
    }
}
?>