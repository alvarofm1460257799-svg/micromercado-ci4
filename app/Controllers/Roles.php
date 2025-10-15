<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RolesModel;
use App\Models\PermisosModel;
use App\Models\DetalleRolesPermisosModel;
use App\Models\UsuariosModel;



class Roles extends BaseController
{

    protected $roles, $permisos, $detalleRoles,$usuarios;
    /*variable para la validacion*/
    protected $reglas;

    public function __construct()
    {
        $this->roles = new RolesModel();
        $this->permisos = new PermisosModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->usuarios = new UsuariosModel();
        /*agregado para la validacion*/

        helper(['form']);
        $this->reglas = [
            'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];
    }

    public function index($activo = 1)
    {       

       
            $roles = $this->roles->where('activo', $activo)->findAll();
            $data = ['titulo' => 'Roles', 'datos' => $roles];

            echo view('header');
            echo view('roles/roles', $data);
            echo view('footer');
      
    }

    public function eliminados($activo = 0)
    {
        $roles = $this->roles->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Roles eliminados', 'datos' => $roles];

        echo view('header');
        echo view('roles/eliminados', $data);
        echo view('footer');
    }


    public function nuevo()
    {
        $data = ['titulo' => 'Agregar Rol'];

        echo view('header');
        echo view('roles/nuevo', $data);
        echo view('footer');
    }


    public function insertar()
    {
        if($this->request->getMethod()=="post" && $this->validate($this->reglas)){
        $this->roles->save(['nombre' => $this->request->getPost('nombre')]);
        

        return redirect()->to(base_url().'/roles');
    }else{
        $data =['titulo'=> 'Agregar rol', 'validation' => $this->validator];
        echo view ('header');
        echo view ('roles/nuevo', $data);
        echo view('footer');
    }
    }
  

    public function editar($id, $valid = null)
    {
        $rol = $this->roles->where('id', $id)->first();
        /*if de validacion*/
        if ($valid != null) {
            $data = ['titulo' => 'Editar rol', 'datos' => $rol, 'validation' =>  $valid];
        } else {
            $data = ['titulo' => 'Editar rol', 'datos' => $rol];
        }
        echo view('header');
        echo view('roles/editar', $data);
        echo view('footer');
    }
    public function actualizar()
    {
        $id = $this->request->getPost('id');
    

    
        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {
            $this->roles->update($id,  [
                'nombre' => $this->request->getPost('nombre')
             
            ]);
            
            return redirect()->to(base_url() . '/roles');
        } else {
            // Si hay errores de validación, volver a cargar la vista de edición
            return $this->editar($id, $this->validator);
        }
    }
    




    /*------------------------------------------------------------------*/
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
         $UsuariosAsociados = $this->usuarios->where('id_rol', $id)->findAll();
     
         if (!empty($UsuariosAsociados)) {
             // Si hay productos asociados, impedir la eliminación y mostrar un mensaje
             echo '<div style="text-align:center; margin-top: 50px;">
                     <h2 style="color: #e74c3c;">Error al eliminar</h2>
                     <p>No se puede eliminar este Rol porque tiene Usuarios asociados.</p>
                     <button onclick="window.history.back()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                         Volver atrás
                     </button>
                   </div>';
             exit;
         }
     
     


        $this->roles->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/roles');
    }
    /*------------------------------------------------------------------*/
    public function reingresar($id)
    {
        $this->roles->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/roles');
    }
    public function detalles($idRol) {



        // Verifica si la variable se establece correctamente
        $permisos = $this->permisos->findAll();
        $permisosAsignados= $this->detalleRoles->where('id_rol',$idRol)->findAll();
        $datos=array();

     


     foreach($permisosAsignados as $permisosAsignado){
        $datos[$permisosAsignado['id_permiso']]=true;
        
       }

        if ($permisos === false) {
            $permisos = []; // Maneja el caso en que no se obtienen resultados
        }
      

      
        
        $data = [
            'titulo' => 'Asignar permisos',
            'permisos' => $permisos,'id_rol'=>$idRol, 'asignado'=>$datos
        ];
    
        echo view('header');
        echo view('roles/detalles', $data);
        echo view('footer');
    }








    public function guardaPermisos()
    {
        if ($this->request->getMethod() == "post") {
            $idRol = $this->request->getPost('id_rol');
            $permisos = $this->request->getPost('permisos');
    
            // Eliminar permisos actuales del rol en la base de datos
            $this->detalleRoles->where('id_rol', $idRol)->delete();
    
            // Guardar los nuevos permisos seleccionados
            foreach ($permisos as $permiso) {
                $this->detalleRoles->save(['id_rol' => $idRol, 'id_permiso' => $permiso]);
            }
    
            // Actualizar los permisos en la sesión
            $permisosActualizados = $this->detalleRoles->select('permisos.nombre')
                ->join('permisos', 'detalle_roles_permisos.id_permiso = permisos.id')
                ->where('detalle_roles_permisos.id_rol', $idRol)
                ->findAll();
    
            // Extraer solo los nombres de los permisos en un array
            $listaPermisosActualizados = array_column($permisosActualizados, 'nombre');
    
            // Actualizar los permisos en la sesión del usuario
            $session = session();
            $session->set('permisos', $listaPermisosActualizados);
    
            // Redirigir de nuevo a la página de roles
            return redirect()->to(base_url() . "/usuarios/logout")->with('success', 'Permisos actualizados correctamente');
        }
    }
    
    
}
