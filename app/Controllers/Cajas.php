<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CajasModel;
use App\Models\ArqueoCajaModel;
use App\Models\VentasModel;
use App\Models\DetalleRolesPermisosModel;

class Cajas extends BaseController
{
    protected $cajas, $arqueoModel, $ventasModel,$detalleRoles;
    protected $reglas;

    public function __construct()
    {
        $this->cajas = new CajasModel();
        $this->arqueoModel = new ArqueoCajaModel();
        $this->ventasModel = new VentasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        helper(['form']);
        $this->reglas=[
        'numero_caja' => [
        'rules' => 'required',
        'errors' => [
            'required'=>'El campo numero caja es obligatorio.'
        ]
        ],
        'nombre' => [
        'rules' => 'required',
        'errors' => [
            'required'=>'El campo {field} es obligatorio.'
        ]
        ],
        
        'folio' => [
            'rules' => 'required',
            'errors' => [
                'required'=>'El campo {field} es obligatorio.'
            ]
            ]
    ];
    }

    public function index($activo = 1)
    {
        
        $cajas = $this->cajas->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Cajas', 'datos' => $cajas];
        
        echo view('header');
        echo view('cajas/cajas', $data);
        echo view('footer');
        
    }

    //TABLA ELIMINADOS
    public function eliminados($activo = 0)
    {
        $cajas = $this->cajas->where('activo',$activo)->findAll();
        $data = ['titulo' => 'Cajas eliminadas', 'datos' => $cajas];
        
       
        echo view('header');
        echo view('cajas/eliminados', $data);
        echo view('footer');
    }

    public function nuevo()
    {
        $data = ['titulo' => 'Agregar caja'];
        
        echo view('header');
        echo view('cajas/nuevo', $data);
        echo view('footer');
        
    }

    public function insertar()
    {
        if($this->request->getMethod()=="post" && $this->validate($this->reglas)){
        $this->cajas->save([
        'numero_caja' => $this->request->getPost('numero_caja'),
        'nombre' => $this->request->getPost('nombre'),
        'folio' => $this->request->getPost('folio')]);
        

        return redirect()->to(base_url().'/cajas');
    }else{
        $data =['titulo'=> 'Agregar caja', 'validation' => $this->validator];
        echo view ('header');
        echo view ('cajas/nuevo', $data);
        echo view('footer');
    }
    }
  
    //findAll = todos los registro
    //first = buscar el primero 
    
    //FUNCIONES
    public function editar($id, $valid=null)
    {
        $caja = $this->cajas->where('id',$id)->first();
        if($valid != null){
            $data = ['titulo' => 'Editar caja', 'datos'=>$caja, 'validation'=> $valid];
        }else{
            $data = ['titulo' => 'Editar caja', 'datos'=>$caja];
        }
        
        echo view('header');
        echo view('cajas/editar', $data);
        echo view('footer');
        
    }
    //FUNCION ACTUALIZAR
    public function actualizar()
    {
        if($this->request->getMethod()=="post" && $this->validate($this->reglas)){
        $this->cajas->update($this->request->getPost('id'),[
        'numero_caja' =>$this->request->getPost('numero_caja'),
        'nombre' =>$this->request->getPost('nombre'),
        'folio' => $this->request->getPost('folio')]);
        
        return redirect()->to(base_url().'/cajas');
        }else{
            return $this->editar($this->request->getPost('id'),$this->validator);
        }
    }
    
    //FUNCION ELIMINAR
    public function eliminar($id)
    {
        $this->cajas->update($id, ['activo' => 0]);

        return redirect()->to(base_url().'/cajas');
    }

    //FUNCION ELIMINAR
    public function reingresar($id)
    {
        $this->cajas->update($id, ['activo' => 1]);

        return redirect()->to(base_url().'/cajas');
    }

