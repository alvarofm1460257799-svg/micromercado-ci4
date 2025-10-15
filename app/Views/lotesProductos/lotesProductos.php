<h1>Lista de Lotes</h1>
<a href="/lotesproductos/crear">Agregar Lote</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Fecha Vencimiento</th>
        <th>Cantidad</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($lotes as $lote): ?>
        <tr>
            <td><?= $lote['id'] ?></td>
            <td><?= $lote['id_producto'] ?></td>
            <td><?= $lote['fecha_vencimiento'] ?></td>
            <td><?= $lote['cantidad'] ?></td>
            <td>
                <a href="/lotesproductos/editar/<?= $lote['id'] ?>">Editar</a>
                <a href="/lotesproductos/eliminar/<?= $lote['id'] ?>">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
