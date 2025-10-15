<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
                    <?php echo $validation->listErrors();?>
                </div>
            <?php  }?>

            <form method="POST" action="<?php echo base_url(); ?>/usuarios/actualizar" autocomplete="off">
                <!-- Hidden field for user ID -->
                <input type="hidden" value="<?php echo $datos['id']; ?>" name="id"/>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Usuario</label>
                            <input class="form-control" id="usuario" name="usuario" type="text" 
                                   value="<?php echo $datos['usuario']; ?>" autofocus required/>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Empleado</label>
                            <select class="form-control" id="id_empleado" name="id_empleado" required>
                                <option value="">Seleccionar empleado</option>
                                <?php foreach($empleados as $empleado){ ?>
                                    <option value="<?php echo $empleado['id']; ?>" 
                                            <?php if($empleado['id'] == $datos['id_empleado']){ echo 'selected'; } ?>>
                                        <?php echo $empleado['nombres'],'  ',$empleado['ap']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Rol</label>
                            <select class="form-control" id="id_rol" name="id_rol" required>
                                <option value="">Seleccionar rol</option>
                                <?php foreach($roles as $rol){ ?>
                                    <option value="<?php echo $rol['id']; ?>" 
                                            <?php if($rol['id'] == $datos['id_rol']){ echo 'selected'; } ?>>
                                        <?php echo $rol['nombre']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Cajas</label>
                            <select class="form-control" id="id_caja" name="id_caja" required>
                                <option value="">Seleccionar caja</option>
                                <?php foreach($cajas as $caja){ ?>
                                    <option value="<?php echo $caja['id']; ?>"
                                            <?php if($caja['id'] == $datos['id_caja']){ echo 'selected'; } ?>>
                                        <?php echo $caja['nombre']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Contraseña</label>
                            <input class="form-control" id="password" name="password" type="hidden"
                                   value="<?php echo $datos['password']; ?>" required />
                        </div>

                    </div>
                </div>

                <br>          
                <a href="<?php echo base_url(); ?>/usuarios" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
    </main>
</div>
