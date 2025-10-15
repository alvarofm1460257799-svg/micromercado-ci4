<div id="layoutSidenav_content">
    <main>
        <div class="container mt-4">
            <h4>Importar Conteo Físico de Lotes</h4>

            <?php if (session('mensaje')): ?>
                <div class="alert alert-info"><?= session('mensaje') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('LotesProductos/importarExcel') ?>" method="post" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="archivo_excel">Selecciona archivo Excel (.xlsx)</label>
                    <input type="file" name="archivo_excel" class="form-control" required accept=".xlsx">
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> Importar Conteo
                </button>
                <a href="<?= base_url('compras/stockActual') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver atrás
                </a>
            </form>

            <div class="mt-4">
                <p><strong>📘 Formato del Excel esperado:</strong></p>
                <ul>
                    <li><strong>Columna A → Producto</strong> (nombre exacto en el sistema)</li>
                    <li><strong>Columna B → Cantidad Física</strong> (solo números enteros)</li>
                    <li><strong>Columna C → Fecha de Vencimiento</strong> (formato: <code>YYYY-MM-DD</code> o <code>DD/MM/YYYY</code>)</li>
                </ul>

                <div class="alert alert-warning mt-2">
                    <strong>Nota:</strong> Si el producto no tiene fecha de vencimiento, deje la celda vacía o escriba 
                    <em>“sin vencimiento”</em>, <em>“no requiere”</em>, <em>“no aplica”</em> o <em>“n/a”</em>.
                </div>
            </div>
        </div>
    </main>
</div>
