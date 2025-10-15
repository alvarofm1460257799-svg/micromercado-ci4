
//es una alerta buena nomas
<?php if ($minimos > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>¡Atención!</strong> Hay <?= $minimos ?> productos con stock mínimo.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($productosPorExpirar)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Alerta!</strong> Hay productos que están por expirar:
        <ul>
            <?php foreach ($productosPorExpirar as $producto): ?>
                <li><?= $producto['nombre'] ?> (Vence el: <?= $producto['fecha_vence'] ?>)</li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
