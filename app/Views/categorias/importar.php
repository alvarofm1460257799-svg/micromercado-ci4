<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4">Importar categorías</h4>

            <?php if (session()->getFlashdata('mensaje')): ?>
                <div class="alert alert-info">
                    <?= session()->getFlashdata('mensaje'); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= base_url('categorias/importarExcel'); ?>" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="archivo_excel" class="form-label">Seleccione archivo Excel (.xlsx)</label>
                            <input type="file" name="archivo_excel" id="archivo_excel" class="form-control" accept=".xlsx" required>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-file-import"></i> Importar
                        </button>
                        <a href="<?= base_url('categorias'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </a>
                    </form>
                </div>
            </div>

          

        </div>
    </main>
</div>
