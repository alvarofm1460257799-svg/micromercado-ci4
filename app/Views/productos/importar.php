<div id="layoutSidenav_content">
    <main>

    
        <div class="container-fluid">
<div class="container mt-4">
    <h4>Importar Productos desde Excel</h4>

<?php if (session('mensaje')): ?>
    <div class="alert alert-info"><?= session('mensaje') ?></div>
<?php endif; ?>


    <form method="POST" action="<?= base_url('productos/importarExcelProductos') ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="archivo_excel">Selecciona archivo Excel (.xlsx)</label>
            <input type="file" name="archivo_excel" id="archivo_excel" class="form-control" required accept=".xlsx">
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Importar</button>
        <a href="<?= base_url('productos') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
    </div>
    </main>
    