<div id="layoutSidenav_content">
    <main>

    <?php if (session('mensaje')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= session('mensaje') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>


<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Registro exitoso!',
            text: 'El producto se ha registrado correctamente.',
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)',
            timer: 3000, // La alerta desaparece después de 3 segundos
            showConfirmButton: false
        });
    </script>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Producto modificado correctamente.',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
<?php endif; ?>


<script>
    // Detectar si hay un mensaje de error en la sesión
    <?php if (session('error')) { ?>
        Swal.fire({
            icon: 'error',
            title: '¡No se puede eliminar!',
            text: '<?php echo session('error'); ?>',
            showConfirmButton: true,
            confirmButtonText: 'Entendido',
            timerProgressBar: true,
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    <?php } ?>
</script>

<!-- Aquí va el resto del contenido de la vista -->

        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url();?>/productos/nuevo" class="btn 
                    btn-info"><i class="fa-solid fa-circle-plus"></i> Agregar</a>

                    <a href="<?php echo base_url();?>/productos/eliminados" class="btn 
                    btn-warning"><i class="fa-solid fa-delete-left"></i> Eliminados</a>

                    <a href="<?php echo base_url();?>/productos/muestraCodigos" class="btn 
                    btn-primary"><i class="fa-solid fa-barcode"></i>  Codigos de Barras</a>
                    <a href="<?= base_url('productos/importarVista') ?>" class="btn btn-success">
                        <i class="fa-solid fa-file-excel"></i> Importar Excel
                    </a>

                </p>
            </div>
            

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                
                                <th style="width: 5%;">Id</th>
                                <th style="width: 13%;">Codigo</th>
                                <th style="width: 30%;">Nombre</th>
                                <th style="width: 5%;">Precio Venta</th>
                                <th style="width: 5%;">Precio Compra</th>
                                <th style="width: 5%;">Existencias Unit.</th>
                                <th>Imagen</th>
                                <th style="width: 5%;">Productos Vencidos</th>
                                <th style="width: 5%;">Detalle  Producto</th>
                                <th style="width: 5%;">Opciones</th>

                         
                                
                          
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    <td><?php echo $dato['id']; ?></td>
                                    <td><?php echo $dato['codigo']; ?></td>
                                    <td><?php echo $dato['nombre']; ?></td>
                                    <td><?php echo $dato['precio_venta']; ?></td>
                                    <td><?php echo $dato['precio_compra']; ?></td>
                                    <td><?php echo $dato['existencias']; ?></td>

                                   <td>
  <?php 
$thumb = FCPATH . 'images/productos/thumbs/' . $dato['id'] . '.jpg';
if (!file_exists($thumb)) {
    $thumb_url = base_url('images/productos/thumbs/default.jpg');
} else {
    $thumb_url = base_url('images/productos/thumbs/' . $dato['id'] . '.jpg');
}
?>
<img src="<?= $thumb_url ?>" alt="<?= esc($dato['nombre']) ?>" loading="lazy" width="80" height="80">



                                    <td> 

                                    <?php echo $dato['productos_vencidos']; ?>
                                        <br><br>

                                        <a href="#" 
                                        data-href="<?php echo base_url('productos/limpiar/' . $dato['id']); ?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modal-limpia" 
                                        class="btn btn-dark btn-sm">
                                        Limpiar <i class="fa-solid fa-trash"></i>
                                        </a>




                                    </td>
                                    
                                    <td>
                                    <br><br> 
                                    <a href="<?php echo base_url() . '/productos/vista/' . $dato['id']; ?>" class="btn btn-info btn-sm" 
                                    data-placement="top" title="Editar Registro">Ver <i class="fas fa-eye"></i></a>

                                    </td>
                                    <td> <a href="<?php echo base_url() . '/productos/editar/' . $dato['id']; ?>" class="btn btn-warning btn-sm" 
                                    data-placement="top" title="Editar Registro">Editar <i class="fa-solid fa-pen-to-square"></i></a>
                                    <br><br>

                                   <a href="#" data-href="<?php echo base_url() . '/productos/eliminar/' . $dato['id']; ?>" data-bs-toggle="modal" 
                                   data-bs-target="#modal-confirma" data-placement="top" title="Eliminar Registro" class="btn btn-danger btn-sm">Borrar <i class="fa-regular fa-trash-can"></i></a>
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
    

<!-- Modal para confirmar la limpieza -->
<div class="modal fade" id="modal-limpia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Desechar Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea desechar los productos vencidos?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <!-- Aquí se actualiza el enlace con el ID del producto -->
                <a href="#" class="btn btn-danger btn-ok" id="btn-limpiar-confirmar">Sí</a>
            </div>
        </div>
    </div>
</div>

<script>

// Manejar el evento de mostrar el modal de limpieza
$('#modal-limpia').on('show.bs.modal', function (e) {
    var href = $(e.relatedTarget).data('href'); // Obtener la URL con el ID del producto

    // Actualizar el botón "Sí" en el modal con el enlace del producto
    $('#btn-limpiar-confirmar').attr('href', href);
});

// Ejecutar la limpieza con AJAX y luego recargar la página
$('#btn-limpiar-confirmar').on('click', function(e) {
    e.preventDefault(); // Prevenir la acción predeterminada

    var href = $(this).attr('href'); // Obtener la URL del enlace

    // Realizar la solicitud AJAX para limpiar
    $.ajax({
        url: href,
        method: 'GET', // O 'POST', según sea necesario
        success: function() {
            // Esperar un tiempo después de que la limpieza haya sido exitosa
            setTimeout(function() {
                window.location.reload(); // Recargar la página después de un retraso
            }, 2000); // 2000 ms (2 segundos) de retraso; ajusta este valor según lo que necesites
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un problema al limpiar los productos. Inténtalo de nuevo.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
});



<?php if (session()->get('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '<?= session()->get("success"); ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php endif; ?>

<?php if (session()->get('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= session()->get("error"); ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php endif; ?>

</script>
