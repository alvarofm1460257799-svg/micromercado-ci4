<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4">Importar Variantes de Producto</h4>

            <!-- Mensajes de sesión -->
            <?php if (session('mensaje')) { ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= session('mensaje') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo base_url(); ?>/variantesproducto/procesarImportacion" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="archivo_excel" class="form-label">Seleccione el archivo Excel (.xlsx o .xls)</label>
                            <input type="file" name="archivo_excel" id="archivo_excel" class="form-control" accept=".xlsx, .xls" required>
                        </div>

                        <div class="alert alert-secondary" style="font-size: 14px;">
                            <strong>Formato esperado del Excel:</strong>
                            <ul class="mb-0">
                                <li><b>codigo_barra</b>: código único de la variante</li>
                                <li><b>producto</b>: nombre del producto (debe existir en la tabla <code>productos</code>)</li>
                                <li><b>descripcion</b>: por ejemplo “Fresa”, “Vainilla”, “500ml”</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-file-import"></i> Importar
                        </button>

                        <a href="<?php echo base_url(); ?>/variantesproducto" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Volver
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
