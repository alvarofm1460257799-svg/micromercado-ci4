<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/usuarios/nuevo" class="btn 
                    btn-info">Agregar</a>

                    <a href="<?php echo base_url();?>/usuarios/eliminados" class="btn 
                    btn-warning">Eliminados</a>
                </p>
            </div>

            <div class="">    
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Caja</th>
                                <th>Opciones</th>
                     
                                
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    <td style="width: 5%;"><?php echo $dato['id']; ?></td>
                                    <td><?php echo $dato['usuario']; ?></td>
                                    <td><?php echo $dato['empleado']; ?></td>
                                    <td><?php echo $dato['rol']; ?></td>
                                    <td><?php echo $dato['caja']; ?></td>

                         
                                     
                                    <td style="width: 5%;"> <a href="<?php echo base_url() . '/usuarios/editar/' . $dato['id']; ?>" class="btn btn-warning btn-sm" 
                                    data-placement="top" title="Editar Registro">Editar <i class="fa-solid fa-pen-to-square"></i></a>
                               
                                    <br> <br>
                                     <a href="#" data-href="<?php echo base_url() . '/usuarios/eliminar/' . $dato['id']; ?>" data-bs-toggle="modal" 
                                    data-bs-target="#modal-confirma" data-placement="top" title="Eliminar Registro" class="btn btn-danger btn-sm">
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
    