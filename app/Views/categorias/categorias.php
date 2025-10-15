<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/categorias/nuevo" class="btn 
                    btn-info"><i class="fa-solid fa-circle-plus"></i> Agregar</a>

                    <a href="<?php echo base_url();?>/categorias/eliminados" class="btn 
                    btn-warning"><i class="fa-solid fa-delete-left"></i> Eliminados</a>
                     <!-- Nuevo botón Importar -->
                    <a href="<?php echo base_url();?>/categorias/importarVista" class="btn btn-success">
                        <i class="fa-solid fa-file-import"></i> Importar
                    </a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Id</th>
                                <th>Categorizacion</th>
                                 <th>Días de aviso de vencimiento</th>
                                <th style="width: 10%;">Listar productos</th>
                                <th style="width: 10%;">Modificar</th>
                                <th style="width: 10%;">Eliminar</th>
                         
                                
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    <td ><?php echo $dato['id']; ?></td>
                                    <td><?php echo $dato['nombre']; ?></td>
                                    <td><?php echo $dato['dias_aviso']; ?></td>
                                    
                                    <td > 
                               
                                    <a href="<?php echo base_url() . '/categorias/listar_producto/' . $dato
                                    ['id']; ?>" class="btn btn-primary btn-sm" data-placement="top" title="Editar Registro">Detalles <i class="fas fa-list-alt"></i></a>
                                    </td>
                                    <td > <a href="<?php echo base_url().'/categorias/editar/'. $dato
                                    ['id']; ?>" class="btn btn-warning btn-sm">Editar <i class="fas fa-pencil-alt"></i></a>
                                     </td>
                                     <td >
                                    <a href="#" data-href="<?php echo base_url() . '/categorias/eliminar/' . $dato['id']; ?>" 
                                    data-bs-toggle="modal" data-bs-target="#modal-confirma" data-placement="top" title="Eliminar Registro" class="btn btn-danger btn-sm">
                                    Borrar <i class="fa-regular fa-trash-can"></i></a>  </td>

                                    
                                    
                                    
                                    
                                    
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
                    <a class="btn btn-danger btn-ok" id="btn-ok">SI</a>
                </div>
            </div>
        </div>
    </div>