<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/empleados/nuevo" class="btn 
                    btn-info"><i class="fa-solid fa-circle-plus"></i> Agregar</a>

                    <a href="<?php echo base_url();?>/empleados/eliminados" class="btn 
                    btn-warning"><i class="fa-solid fa-delete-left"></i> Eliminados</a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Id</th>
                                <th>Nombres</th>
                                <th>Apellido Paterno</th>
                                <th>Apellido Materno</th>
                                <th style="width: 10%;">CI</th>
                                <th style="width: 7%;">Celular Referencia</th>
                                <th style="width: 20%;">Direccion</th>
                                <th>Genero</th>
                                <th style="width: 5%;">Opciones</th>
                               
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                            <tr>
                                    <td ><?php echo $dato['id']; ?></td>
                                    <td ><?php echo $dato['nombres']; ?></td>
                                    <td ><?php echo $dato['ap']; ?></td>
                                    <td ><?php echo $dato['am']; ?></td>
                                    <td ><?php echo $dato['ci']; ?></td>
                                    <td ><?php echo $dato['cel_ref']; ?></td>
                                    <td ><?php echo $dato['direccion']; ?></td>
                                    <td ><?php echo $dato['genero']; ?></td>
                                  

                                    <td >
                                         <a href="<?php echo base_url() . '/empleados/editar/' . $dato['id']; ?>" class="btn btn-warning btn-sm" 
                                         data-toggle="tooltip" data-placement="top" title="Editar Registro">Editar <i class="fas fa-pencil-alt"></i></a>
                                        <br><br>

                                        <a href="#" data-href="<?php echo base_url() . '/empleados/eliminar/' . $dato['id']; ?>" 
                                        data-bs-toggle="modal" data-bs-target="#modal-confirma" data-placement="top" title="Eliminar Registro" class="btn btn-danger btn-sm">
                                        Borrar <i class="fa-regular fa-trash-can"></i></a>
                                    </td>

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
                    <h5 class="modal-title" id="exampleModalLabel">Eliminar registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Desea eliminar este registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-linght" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-linght" data-bs-dismiss="modal">no</button>
                    <a class="btn btn-danger btn-ok" id="btn-ok">SI</a>
                </div>
            </div>
        </div>
    </div>
    