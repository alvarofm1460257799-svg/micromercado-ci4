<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>
            
            <form method="POST" action="<?php echo base_url(); ?>/empleados/actualizar"
            autocomplete="off">
            <!--hiden = tipo oculto-->
            <input type="hidden" value="<?php echo $datos['id'];?>" name="id"/>
            
            <div class="form-group">
                <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Nombres</label>
                        <input class="form-control" id="nombres" name="nombres" type="text" 
                        value="<?php echo $datos['nombres'];?>" autofocus  />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Apellido Paterno</label>
                        <input class="form-control" id="ap" name="ap" 
                        type="text" value="<?php echo $datos['ap'];?>"  />
                    </div>
                 </div>
                 </div>
                 <br>
                 <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Apellido Materno</label>
                        <input class="form-control" id="am" name="am" type="text" 
                        value="<?php echo $datos['am'];?>" autofocus  />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* CI</label>
                        <input class="form-control" id="ci" name="ci" 
                        type="text" value="<?php echo $datos['ci'];?>"  />
                    </div>
                 </div>
                 </div>
                 <br>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>* Celular Referencia</label>
                            <input class="form-control" id="cel_ref" name="cel_ref" type="number" 
                            value="<?php echo $datos['cel_ref'];?>" autofocus  />
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>* Direccion</label>
                            <input class="form-control" id="direccion" name="direccion" 
                            type="text" value="<?php echo $datos['direccion'];?>"  />
                        </div>
                    </div>
                 </div>
                 <br>
                 <div class="col-12 col-sm-6">
                    <label for="genero">* Género</label>
                    <select class="form-control" id="genero" name="genero" >
                        <option value="">Seleccionar género</option>
                        <option value="MASCULINO" <?php echo (isset($datos['genero']) && $datos['genero'] == 'MASCULINO') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="FEMENINO" <?php echo (isset($datos['genero']) && $datos['genero'] == 'FEMENINO') ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>


               
            </div>
                <!--br para salto YO LO PUSE-->          
                <br>
                <a href="<?php echo base_url(); ?>/empleados" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
        
    </main>