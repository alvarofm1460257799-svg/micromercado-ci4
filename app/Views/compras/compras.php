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

        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                  

                    <a href="<?php echo base_url();?>/compras/eliminados" class="btn 
                    btn-warning">Eliminados</a>
                 
                    <a href="<?= base_url('/compras/importarVista') ?>" class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Importar Compra Masiva
                </a>



               

                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                               
                                <th>Folio</th>
                                <th>Total</th>
                                <th>Fecha y Hora</th>
                                <th>Proveedor</th>
                                <th>Telefono</th>
                                <th>Recibo</th>
                                <th>Opcion</th>

                                
                            </tr>
                        </thead>
                        <?php foreach($compras as $compra){ ?>
                                <tr>
                                    <td><?php echo $compra['compra_id']; ?></td>
                                    <td><?php echo $compra['folio']; ?></td>
                                    <td><?php echo $compra['total']; ?></td>
                                    <td><?php echo $compra['fecha_alta']; ?></td>
                                    <td><?php echo $compra['proveedor']; ?></td>
                                    <td><?php echo $compra['cel_ref']; ?></td>

                                    <td> <a href="<?php echo base_url().'/compras/muestraCompraPdf/'. $compra
                                    ['compra_id']; ?>" class="btn btn-primary">Detalles <i class="fas fa-file-alt"></i></a>
                                    </td> 
                                    
                                    <td><a href="#" data-href="<?php echo base_url() . '/compras/eliminar/' . $compra['compra_id']; ?>" data-bs-toggle="modal" 
                                    data-bs-target="#modal-confirma" data-placement="top" title="Eliminar Registro" class="btn btn-danger btn-sm">
                                    Borrar <i class="fa-regular fa-trash-can"></i></a>
                                    </td>

                                </tr>

                            <?php } ?>
                       
                        <tbody>
                            <?php  ?>
                               

                            <?php  ?>
                            
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
    