<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h4 class="mt-4">Presentaciones de Productos</h4>

            <div class="mb-3">
                <a href="<?= base_url('presentaciones/nuevo') ?>" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Agregar Presentación
                </a>
                <a href="<?= base_url('presentaciones/generarUnidades') ?>" class="btn btn-success">
                    <i class="fas fa-cogs"></i> Generar Unidades
                </a>
                <a href="<?= base_url('presentaciones/eliminados') ?>" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Ver Eliminadas
                </a>
                <a href="<?= base_url('presentaciones/importarVista') ?>" class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Importar
                </a>


            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <table id="id_datatable" class="table table-bordered table-striped table-sm" style="font-size: 14px;">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                 <th>Codigo</th>
                                <th>Tipo</th>
                                <th>Cant. x Presentación</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Modificar</th>
                                   <th>Borrar</th>
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
                                        <a href="<?= base_url('presentaciones/editar/' . $p['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        </td>
                                          <td class="text-center">
                                        <?php if ($p['activo']): ?>
                                            <a href="<?= base_url('presentaciones/eliminar/' . $p['id']) ?>" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
