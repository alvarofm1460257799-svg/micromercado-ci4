<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    
                    <a href="<?php echo base_url(); ?>/cajas" class="btn 
                    btn-warning">Volver</a>
                </p>
            </div>

            <div class="table=responsive">    
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                            <th  style="width: 5%;">Id</th>
                                <th style="width: 10%;">Numero Caja</th>
                                <th>Nombre</th>
                                <th>Folio</th>
                                <th style="width: 10%;">Restaurar</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                     <td ><?php echo $dato['id']; ?></td>
                                    <td ><?php echo $dato['numero_caja']; ?></td>
                                    <td ><?php echo $dato['nombre']; ?></td>
                                    <td ><?php echo $dato['folio']; ?></td>

                                    <td> <a href="<?php echo base_url().'/cajas/reingresar/'. $dato
                                    ['id']; ?>" ><i class="fas fa-arrow-alt-circle-up"></i></a>
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
    