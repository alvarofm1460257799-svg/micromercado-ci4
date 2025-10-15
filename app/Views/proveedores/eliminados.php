<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            
            <div>
                <p>
                    
                    <a href="<?php echo base_url(); ?>/proveedores" class="btn 
                    btn-warning">Proveedores</a>
                </p>
            </div>

            <div class="table=responsive">    
            <table id="id_datatable" class="table table-striped table-bordered table-hover table-sm table-darck" style="font-size: 14px;">
                        <thead>
                            <tr>
                            <th style="width: 5%;">Id</th>
                                <th style="width: 10%;" >Nombre</th>
                                <th style="width: 10%;" >Apellido</th>
                                <th  style="width: 10%;" >CI</th>
                                <th  style="width: 10%;" >Celular Referencia</th>
                                <th style="width: 25%;">Direccion</th>
                                 <th style="width: 10%;">Empresa</th>
                                <th style="width: 5%;">Restaurar</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php foreach($datos as $dato){ ?>
                                <tr>
                                    <td ><?php echo $dato['id']; ?></td>
                                    <td ><?php echo $dato['nombre']; ?></td>
                                    <td  ><?php echo $dato['apellido']; ?></td>
                                    <td ><?php echo $dato['CI']; ?></td>
                                    <td  ><?php echo $dato['cel_ref']; ?></td>
                                    <td  ><?php echo $dato['direccion']; ?></td>
                                          <td  ><?php echo $dato['empresa']; ?></td>

                                    <td> <a href="<?php echo base_url().'/proveedores/reingresar/'. $dato
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
    