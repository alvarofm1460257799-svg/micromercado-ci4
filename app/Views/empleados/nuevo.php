<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>

            <form method="POST" action="<?php echo base_url(); ?>/empleados/insertar"
            autocomplete="off">
            <?php csrf_field();?>
            <div class="form-group">
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Nombres</label>
                        <input class="form-control" id="nombres" name="nombres" type="text" value="<?php 
                        echo set_value('nombres')?>" autofocus   />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Apellido Paterno</label>
                        <input class="form-control" id="ap" name="ap" value="<?php echo set_value('ap')?>" 
                        type="text"  />
                    </div>
                </div>
                </div>
                <br>
                <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Apellido Materno</label>
                        <input class="form-control" id="am" name="am" value="<?php echo set_value('am')?>" 
                        type="text"  />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>* CI</label>
                        <input class="form-control" id="ci" name="ci" value="<?php echo set_value('ci')?>" 
                        type="text"  />
                    </div>
                </div>
                </div>
                <br>
                <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Celular Referencia</label>
                        <input class="form-control" id="cel_ref" name="cel_ref" value="<?php echo set_value('cel_ref')?>" 
                        type="number"  />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>* Direccion</label>
                        <input class="form-control" id="direccion" name="direccion" value="<?php echo set_value('direccion')?>" 
                        type="text"  />
                    </div>
                </div>
                <br>
                </div>
                    <div class="col-12 col-sm-6">
                    <label>* Genero</label>
                    <select class="form-control" id="genero" name="genero" >
                        <option value="">Seleccionar género</option>
                        <option value="MASCULINO" <?php echo (isset($datos['genero']) && $datos['genero'] == 'MASCULINO') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="FEMENINO" <?php echo (isset($datos['genero']) && $datos['genero'] == 'FEMENINO') ? 'selected' : ''; ?>>Femenino</option>
                        
                    </select>
                     </div>

                    
                
            </div>
            <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>
                <a href="<?php echo base_url(); ?>/empleados" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
        
    </main>
    
    