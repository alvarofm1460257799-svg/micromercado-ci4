<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
              
            <?php if(isset($validation)){ ?>
                <div class="alert alert-danger">
                    <?php echo $validation->listErrors(); ?>
                </div>
            <?php } ?>
        
            <form method="POST" action="<?php echo base_url(); ?>/variantesproducto/actualizar" autocomplete="off">
                <!-- hidden = tipo oculto -->
                <input type="hidden" value="<?php echo $datos['id']; ?>" name="id"/>
                
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>* Producto</label>
                            <select class="form-control" name="id_producto" required>
                                <option value="">Selecciona un producto</option>
                                <?php foreach($productos as $producto){ ?>
                                    <option value="<?php echo $producto['id']; ?>" 
                                        <?php echo ($producto['id'] == $datos['id_producto']) ? 'selected' : ''; ?>>
                                        <?php echo $producto['nombre']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 mt-2">
                            <label>* Código de Barra</label>
                            <input class="form-control" id="codigo_barra" name="codigo_barra" type="text" 
                                value="<?php echo $datos['codigo_barra']; ?>" required />
                        </div>

                        <div class="col-12 col-sm-6 mt-2">
                            <label>* Descripción (Sabor, Color, etc.)</label>
                            <input class="form-control" id="descripcion" name="descripcion" type="text" 
                                value="<?php echo $datos['descripcion']; ?>" required />
                        </div>
                    </div>
                </div>

                <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>
                <a href="<?php echo base_url(); ?>/variantesproducto" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        
        </div>
    </main>
</div>
