<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <br><br>
        
            <div class="card mb-1">
                <div class="card-body">
     

                    <h5 style=" position: absolute; top:20%;" class="mt"><?php echo $titulo; ?></h5>
                </div>
            </div>
            <div class="card mb-4">
               
                <div class="card-body">

                    <!--validacion-->
                    <?php if (isset($validation)) { ?>
                        <div class="alert alert-danger">
                            <?php echo $validation->listErrors(); ?>
                        </div>
                    <?php } ?>
                    <div class="card-header">
                        <form method="POST" action="<?php echo base_url(); ?>/roles/insertar" autocomplete="off">

                            <div class="form-group">
                                <div class="row"><!-- fila-->
                                    <div class="col-12 col-sm-6 ">
                                        <label>* Nombre</label>
                                        <input type="text" value="<?php echo set_value('nombre') ?>" class="form-control" id="nombre" name="nombre"  />
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
                </div>
            </div>
        </div>
    </main>