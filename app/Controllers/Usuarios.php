<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuariosModel;
use App\Models\CajasModel;
use App\Models\RolesModel;
use App\Models\DetalleRolesPermisosModel;
use App\Models\EmpleadosModel;



class Usuarios extends BaseController
{
    protected $usuarios, $cajas, $roles,$empleados,$detalleRoles;
    protected $reglas, $reglasLogin, $reglasCambia;
    
    public function __construct()
    {
        $this->usuarios = new UsuariosModel();
        $this->cajas = new CajasModel();
        $this->roles = new RolesModel();
        $this->empleados = new EmpleadosModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        

        helper(['form']);
        $this->reglas = [
            'usuario' => [
                'rules' => 'required|is_unique[usuarios.usuario]',
                'errors' => [
                    'required' => 'El campo usuario es obligatorio.',
                    'is_unique' => 'Este usuario ya está registrado.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo contraseña es obligatorio.'
                ]
            ],
            'repassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'El campo confirmación de contraseña es obligatorio.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ],
            'id_empleado' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo empleado es obligatorio.'
                ]
            ],
            'id_caja' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo caja es obligatorio.'
                ]
            ],
            'id_rol' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo rol es obligatorio.'
                ]
            ]
        ];

        $this->reglasLogin = [
            'usuario' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo usuario es obligatorio.',
                    'is_unique' => 'Este usuario ya está registrado.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo contraseña es obligatorio.'
                ]
                ]
                ];

                $this->reglasCambia= [
                    'password' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'El campo password es obligatorio.',
                            
                        ]
                    ],
                    'repassword' => [
                        'rules' => 'required|matches[password]',
                        'errors' => [
                            'required' => 'El campo confirmación de contraseña es obligatorio.',
                            'matches' => 'Las contraseñas no coinciden.'
                        ]
                    ]
                        ];
    
    }
    
   

    public function index($activo = 1)
    {

      
    
        // Cargar el modelo de usuarios
        $usuariosModel = new UsuariosModel();
        
        // Obtener los usuarios activos en orden descendente
        $usuarios = $usuariosModel
                    ->where('activo', $activo)
                    ->orderBy('id', 'DESC') // Orden descendente
                    ->obtenerUsuarios();
        
        // Preparar los datos para la vista
        $data = ['titulo' => 'Usuarios', 'datos' => $usuarios];
        
        // Cargar las vistas con los datos
        echo view('header');
        echo view('usuarios/usuarios', $data);
        echo view('footer');
    }
    
    

    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_usuarios');
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
        $usuarios = $this->usuarios->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Usuarios eliminadas', 'datos' => $usuarios];
        
       
        echo view('header');
        echo view('usuarios/eliminados', $data);
        echo view('footer');
    }

    public function nuevo()
    {
        $cajas = $this->cajas->where('activo',1)->findAll();
        $roles = $this->roles->where('activo',1)->findAll();
        $empleados = $this->empleados->where('activo', 1)->findAll();
        $data = ['titulo' => 'Agregar usuario','cajas'=> $cajas, 'roles'=>$roles, 'empleados'=>$empleados]; 
        echo view('header');
        echo view('usuarios/nuevo', $data);
        echo view('footer');
        
    }



    public function insertar()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $passwordInput = $this->request->getPost('password');
            $hash = password_hash($passwordInput ?? '', PASSWORD_DEFAULT);
            $this->usuarios->save([
                'usuario' => $this->request->getPost('usuario'),
                'password' => $hash,
                'id_empleado' => $this->request->getPost('id_empleado'),
                'id_caja' => $this->request->getPost('id_caja'),
                'id_rol' => $this->request->getPost('id_rol'),
                'activo' => 1
            ]);
    
            return redirect()->to(base_url() . '/usuarios');
        } else {
            $cajas = $this->cajas->where('activo', 1)->findAll();
            $roles = $this->roles->where('activo', 1)->findAll();
            $empleados = $this->empleados->where('activo', 1)->findAll();
            $data = ['titulo' => 'Agregar usuario','cajas' => $cajas,'roles' => $roles,'empleados' => $empleados,
            'validation' => $this->validator
            ];
            echo view('header');
            echo view('usuarios/nuevo', $data);
            echo view('footer');
        }
    }
    
  


    //findAll = todos los registro
    //first = buscar el primero 
    
    // Función para cifrar la contraseña
    private function cifrarPassword($password)
    {
        // Genera un hash seguro de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        return $hashed_password;
    }
    //FUNCIONES
    public function editar($id, $valid = null)
    {
        $roles = $this->roles->where('activo', 1)->findAll();
        $cajas = $this->cajas->where('activo', 1)->findAll();
        $empleados = $this->empleados->where('activo', 1)->findAll();
        $usuario = $this->usuarios->where('id', $id)->first();
    
        if ($valid != null) {
            $data = [
                'titulo' => 'Editar usuario', 
                'datos' => $usuario, 
                'validation' => $valid, 
                'roles' => $roles,
                'cajas' => $cajas,
                'empleados' => $empleados

            ];
        } else {
            $data = [
                'titulo' => 'Editar usuario', 
                'datos' => $usuario, 
                'roles' => $roles,
                'cajas' => $cajas,
                'empleados' => $empleados
            ];
        }
    
        echo view('header');
        echo view('usuarios/editar', $data);
        echo view('footer');
    }
    
    //FUNCION ACTUALIZAR
    public function actualizar()
    {
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');
    
        // Datos que siempre se actualizan
        $data = [
            'usuario' => $this->request->getPost('usuario'),
            'id_empleado' => $this->request->getPost('id_empleado'),
            'id_rol' => $this->request->getPost('id_rol'),
            'id_caja' => $this->request->getPost('id_caja'),
            'id_empleado' => $this->request->getPost('id_empleado')
        ];
    
        // Si se ha proporcionado una nueva contraseña, la actualizamos
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT); // Hashea la nueva contraseña
        }
    
        $this->usuarios->update($id, $data);
    
        return redirect()->to(base_url('/usuarios'));
    }
    
    
    //FUNCION ELIMINAR
    public function eliminar($id)
    {
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '___Eliminar_usuarios');
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
        $this->usuarios->update($id, ['activo' => 0]);

        return redirect()->to(base_url().'/usuarios');
    }
    

    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        $this->usuarios->update($id, ['activo' => 1]);

        return redirect()->to(base_url().'/usuarios');
    }
    public function login(){
        echo view('login');
        
    }













    public function valida() {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglasLogin)) {
            $usuario = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');
            $datosUsuario = $this->usuarios->where('usuario', $usuario)->first();
    
            if ($datosUsuario != null && password_verify($password, $datosUsuario['password'])) {
                // Obtener el nombre del rol y los permisos asociados a este rol
                $nombreRol = $this->roles->where('id', $datosUsuario['id_rol'])->first()['nombre'];
                $permisos = $this->detalleRoles->select('permisos.nombre')
                    ->join('permisos', 'detalle_roles_permisos.id_permiso = permisos.id')
                    ->where('detalle_roles_permisos.id_rol', $datosUsuario['id_rol'])
                    ->findAll();
    
                // Extraer solo los nombres de los permisos en un array
                $listaPermisos = array_column($permisos, 'nombre');
    
                // Configurar datos de sesión
                $datosSesion = [
                    'id_usuario' => $datosUsuario['id'],
                    'usuario' => $datosUsuario['usuario'],
                    'id_empleado' => $datosUsuario['id_empleado'],
                    'id_caja' => $datosUsuario['id_caja'],
                    'id_rol' => $datosUsuario['id_rol'],
                    'rol' => $nombreRol,
                    'permisos' => $listaPermisos
                ];
    
                // Iniciar la sesión con los datos configurados
                $session = session();
                $session->set($datosSesion);
    
          
                return redirect()->to(base_url() . '/inicio');
            } else {
                $data['error'] = "Usuario o contraseña incorrectos";
                echo view('login', $data);
            }
        } else {
            $data = ['validation' => $this->validator];
            echo view('login', $data);
        }
    }
    












    
    public function logout(){
        $session=session();

        $ip= $_SERVER['REMOTE_ADDR'];
        $detalles=$_SERVER['HTTP_USER_AGENT'];

  

        $session->destroy();
        return redirect()->to(base_url());
    }
     public function cambia_password(){
        $session = session();
        $usuario = $this->usuarios->where('id', $session->id_usuario)->first();
        $data = ['titulo' => 'Cambiar contraseña','usuario'=> $usuario]; 
        echo view('header');
        echo view('usuarios/cambia_password', $data);
        echo view('footer');
        

     }
      public function actualizar_password(){
        if ($this->request->getMethod() == "post" && $this->validate($this->reglasCambia)) {
            $session = session();
            $idUsuario =$session->id_usuario;
            $passwordInput = $this->request->getPost('password');
            $hash = password_hash($passwordInput ?? '', PASSWORD_DEFAULT);

            $this->usuarios->update($idUsuario,['password' => $hash]);
         
            $usuario = $this->usuarios->where('id', $session->id_usuario)->first();
        $data = ['titulo' => 'Cambiar contraseña','usuario'=> $usuario,'mensaje'=>
            'Contraseña actualizada']; 
        echo view('header');
        echo view('usuarios/cambia_password', $data);
        echo view('footer');

        } else {
            $session = session();
            $usuario = $this->usuarios->where('id', $session->id_usuario)->first();
        $data = ['titulo' => 'Cambiar contraseña','usuario'=> $usuario, 'validation'=>
        $this->validator]; 
        echo view('header');
        echo view('usuarios/cambia_password', $data);
        echo view('footer');
        }


      }
  
}
