<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
         
                
            <div>
                <a href="<?php echo base_url(); ?>/compras/kardex" class="btn btn-info">
                    <i class="fa-solid fa-file"></i> Kardex
                </a>
    
  
                <a href="<?php echo base_url(); ?>/ventas/sinStock" class="btn btn-danger">
                    <i class="fa-solid fa-exclamation-triangle"></i> Ventas sin stock
                </a>
                <a href="<?= base_url('/lotesProductos/importarVista') ?>" class="btn btn-success " >
                    <i class="fa-solid fa-file-excel"></i> Importar Excel
                </a>
                 <a href="<?= base_url('/AjusteInventario/index') ?>" class="btn btn-success"style="background-color: orange;">
                    <i class="fa-solid fa-file-excel"></i> Ajustar stock
                </a>

    
            </div>

            <br>

            <div class="card-body">
                <table id="id_datatable" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Movimiento</th>
                            <th>Fecha y Hora</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $movimiento) { ?>
                            <tr>
                                <td><?php echo $movimiento['movimiento']; ?></td>
                                <td><?php echo date('d-m-Y H:i:s', strtotime($movimiento['fecha_alta'])); ?></td>
                                <td><?php echo $movimiento['producto']; ?></td>
                                <td><?php echo $movimiento['cantidad']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
