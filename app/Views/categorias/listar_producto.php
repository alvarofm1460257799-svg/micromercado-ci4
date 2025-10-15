<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            
            <div>
                <p>
                    <a href="<?php echo base_url(); ?>/categorias" class="btn btn-warning">Volver</a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio de Venta</th>
                            <th>Categoría</th>
                     
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($datos as $dato) { ?>
                            <tr>
                                <td><?php echo $dato['id']; ?></td>
                                <td><?php echo $dato['codigo']; ?></td>
                                <td><?php echo $dato['nombre']; ?></td>
                                <td><?php echo $dato['venta']; ?></td>
                                <td><?php echo $dato['categoria']; ?></td>
                              
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
