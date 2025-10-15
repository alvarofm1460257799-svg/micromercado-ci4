<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid ">
            <!--<ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Tables</li>
                        </ol>-->
            <!--<div class="card mb-4">
                            <div class="card-body">
                                DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                                <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                                .
                            </div>
                        </div>-->

              
                    <!--DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                    <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>-->

                    <h5  class="mt-4"><?php echo $titulo; ?></h5>

                    <!--validacion-->
                    <?php if (isset($validation)) { ?>
                        <div class="alert alert-danger">
                            <?php echo $validation->listErrors(); ?>
                        </div>
                    <?php } ?>

                
                        <form method="POST" action="<?php echo base_url(); ?>/roles/actualizar" autocomplete="off">

                            <!--hiden = tipo oculto-->
                            <input type="hidden" value="<?php echo $datos['id']; ?>" name="id" />

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <label>* Nombre</label><br><br>
                                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $datos['nombre']; ?>"  />
                                    </div>
                                </div>
                            </div>
                            <br>
                            <i class="campo-obligatorio">(*) Campo obligatorio</i>
                            <br><br>
                            <a href="<?php echo base_url(); ?>/roles" class="btn btn-primary">Regresar</a>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </form>

        </div>
    </main>