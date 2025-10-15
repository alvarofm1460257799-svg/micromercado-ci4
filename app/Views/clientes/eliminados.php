<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    
                    <a href="<?php echo base_url(); ?>/clientes" class="btn btn-warning">Clientes</a>
                </p>
            </div>

            <div class="table=responsive">    
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                 
                        <th style="width: 5%;">Id</th>
                                <th>Nombre</th>
                                <th>CI</th>
                                <th>Direccion</th>
                                <th>Telefono</th>
                                <th>Correo</th>
                                <th style="width: 5%;">Resturar</th>
                         
                                
                                
                          
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    
                                <td><?php echo $dato['id']; ?></td>
                                    <td><?php echo $dato['nombre']; ?></td>
                                    <td><?php echo $dato['CI']; ?></td>
                                    <td><?php echo $dato['direccion']; ?></td>
                                    <td><?php echo $dato['telefono']; ?></td>
                                    <td><?php echo $dato['correo']; ?></td>


                                    <td> <a href="<?php echo base_url().'/clientes/reingresar/'.$dato
                                    ['id']; ?>" ><i class="fas fa-arrow-alt-circle-up"></i></a>
                                    </td>
                                    <td> 
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
    