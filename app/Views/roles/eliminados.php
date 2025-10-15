<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
           
                <br><br>
                    <h5  class="mt"><?php echo $titulo; ?></h5>
          
            <div class="card mb-4">
                <div class="card-header">
                    <a href="<?php echo base_url(); ?>/roles" class="btn btn-warning ">Roles</a>
                </div>
                <div class="card-body">
                <table id="id_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Id</th>
                                <th>Nombre</th>
                                <th style="width: 7%;">Restaurar</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($datos as $dato) { ?>
                                <tr>
                                    <td><?php echo $dato['id']; ?></td>
                             
                                    <td><?php echo $dato['nombre']; ?></td>


                                    <td><a href="#" data-href="<?php echo base_url() . '/roles/reingresar/' . $dato['id']; ?>" data-bs-toggle="modal" data-bs-target="#modal-confirma" data-placement="top" style="margin-left:45%;" title="Reingresar Registro"><i class="fas fa-arrow-alt-circle-up"></i></a>
                                    </td>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>



    <!-- Modal ventanita para mostrar si va elminar o no-->
    <div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reingresar registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Decea reingresar este registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-linght" data-bs-dismiss="modal">Cancelar</button>
                    <a class="btn btn-danger btn-ok" id="btn-ok">SI</a>
                </div>
            </div>
        </div>
    </div>