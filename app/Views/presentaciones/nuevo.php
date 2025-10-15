<?php $validation = session('validation'); ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div id="layoutSidenav_content">
    <main>
        <div class="container mt-4">
            <h4>Nueva Presentación</h4>
            <form method="POST" action="<?= base_url('presentaciones/insertar') ?>">

                <!-- Producto -->
                <div class="form-group mb-3">
                    <label for="id_producto">Producto base</label>
                    <select name="id_producto" id="id_producto" class="form-control <?= ($validation && $validation->hasError('id_producto')) ? 'is-invalid' : '' ?>">
                        <option value="">-- Selecciona un producto --</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto['id'] ?>" <?= old('id_producto') == $producto['id'] ? 'selected' : '' ?>>
                                <?= esc($producto['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($validation && $validation->hasError('id_producto')): ?>
                        <div class="invalid-feedback"><?= $validation->showError('id_producto') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Presentación padre -->
                <div class="form-group mb-3">
                    <label for="id_padre">Presentación padre (opcional)</label>
                    <select name="id_padre" id="id_padre" class="form-control">
                        <option value="">-- Sin presentación padre --</option>
                        <?php foreach ($presentaciones_padre as $pres): ?>
                            <option value="<?= $pres['id'] ?>" <?= old('id_padre') == $pres['id'] ? 'selected' : '' ?>>
                                <?= esc($pres['tipo']) ?> de <?= esc($pres['nombre_producto']) ?> (<?= $pres['cantidad_unidades'] ?> unidades)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tipo de presentación -->
                <div class="form-group mb-3">
                    <label for="tipo">Tipo de presentación (Ej: unidad, paquete x6, caja x24)</label>
                    <input type="text" name="tipo" id="tipo"
                        class="form-control <?= ($validation && $validation->hasError('tipo')) ? 'is-invalid' : '' ?>"
                        value="<?= old('tipo') ?>" required>
                    <?php if ($validation && $validation->hasError('tipo')): ?>
                        <div class="invalid-feedback"><?= $validation->showError('tipo') ?></div>
                    <?php endif; ?>
                </div>

                <!-- Código y cantidad -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="codigo">Código</label>
                        <input type="text" name="codigo" id="codigo"
                            class="form-control <?= ($validation && $validation->hasError('codigo')) ? 'is-invalid' : '' ?>"
                            value="<?= old('codigo') ?>" required>
                        <?php if ($validation && $validation->hasError('codigo')): ?>
                            <div class="invalid-feedback"><?= $validation->showError('codigo') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cantidad_unidades">Cantidad de unidades</label>
                        <input type="number" name="cantidad_unidades" id="cantidad_unidades" min="1"
                            class="form-control <?= ($validation && $validation->hasError('cantidad_unidades')) ? 'is-invalid' : '' ?>"
                            value="<?= old('cantidad_unidades') ?>" required>
                        <?php if ($validation && $validation->hasError('cantidad_unidades')): ?>
                            <div class="invalid-feedback"><?= $validation->showError('cantidad_unidades') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Precios -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="precio_venta">Precio de venta</label>
                        <input type="number" name="precio_venta" id="precio_venta" step="0.01"
                            class="form-control <?= ($validation && $validation->hasError('precio_venta')) ? 'is-invalid' : '' ?>"
                            value="<?= old('precio_venta') ?>" required>
                        <?php if ($validation && $validation->hasError('precio_venta')): ?>
                            <div class="invalid-feedback"><?= $validation->showError('precio_venta') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="precio_compra">Precio de compra</label>
                        <input type="number" name="precio_compra" id="precio_compra" step="0.01"
                            class="form-control <?= ($validation && $validation->hasError('precio_compra')) ? 'is-invalid' : '' ?>"
                            value="<?= old('precio_compra') ?>">
                        <?php if ($validation && $validation->hasError('precio_compra')): ?>
                            <div class="invalid-feedback"><?= $validation->showError('precio_compra') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="<?= base_url('presentaciones') ?>" class="btn btn-secondary">Cancelar</a>
                </div>

            </form>
        </div>
    </main>
</div>

<!-- Select2 Activation -->
<script>
$(document).ready(function() {
    $('#id_producto').select2({
        placeholder: "-- Selecciona un producto --",
        allowClear: true,
        width: '100%'
    });

    $('#id_padre').select2({
        placeholder: "-- Sin presentación padre --",
        allowClear: true,
        width: '100%'
    });
});
</script>
