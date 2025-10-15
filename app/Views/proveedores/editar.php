<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>
            
            <form method="POST" action="<?php echo base_url(); ?>/proveedores/actualizar"
            autocomplete="off">
            <!--hiden = tipo oculto-->
            <input type="hidden" value="<?php echo $datos['id'];?>" name="id"/>
            
            <div class="form-group">
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" 
                        value="<?php echo $datos['nombre'];?>" autofocus  />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Apellido</label>
                        <input class="form-control" id="apellido" name="apellido" 
                        type="text" value="<?php echo $datos['apellido'];?>"  />
                    </div>    
                  </div>
                  </div>
                  <br>
                  <div class="form-group">
                  <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* CI</label>
                        <input class="form-control" id="CI" name="CI" 
                        type="text" value="<?php echo $datos['CI'];?>"  />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>* Celular Referencia</label>
                        <input class="form-control" id="cel_ref" name="cel_ref" type="number" 
                        value="<?php echo $datos['cel_ref'];?>" autofocus  />
                    </div>
                </div>
                </div>
                    <br>
                     <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>Direccion</label>
                        <input class="form-control" id="direccion" name="direccion" type="text" 
                        value="<?php echo $datos['direccion'];?>" autofocus  />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>Empresa</label>
                        <input class="form-control" id="empresa" name="empresa" type="text" 
                        value="<?php echo $datos['empresa'];?>" autofocus  />
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