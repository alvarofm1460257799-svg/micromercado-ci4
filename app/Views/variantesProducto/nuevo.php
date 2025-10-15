<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?= $titulo; ?></h4>
            
            <?php if(isset($validation)){ ?>
                <div class="alert alert-danger">
                    <?= $validation->listErrors(); ?>
                </div>
            <?php } ?>

            <form method="POST" action="<?= base_url(); ?>/variantesproducto/insertar" autocomplete="off">
                <div class="form-group">
                    <div class="row">
                        <!-- Producto -->
                        <div class="col-12 col-sm-6">
                            <label>* Producto</label>
                            <select class="form-control select2" name="id_producto" required>
                                <option value="">Selecciona un producto</option>
                                <?php foreach($productos as $producto){ ?>
                                    <option value="<?= $producto['id']; ?>" 
                                        <?= old('id_producto') == $producto['id'] ? 'selected' : '' ?>>
                                        <?= $producto['nombre']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Código de barra -->
                        <div class="col-12 col-sm-6">
                            <label>* Código de Barra</label>
                            <input class="form-control" id="codigo_barra" name="codigo_barra" type="text"
                                value="<?= old('codigo_barra'); ?>" required />
                        </div>

                        <!-- Descripción -->
                        <div class="col-12 col-sm-6 mt-2">
                            <label>* Descripción (Sabor, Color, etc.)</label>
                            <input class="form-control" id="descripcion" name="descripcion" type="text"
                                value="<?= old('descripcion'); ?>" required />
                        </div>
                    </div>
                </div>

                <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>
                <a href="<?= base_url(); ?>/variantesproducto" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
    </main>
</div>

<!-- Select2 CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Inicializar Select2 -->
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Selecciona un producto",
        allowClear: true,
        width: '100%'
    });
});
</script>
