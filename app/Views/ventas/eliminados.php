<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
               

                    <a href="<?php echo base_url();?>/ventas" class="btn 
                    btn-success">Ventas</a>
                </p>
            </div>

            <div class="card-body">
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Cajero</th>
                                <th></th>
                           
                                
                          
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    <td><?php echo $dato['fecha_alta']; ?></td>
                                    <td><?php echo $dato['folio']; ?></td>
                                    <td><?php echo $dato['cliente']; ?></td>
                                    <td><?php echo $dato['total']; ?></td>
                                    <td><?php echo $dato['cajero']; ?></td>

                                    <td> <a href="<?php echo base_url().'/ventas/muestraTicket/'. $dato
                                    ['id']; ?>" class="btn btn-primary"><i class="fas fa-list-alt"></i></a>
                                    </td>

                                    
                                    
                                    
                                    
                                </tr>

                            <?php } ?>
                            
                        </tbody>
                    </table>
                </div>
                </div>
                </div>
                </div>
    </main>
    