<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h4 class="mt-4">Importar Presentaciones de Productos</h4>

            <?php if (session()->getFlashdata('mensaje')): ?>
                <div class="alert alert-info mt-3">
                    <?= session()->getFlashdata('mensaje') ?>
                </div>
            <?php endif; ?>

            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-file-import"></i> Importar desde Excel (.xlsx)
                </div>
                <div class="card-body">
                    <form action="<?= base_url('presentaciones/importarExcelPresentaciones') ?>" 
                          method="post" 
                          enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="archivo_excel" class="form-label">
                                <strong>Selecciona un archivo Excel (.xlsx)</strong>
                            </label>
                            <input type="file" name="archivo_excel" id="archivo_excel" 
                                   class="form-control" accept=".xlsx" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('presentaciones') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Importar Presentaciones
                            </button>
                        </div>
                    </form>

                    <hr>

                    <div class="mt-3">
                        <p><strong>📋 Formato del Excel (columnas en orden):</strong></p>
                        <ul>
                            <li><b>Código</b> — Código único de la presentación.</li>
                            <li><b>Producto</b> — Nombre exacto del producto al que pertenece.</li>
                            <li><b>Tipo</b> — Nombre de la presentación (unidad, caja 24, fardo, etc.).</li>
                            <li><b>Cantidad x Presentación</b> — Número de unidades que contiene.</li>
                            <li><b>Precio Compra</b> — Precio de compra de esta presentación.</li>
                            <li><b>Precio Venta</b> — Precio de venta de esta presentación.</li>
                            <li><b>Código Padre</b> — (opcional) Código de otra presentación que actúa como padre jerárquico.</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-info-circle"></i> 
                        Si importas presentaciones con jerarquías (id_padre), 
                        el sistema las conectará automáticamente después de insertar todas las filas.
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
