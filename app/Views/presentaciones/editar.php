<?php $validation = \Config\Services::validation(); ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h4 class="mt-4">Editar Presentación</h4>

            <form method="POST" action="<?= base_url('presentaciones/actualizar') ?>">
                <input type="hidden" name="id" value="<?= esc($presentacion['id']) ?>">

                <!-- Producto -->
                <div class="mb-3">
                    <label for="id_producto" class="form-label">Producto</label>
                    <select name="id_producto" id="id_producto" class="form-select <?= $validation->hasError('id_producto') ? 'is-invalid' : '' ?>">
                        <option value="">-- Selecciona un producto --</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto['id'] ?>" <?= old('id_producto', $presentacion['id_producto']) == $producto['id'] ? 'selected' : '' ?>>
                                <?= esc($producto['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"><?= $validation->showError('id_producto') ?></div>
                </div>

                <!-- Presentación padre -->
                <div class="mb-3">
                    <label for="id_padre" class="form-label">Presentación padre (opcional)</label>
                    <select name="id_padre" id="id_padre" class="form-select">
                        <option value="">-- Sin presentación padre --</option>
                        <?php foreach ($presentaciones_padre as $padre): ?>
                            <?php if ($padre['id'] != $presentacion['id']): ?>
                                <option value="<?= $padre['id'] ?>" <?= old('id_padre', $presentacion['id_padre']) == $padre['id'] ? 'selected' : '' ?>>
                                    <?= esc($padre['tipo']) ?> de <?= esc($padre['nombre_producto']) ?> (<?= $padre['cantidad_unidades'] ?> unidades)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tipo -->
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo</label>
                    <input type="text" name="tipo" id="tipo" class="form-control <?= $validation->hasError('tipo') ? 'is-invalid' : '' ?>" 
                        value="<?= old('tipo', $presentacion['tipo']) ?>" required>
                    <div class="invalid-feedback"><?= $validation->showError('tipo') ?></div>
                </div>

                <!-- Código y Cantidad -->
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" name="codigo" id="codigo" class="form-control <?= $validation->hasError('codigo') ? 'is-invalid' : '' ?>"
                            value="<?= old('codigo', $presentacion['codigo']) ?>" required>
                        <div class="invalid-feedback"><?= $validation->showError('codigo') ?></div>
                    </div>

                    <div class="col-sm-6">
                        <label for="cantidad_unidades" class="form-label">Cantidad de Unidades</label>
                        <input type="number" name="cantidad_unidades" id="cantidad_unidades" class="form-control <?= $validation->hasError('cantidad_unidades') ? 'is-invalid' : '' ?>"
                            value="<?= old('cantidad_unidades', $presentacion['cantidad_unidades']) ?>" required>
                        <div class="invalid-feedback"><?= $validation->showError('cantidad_unidades') ?></div>
                    </div>
                </div>

                <!-- Precios -->
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="precio_venta" class="form-label">Precio Venta</label>
                        <input type="number" name="precio_venta" id="precio_venta" step="0.01" class="form-control <?= $validation->hasError('precio_venta') ? 'is-invalid' : '' ?>"
                            value="<?= old('precio_venta', $presentacion['precio_venta']) ?>" required>
                        <div class="invalid-feedback"><?= $validation->showError('precio_venta') ?></div>
                    </div>

                    <div class="col-sm-6">
                        <label for="precio_compra" class="form-label">Precio Compra</label>
                        <input type="number" name="precio_compra" id="precio_compra" step="0.01" class="form-control <?= $validation->hasError('precio_compra') ? 'is-invalid' : '' ?>"
                            value="<?= old('precio_compra', $presentacion['precio_compra']) ?>" required>
                        <div class="invalid-feedback"><?= $validation->showError('precio_compra') ?></div>
                    </div>
                </div>

                <!-- Botones -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="<?= base_url('presentaciones') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </form>
        </div>
    </main>
</div>

<!-- Select2 Activation -->
<script>
$(document).ready(function() {
    $('#id_producto').select2({ placeholder: "-- Selecciona un producto --", allowClear: true, width: '100%' });
    $('#id_padre').select2({ placeholder: "-- Sin presentación padre --", allowClear: true, width: '100%' });
});
</script>
