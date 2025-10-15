<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid ">

                        <p></p>
            <div class="card mb-1">
                <div class="card-body">

                    <h5 style="position: absolute; top:15%;" class="mt"><?php echo $titulo; ?></h5>
                </div>
            </div>
            <div class="card mb-4">
            <div class="card-header">
                    <p>
                        <a href="<?php echo base_url(); ?>/roles/nuevo" class="btn 
                    btn-info"><i class="fa-solid fa-plus"></i> Agregar</a>
                        <a href="<?php echo base_url(); ?>/roles/eliminados" class="btn 
                    btn-warning">Eliminados</a>
                    </p>
                </div>
           
                <div class="card-body">
                    <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead class="title_atributo">
                            <tr>
                                <th style="width: 5%;">Id</th>
                                
                                <th style="width: 30%;">Nombre</th>
                                <th style="width: 5%;">Administrar</th>
                                <th style="width: 5%;">Opciones</th>
                      
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($datos as $dato) { ?>
                                <tr>
                                    <td><?php echo $dato['id']; ?></td>
                                    <td><?php echo $dato['nombre']; ?></td>

                                    <td> 
                                    <br>   
                                    <a href="<?php echo base_url() . '/roles/detalles/' . $dato['id']; ?>" class="btn btn-primary btn-sm" 
                                    data-placement="top" title="Editar Registro">Acessos <i class="fas fa-list-alt"></i></a>
                                    </td>
                                    <td> <a href="<?php echo base_url() . '/roles/editar/' . $dato['id']; ?>" class="btn btn-warning btn-sm" 
                                    data-placement="top" title="Editar Registro">Editar <i class="fas fa-pencil-alt"></i></a>
                                    <br> <br>

                                   <a href="#" data-href="<?php echo base_url() . '/roles/eliminar/' . $dato['id']; ?>" 
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
                    <a class="btn btn-danger btn-ok" id="btn-ok">SI</a>
                </div>
            </div>
        </div>
    </div>