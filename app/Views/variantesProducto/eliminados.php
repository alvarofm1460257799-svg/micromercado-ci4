<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>

            <div class="card-body">
                <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th>Producto</th>
                            <th>Código de Barra</th>
                            <th>Descripción</th>
                            <th style="width: 10%;">Editar</th>
                            <th style="width: 10%;">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($datos as $dato){ ?>
                        <tr>
                            <td><?php echo $dato['id']; ?></td>
                            <td><?php echo $dato['nombre_producto']; ?></td>
                            <td><?php echo $dato['codigo_barra']; ?></td>
                            <td><?php echo $dato['descripcion']; ?></td>
                            <td>
                                <a href="<?php echo base_url().'/variantesproducto/editar/'. $dato['id']; ?>" class="btn btn-warning btn-sm">
                                    Editar <i class="fas fa-pencil-alt"></i>
                                </a>
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
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="modalConfirmaLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmaLabel">Eliminar registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea eliminar esta variante?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok" id="btn-ok">Sí</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Pasa el link al botón del modal
    var modalConfirma = document.getElementById('modal-confirma');
    modalConfirma.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var href = button.getAttribute('data-href');
        var btnOk = modalConfirma.querySelector('.btn-ok');
        btnOk.setAttribute('href', href);
    });
</script>
