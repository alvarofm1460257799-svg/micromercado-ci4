<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?= $titulo ?></h4>

            <div class="mb-3">
                <p><strong>Motivo:</strong> <?= $ajuste['motivo'] ?></p>
                <p><strong>Observaciones:</strong> <?= $ajuste['observaciones'] ?></p>
                <p><strong>Fecha:</strong> <?= $ajuste['fecha'] ?></p>
            </div>

            <h5>Productos ajustados</h5>
            <table id="id_datatable" class="table table-striped table-hover table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad Antes</th>
                        <th>Cantidad Después</th>
                        <th>Diferencia</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($detalles as $d): ?>
                    <tr>
                        <td><?= $d['nombre'] ?></td>
                        <td><?= $d['cantidad_antes'] ?></td>
                        <td><?= $d['cantidad_despues'] ?></td>
                        <td><?= $d['diferencia'] ?></td>
                        <td><?= $d['observacion'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="<?= base_url('ajusteInventario') ?>" class="btn btn-secondary">Volver</a>
        </div>
    </main>
</div>
