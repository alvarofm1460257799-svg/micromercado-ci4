<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="container mt-4">
                <h4>Importar Proveedores</h4>

                <?php if (session('mensaje')): ?>
                    <div class="alert alert-info"><?= session('mensaje') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('proveedores/importarExcel') ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="archivo_excel">Selecciona archivo Excel (.xlsx)</label>
                        <input type="file" name="archivo_excel" class="form-control" required accept=".xlsx">
                    </div>

                    <button type="submit" class="btn btn-success">Importar</button>
                    <a href="<?= base_url('proveedores') ?>" class="btn btn-secondary ms-2">Volver atrás</a>
                </form>
            </div>
        </div>
    </main>
</div>
