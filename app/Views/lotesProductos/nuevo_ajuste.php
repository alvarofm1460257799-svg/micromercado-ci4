<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?= $titulo ?></h4>
            
            <form method="POST" action="<?= base_url('AjusteInventario/guardar') ?>">

                <!-- Motivo y Observaciones en dos columnas -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Motivo</label>
                        <select name="motivo" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Daño">Daño</option>
                            <option value="Pérdida">Pérdida</option>
                            <option value="Robo">Robo</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="1"></textarea>
                    </div>
                </div>

                <!-- Buscador de productos -->
                <div class="mb-3">
                    <label>Agregar producto</label>
                    <select id="buscadorProducto" class="form-select">
                        <option value="">Buscar producto...</option>
                        <?php foreach($productos as $p): ?>
                            <option value="<?= $p['id'] ?>" data-nombre="<?= $p['nombre'] ?>">
                                <?= $p['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tabla de productos -->
                <h5>Productos a ajustar</h5>
                <table class="table table-bordered table-sm" style="background-color: white;" id="tablaProductos">
                    <thead  style="background-color: black;">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Antes</th>
                            <th>Cantidad Después</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="submit" class="btn btn-success">Guardar Ajuste</button>
                <a href="<?= base_url('ajusteInventario') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </main>
</div>

<script>
        const base_url = "<?= base_url(); ?>";
// Agregar producto a la tabla
document.getElementById('buscadorProducto').addEventListener('change', function() {
    let id = this.value;
    let nombre = this.options[this.selectedIndex].dataset.nombre;

    if(id) {
        // Llamada Ajax para obtener el stock actual
        fetch(base_url + "/AjusteInventario/obtenerStock/" + id)
            .then(res => res.json())
            .then(data => {
                let stock = data.stock ?? 0;

                let fila = `
                    <tr>
                        <td>
                            ${nombre}
                            <input type="hidden" name="id_producto[]" value="${id}">
                        </td>
                        <td><input type="number" step="0.01" name="cantidad_antes[]" class="form-control" value="${stock}" readonly></td>
                        <td><input type="number" step="0.01" name="cantidad_despues[]" class="form-control" value="${stock}"></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm eliminarFila">X</button>
                        </td>
                    </tr>
                `;
                document.querySelector("#tablaProductos tbody").insertAdjacentHTML('beforeend', fila);
                document.getElementById('buscadorProducto').value = ""; // reset
            });
    }
});




    // Eliminar fila
    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('eliminarFila')) {
            e.target.closest('tr').remove();
        }
    });
</script>