    public function arqueo($idCaja,$activo_usuario=1, $activo_caja=1){
        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, 'ArqueoCaja');
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
        $arqueos =$this->arqueoModel->getDatos($idCaja,$activo_usuario, $activo_caja);
        $data=['titulo'=>'Cierre de cajas','datos' =>$arqueos];
        echo view('header');
        echo view('cajas/arqueos',$data);
        echo  view('footer');
    
    }

    public function nuevo_arqueo(){
        $session=session();
        $existe =$this->arqueoModel->where(['id_caja'=>$session->id_caja,'estatus'=>1])->countAllResults();
        if($existe >0){
            echo '<div style="color: red; font-weight: bold; text-align: center; padding: 10px; border: 1px solid red; border-radius: 5px; background-color: #ffe6e6;">';
            echo 'La caja ya está abierta. Por favor, cierra la caja antes de realizar un nuevo arqueo.';
            echo '</div>';
            echo '<div style="text-align: center; margin-top: 20px;">';
            echo '<a href="javascript:history.back()" class="btn btn-secondary btn-sm" 
                   style="background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; 
                          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);" 
                   data-placement="top" title="Volver">';
            echo '<i class="fas fa-arrow-left"></i> Volver';
            echo '</a>';
            echo '</div>';
            exit;
        }
        if($this->request->getMethod()=="post"){
            $fecha= date('Y-m-d:h:i:s');
            $existe=0;
            $this->arqueoModel->save([
            'id_caja'=> $session->id_caja, 
            'id_usuario'=> $session->id_usuario,
            'fecha_inicio'=>$fecha,
            'monto_inicial'=> $this->request->getPost('monto_inicial'),'estatus'=>1]);
            return redirect()->to(base_url().'/cajas');

        }else{
            $caja=$this->cajas->where('id',$session->id_caja)->first();
            $data=['titulo'=> 'Apertura de Caja','caja'=> $caja,'session'=>$session];
            echo view('header');
            echo view('cajas/nuevo_arqueo',$data);
            echo  view('footer');
        

        }

    }
    public function cerrar(){
        $session=session();
        
      
        if($this->request->getMethod()=="post"){
            $fecha= date('Y-m-d H:i:s');
            
            $this->arqueoModel->update($this->request->getPost('id_arqueo'),[
            'fecha_fin'=>$fecha,
            'monto_final'=> $this->request->getPost('monto_final'),
            'total_ventas'=> $this->request->getPost('total_ventas'),'estatus'=>0]);
            
            return redirect()->to(base_url().'/cajas');

        }else{
            $montoTotal=$this->ventasModel->totalDia(date('Y-m-d'));
            $CantidadVentas=$this->ventasModel->CantidadVentasDia(date('Y-m-d'));
            $arqueo=$this->arqueoModel->where(['id_caja'=>$session->id_caja,'estatus'=>1])->first();
            $caja=$this->cajas->where('id',$session->id_caja)->first();
            $data=['titulo'=> 'Cerrar de Caja',
            'caja'=> $caja,'session'=>$session, 
            'arqueo'=>$arqueo,
            'monto'=>$montoTotal,
            'cantidadVenta'=>$CantidadVentas
        ];
            echo view('header');
            echo view('cajas/cerrar',$data);
            echo  view('footer');
        

        }

    }
    public function mostrarArqueos($id) {
        $datosArqueo = $this->cajas->getDatosArqueo($id);  // Llamamos al método con el ID
    
        // Pasamos los datos a la vista
        $data['arqueo'] = $datosArqueo;
    
        echo view('header');
        echo view('cajas/ver_arqueo', $data);  // Pasamos los datos a la vista
        echo view('footer');
    }
    

    public function generarDatosArqueo($id_arqueo_caja) {
        // Instancia del modelo
        $model = new \App\Models\ArqueoCajaModel();
        
        // Obtener los datos del arqueo específico
        $datosCajas = $model->getDatosArqueo($id_arqueo_caja);
    
        // Verificar si se obtuvieron datos
        if (empty($datosCajas)) {
            // Si no hay datos, puedes manejarlo de alguna manera, como lanzar una excepción o mostrar un mensaje
            throw new \Exception("No se encontraron datos para el arqueo de caja con ID: " . $id_arqueo_caja);
        }
    
        $pdf = new \FPDF('L', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Arqueo de caja");
        $pdf->SetFont("Arial", 'B', 10);
        $pdf->Image("images/logotipo.png", 10, 5, 20);
        $pdf->Cell(0, 5, utf8_decode("REPORTE DE ARQUEOS DE CAJA"), 0, 1, 'C');
        $pdf->Ln(10);
    
        $pdf->Cell(30, 5, utf8_decode("Nombre Caja"), 1, 0, 'C');
        $pdf->Cell(40, 5, utf8_decode("Nombre Usuario"), 1, 0, 'C');
        $pdf->Cell(35, 5, utf8_decode("Monto inicial Bs"), 1, 0, 'C');
        $pdf->Cell(35, 5, utf8_decode("Monto final Bs"), 1, 0, 'C');
        $pdf->Cell(35, 5, utf8_decode("Total Ventas Bs"), 1, 0, 'C');
        $pdf->Cell(44, 5, utf8_decode("Fecha de inicio"), 1, 0, 'C');
        $pdf->Cell(44, 5, utf8_decode("Fecha Fin"), 1, 1, 'C');
    
        foreach ($datosCajas as $caja) {
            $pdf->Cell(30, 5, utf8_decode($caja["nombre_caja"]), 1, 0, 'C');
            $pdf->Cell(40, 5, utf8_decode($caja["nombre_usuario"]), 1, 0, 'C');
            $pdf->Cell(35, 5, $caja["monto_inicial"], 1, 0, 'C');
            $pdf->Cell(35, 5, $caja["monto_final"], 1, 0, 'C');
            $pdf->Cell(35, 5, $caja["total_ventas"], 1, 0, 'C');
            $pdf->Cell(44, 5, $caja["fecha_inicio"], 1, 0, 'C');
            $pdf->Cell(44, 5, $caja["fecha_fin"], 1, 1, 'C');
        }
    
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('ProductoMinimo.pdf', 'I');
    }
    
    




}

?>