
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
<h4 class="mt-4"><?= $titulo ?></h4>

<a href="<?= base_url('presentaciones') ?>" class="btn btn-secondary mb-3">
    <i class="fas fa-arrow-left"></i> Volver a Presentaciones
</a>

<table id="id_datatable" class="table table-bordered table-striped table-sm" style="font-size: 14px;">
    <thead class="table-dark text-center">
        <tr>
            <th>ID</th>
            <th>Producto</th>
            <th>Código</th>
            <th>Tipo</th>
            <th>Cant. x Presentación</th>
            <th>Precio Compra</th>
            <th>Precio Venta</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($presentaciones as $p): ?>
            <tr>
                <td><?= esc($p['id']) ?></td>
                <td><?= esc($p['nombre_producto']) ?></td>
                <td><?= esc($p['codigo']) ?></td>
                <td><?= esc($p['tipo']) ?></td>
                <td class="text-center"><?= esc($p['cantidad_unidades']) ?></td>
                <td class="text-end">Bs <?= number_format($p['precio_compra'], 2) ?></td>
                <td class="text-end">Bs <?= number_format($p['precio_venta'], 2) ?></td>
                <td class="text-center">
                    <a href="<?= base_url('presentaciones/reingresar/' . $p['id']) ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-reply"></i> Reingresar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
  </div>
                </div>
    </main>
    
