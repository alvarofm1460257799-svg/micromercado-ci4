<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/cajas/nuevo_arqueo" class="btn 
                    btn-info">Agregar</a>

                    <a href="<?php echo base_url();?>/cajas/eliminados" class="btn 
                    btn-warning">Eliminados</a>

                    <a href="<?php echo base_url(); ?>/cajas" class="btn 
                    btn-warning">Volver</a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Id</th>
                                <th>Fecha apertura</th>
                                <th>Fecha cierre</th>
                                <th>Monto inicial Bs</th>
                                <th>Monto final Bs</th>
                                <th>Total Ventas Bs</th>
                                <th>Usuario</th>
                                <th style="width: 5%;">Estado</th>
                                <th style="width: 10%;">Opcion</th>
                  
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                            <tr>
                                    <td ><?php echo $dato['id']; ?></td>
                                    <td ><?php echo $dato['fecha_inicio']; ?></td>
                                    <td ><?php echo $dato['fecha_fin']; ?></td>
                                    <td ><?php echo $dato['monto_inicial']; ?></td>
                                    <td ><?php echo $dato['monto_final']; ?></td>
                                    <td ><?php echo $dato['total_ventas']; ?></td>
                                    <td ><?php echo $dato['nombre']; ?></td>
                                    

                                    <?php if ($dato['estatus']==1) {?> 
                                        <td>Abierta</td>
                                        <td ><a href="#" data-href="<?php echo base_url() . '/cajas/cerrar/' . $dato['id']; ?>" data-bs-toggle="modal" 
                                        data-bs-target="#modal-confirma" data-placement="top" title="Eliminar Registro" class="btn btn-danger btn-sm"><i class="fas fa-lock"></i> Cerrar</a>
                                        </td>
                                        <?php } else {  ?>
                                            <td>Cerrado</td>
                                            
                                            <td>
                                        <a href="<?php echo base_url() . '/cajas/generarDatosArqueo/' . $dato['id']; ?>" class="btn btn-primary" 
                                        data-placement="top" title="Generar Reporte">Imprimir <i class="fas fa-print"></i></a>
                                        </td>

                                   <?php } ?>

                                   

                                </tr>

                            <?php } ?>
                            
                        </tbody>
                    </table>
                </div>
                </div>
                </div>
                </div>
    </main>
    
    <!-- Modal ventanita para mostrar si va elminar o no-->
    <div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CERRAR LA CAJA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Desea Cerrar la Caja?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-linght" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-linght" data-bs-dismiss="modal">no</button>
                    <a class="btn btn-danger btn-ok" id="btn-ok">SI</a>
                </div>
            </div>
        </div>
    </div>
    