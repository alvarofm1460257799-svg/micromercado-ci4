<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4">Productos por Proveedor</h4>
            
            <div>
                <p>
                <a href="<?php echo base_url(); ?>/proveedores" class="btn btn-warning">Volver</a>
                </p>
            </div>

            <div class="card-body">
                <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio Compra</th>
                            <th>Proveedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($datos as $dato){ ?>
                            <tr>
                                <td><?php echo $dato['id']; ?></td>
                                <td><?php echo $dato['codigo']; ?></td>
                                <td><?php echo $dato['nombre']; ?></td>
                                <td><?php echo $dato['compra']; ?></td>
                                <td><?php echo $dato['proveedor']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
