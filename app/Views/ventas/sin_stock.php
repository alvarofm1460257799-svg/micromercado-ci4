<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
               

                    <a href="<?php echo base_url();?>/compras/stockActual" class="btn 
                    btn-success">Regresar</a>
                </p>
            </div>

            <div class="card-body">
                 <table id="id_datatable" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Folio Venta</th>
                    <th>Producto</th>
                    <th>Cantidad Faltante</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta) { ?>
                    <tr>
                        <td><?php echo $venta['id_venta']; ?></td>
                        <td><?php echo $venta['nombre_producto']; ?></td>
                        <td><?php echo $venta['cantidad_faltante']; ?></td>
                        <td><?php echo date('d-m-Y H:i:s', strtotime($venta['fecha'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
