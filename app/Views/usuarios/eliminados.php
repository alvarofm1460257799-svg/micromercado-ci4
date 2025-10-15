<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    
                    <a href="<?php echo base_url(); ?>/usuarios" class="btn 
                    btn-warning">Usuarios</a>
                </p>
            </div>

            <div class="table=responsive">    
            <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Usuario</th>
                                <th>Restaurar</th>
                     
                            
                                 
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    <td style="width: 5%;"><?php echo $dato['id']; ?></td>
                                    <td><?php echo $dato['usuario']; ?></td>
                    
                                    <td style="width: 10%;"> <a href="<?php echo base_url().'/usuarios/reingresar/'. $dato
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
    