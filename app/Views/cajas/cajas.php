<div id="layoutSidenav_content">
    <main>
        
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/cajas/nuevo" class="btn 
                    btn-info">Agregar</a>

                    <a href="<?php echo base_url();?>/cajas/eliminados" class="btn 
                    btn-warning">Eliminados</a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th  style="width: 5%;">Id</th>
                                <th style="width: 10%;">Numero Caja</th>
                                <th>Nombre</th>
                                <th>Folio</th>
                                <th style="width: 10%;">Arqueo de Caja</th>
                                <th style="width: 10%;">Opciones</th>
              
                                
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                            <tr>
                                    <td ><?php echo $dato['id']; ?></td>
                                    <td ><?php echo $dato['numero_caja']; ?></td>
                                    <td ><?php echo $dato['nombre']; ?></td>
                                    <td ><?php echo $dato['folio']; ?></td>

                                    <td> 
                                     <a href="<?php echo base_url() . '/cajas/arqueo/' . $dato['id']; ?>" class="btn btn-primary btn-sm" 
                                    data-placement="top" title="Editar Registro">Ver Detalles <i class="fas fa-clipboard-list"></i></a>
                                    </td>
                                    <td class="even"> <a href="<?php echo base_url() . '/cajas/editar/' . $dato['id']; ?>" class="btn btn-warning btn-sm" 
                                    data-toggle="tooltip" data-placement="top" title="Editar Registro">Editar<i class="fa-solid fa-pen-to-square"></i></a>
                               
                                    <br><br>
                                    <a href="#" data-href="<?php echo base_url() . '/cajas/eliminar/' . $dato['id']; ?>" 
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
    