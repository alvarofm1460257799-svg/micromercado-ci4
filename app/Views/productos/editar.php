<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>



            <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

            
            <form method="POST" action="<?php echo base_url(); ?>/productos/actualizar"
            autocomplete="off" enctype="multipart/form-data">
            
            <?php csrf_field()?>

            <input type="hidden" name="id" id="id" value="<?php echo $producto['id']; ?>">

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Codigo</label>
                        <input class="form-control" id="codigo" name="codigo" type="number" 
                        value="<?php echo $producto['codigo']; ?>" 
                        autofocus require />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" 
                        type="text" value="<?php echo $producto['nombre']; ?>" require />
                    </div>
                </div>
            </div>
            <br>

            <!--SALTO DE BOTON Y OTRA FILA-->
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Proveedor</label>
                        <select class="form-control" id="id_proveedor" name="id_proveedor" require>
                            <option value="">Seleccionar proveedor</option>
                            <?php foreach($proveedores as $proveedor){ ?>
                                <option value="<?php echo $proveedor['id'];?>" <?php if($proveedor['id'] ==
                                $producto['id_proveedor']){ echo 'selected';} ?> ><?php echo $proveedor
                                ['nombre'],'  ',$proveedor['apellido'];?> </option>
                            <?php } ?>    
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Categoria</label>
                        <select class="form-control" id="id_categoria" name="id_categoria" require>
                            <option value="">Seleccionar categoria</option>
                            <?php foreach($categorias as $categoria){ ?>
                                <option value="<?php echo $categoria['id'];?>"<?php if($categoria['id'] ==
                                $producto['id_categoria']){ echo 'selected';} ?> ><?php echo $categoria
                                ['nombre'];?> </option>
                            <?php } ?>    
                        </select>
                    </div>
                </div>
            </div>
            <br>

            <div class="form-group">
                <div class="row">
                <div class="col-12 col-sm-6">
                    <label>* Precio venta</label>
                    <input class="form-control" id="precio_venta" name="precio_venta" type="number" 
                        value="<?php echo $producto['precio_venta']; ?>" step="0.01"  />
                </div>

                <div class="col-12 col-sm-6">
                    <label>* Precio compra</label>
                    <input class="form-control" id="precio_compra" name="precio_compra" 
                        type="number" value="<?php echo $producto['precio_compra']; ?>" step="0.01"  />
                </div>

                </div>
            </div>
            <br>

            <div class="form-group">
            <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Stock minimo</label>
                        <input class="form-control" id="stock_minimo" name="stock_minimo" type="number" 
                        value="<?php echo $producto['stock_minimo']; ?>" require />
                    </div>
                    
                </div>
            </div>
            <br>


            <div class="form-group">
            <div class="row">
                        <div class="col-12 col-sm-6">
                        
                        <input class="form-control" id="existencias" name="existencias" 
                        type="hidden" value="<?php echo $producto['existencias']; ?>" require />
                         </div>

                      
                    </div>
                </div>


            <div class="form-group">
                    <div class="row">

                        <div class="row">
                            <div class="col-12 col-sm-6">
                            <br>
                            <label>imagen del Producto</label>
                            <br>
                            <img src="<?php echo base_url() . '/images/productos/'.$producto['id'].'.jpg';?>" class="img-resposive" width="200"/>
                            <br>
                            <input type="file" id="img_producto" name="img_producto" accept="image/*"/>
                            <p class="text-danger">Cargar Imagen en formato PNG de 150x150 pixeles</p>
                        </div>

                    </div>
            </div>
            <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>


                <a href="<?php echo base_url(); ?>/productos" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
        
    </main>
    
    