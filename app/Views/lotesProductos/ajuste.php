<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4">Ajustes de Inventario</h4>
            
            <div>
                <p>
                    <a href="<?= base_url(); ?>/AjusteInventario/crear" class="btn btn-info">
                        <i class="fa-solid fa-circle-plus"></i> Nuevo Ajuste
                    </a>
                    <a href="<?= base_url('compras/stockActual') ?>" class="btn btn-secondary">Volver</a>
                </p>
            </div>

            <div class="card-body">
                <table id="id_datatable" class="table table-striped table-hover table-bordered align-middle">

                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Fecha</th>
                            <th style="width: 25%;">Motivo</th>
                            <th style="width: 10%;">Usuario</th>
                            <th style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ajustes as $ajuste): ?>
                        <tr>
                            <td><?= $ajuste['id'] ?></td>
                            <td><?= $ajuste['fecha'] ?></td>
                            <td><?= $ajuste['motivo'] ?></td>
                            <td><?= $ajuste['nombre_usuario'] ?></td>

                            <td>
                                <a href="<?= base_url('AjusteInventario/detalle/'.$ajuste['id']) ?>" 
                                   class="btn btn-primary btn-sm" data-placement="top" title="Ver Detalle">
                                   Detalle <i class="fas fa-list-alt"></i>
                                </a>
                               
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación -->
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

<!-- Script para pasar la URL al modal -->
<script>
    var modalConfirma = document.getElementById('modal-confirma')
    modalConfirma.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var href = button.getAttribute('data-href')
        var btnOk = modalConfirma.querySelector('.btn-ok')
        btnOk.setAttribute('href', href)
    })
</script>
