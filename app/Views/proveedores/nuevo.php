<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>

            <form method="POST" action="<?php echo base_url(); ?>/proveedores/insertar"
            autocomplete="off">
            <?php csrf_field();?>
            <div class="form-group">
            <div class="form-group">
                <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>* Nombre</label>
                            <input class="form-control" id="nombre" name="nombre" type="text" value="<?php 
                            echo set_value('nombre')?>" autofocus   />
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>* Apellido</label>
                            <input class="form-control" id="apellido" name="apellido" value="<?php echo set_value('apellido')?>" 
                            type="text"  />
                        </div>
                    </div>
                    </div>
                    <br>
                    <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>* CI</label>
                            <input class="form-control" id="CI" name="CI" value="<?php echo set_value('CI')?>" 
                            type="text"  />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>* Celular Referencia</label>
                            <input class="form-control" id="cel_ref" name="cel_ref" value="<?php echo set_value('cel_ref')?>" 
                            type="number"  />
                        </div>

                    </div>
                    </div>
                    <br>
                     <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>* Direccion</label>
                            <input class="form-control" id="direccion" name="direccion" value="<?php echo set_value('direccion')?>" 
                            type="text"  />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Empresa</label>
                            <input class="form-control" id="empresa" name="empresa" value="<?php echo set_value('empresa')?>" 
                            type="text"  />
                        </div>
                        </div>
                </div>
           
            <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>
                <a href="<?php echo base_url(); ?>/proveedores" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
        
    </main>
    
    