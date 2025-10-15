<div id="layoutSidenav_content">
    <main>
    <?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= session()->getFlashdata('error'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '<?= session()->getFlashdata('success'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>

        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
               

                    <a href="<?php echo base_url();?>/ventas/eliminados" class="btn 
                    btn-warning">Eliminados</a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Cajero</th>
                                <th>Recibo</th>
                                <th>Opcion</th>
                                
                          
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    
                                    <td><?php echo $dato['fecha_alta']; ?></td>
                                    <td><?php echo $dato['folio']; ?></td>
                                    <td><?php echo $dato['cliente']; ?></td>
                                    <td><?php echo $dato['total']; ?></td>
                                    <td><?php echo $dato['cajero']; ?></td>

                           
                                    <td> <a href="<?php echo base_url() . '/ventas/muestraTicket/' . $dato['id']; ?>" class="btn btn-primary" 
                                    data-placement="top" title="Editar Registro">Detalles <i class="fas fa-list-alt"></i></a>
                                    </td>

                                    <td><a href="#" data-href="<?php echo base_url() . '/ventas/eliminar/' . $dato['id']; ?>" data-bs-toggle="modal" 
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
    