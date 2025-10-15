<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    
                    <a href="<?php echo base_url(); ?>/productos" class="btn 
                    btn-warning">Productos</a>
                </p>
            </div>

            <div class="table=responsive">    
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                                <th style="width: 5%;">Id</th>
                                <th style="width: 13%;">Codigo</th>
                                <th style="width: 30%;">Nombre</th>
                                <th style="width: 5%;">Precio Venta</th>
                                <th style="width: 5%;">Precio Compra</th>
                                <th style="width: 5%;">Existencias</th>
                                <th style="width: 5%;">Restaurar</th>
                            
                                
                          
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    
                                    <td ><?php echo $dato['id']; ?></td>
                                    <td ><?php echo $dato['codigo']; ?></td>
                                    <td ><?php echo $dato['nombre']; ?></td>
                                    <td ><?php echo $dato['precio_venta']; ?></td>
                                    <td ><?php echo $dato['precio_compra']; ?></td>
                                    <td ><?php echo $dato['existencias']; ?></td>
                                  

                                    <td > <a href="<?php echo base_url().'/productos/reingresar/'.$dato
                                    ['id']; ?>" ><i class="fa-solid fa-trash-arrow-up"></i></a>
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
    