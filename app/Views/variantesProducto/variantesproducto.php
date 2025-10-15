<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/variantesproducto/nuevo" class="btn btn-info">
                        <i class="fa-solid fa-circle-plus"></i> Agregar
                    </a>

                    <a href="<?php echo base_url();?>/variantesproducto/eliminados" class="btn btn-warning">
                        <i class="fa-solid fa-delete-left"></i> Eliminados
                    </a>
                     <!-- 🔽 Nuevo botón que redirige a la vista de importación -->
                            <a href="<?php echo base_url(); ?>/variantesproducto/importar" class="btn btn-success">
                                <i class="fa-solid fa-file-excel"></i> Importar Excel
                            </a>
                </p>
            </div>

            <div class="card-body">
                <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">Id</th>
                            <th>Producto</th>
                            <th>Código de barras</th>
                            <th>Descripción</th>
                            <th style="width: 10%;">Modificar</th>
                            <th style="width: 10%;">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($datos as $dato){ ?>
                            <tr>
                                <td><?php echo $dato['id']; ?></td>
                                <td>
                                    <?php 
                                        // Obtener nombre del producto asociado si está cargado
                                        echo isset($dato['producto_nombre']) ? $dato['producto_nombre'] : '';
                                    ?>
                                </td>
                                <td><?php echo $dato['codigo_barra']; ?></td>
                                <td><?php echo $dato['descripcion']; ?></td>
                                
                                <td>
                                    <a href="<?php echo base_url().'/variantesproducto/editar/'. $dato['id']; ?>" 
                                       class="btn btn-warning btn-sm">Editar <i class="fas fa-pencil-alt"></i></a>
                                </td>
                                <td>
                                    <a href="#" data-href="<?php echo base_url() . '/variantesproducto/eliminar/' . $dato['id']; ?>" 
                                       data-bs-toggle="modal" data-bs-target="#modal-confirma" class="btn btn-danger btn-sm">
                                       Borrar <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Desea eliminar este registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <a class="btn btn-danger btn-ok" id="btn-ok">SI</a>
                </div>
            </div>
        </div>
    </div>
</div>
