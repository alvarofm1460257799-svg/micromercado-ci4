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
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>




            
            <form method="POST"  enctype="multipart/form-data" action="<?php echo base_url(); ?>/productos/insertar"
            autocomplete="off">

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Codigo</label>
                        <input class="form-control" id="codigo" name="codigo" type="number" value="<?php 
                        echo set_value('codigo')?>"autofocus  />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php 
                        echo set_value('nombre')?>" autofocus  />
                    </div>
                </div>
            </div>
            <br>

            <!--SALTO DE BOTON Y OTRA FILA-->
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Proveedor</label>
                        <select class="form-control" id="id_proveedor" name="id_proveedor" >
                            <option value="">Seleccionar proveedor</option>
                            <?php foreach($proveedores as $proveedor){ ?>
                                <option value="<?php echo $proveedor['id'];?>"><?php echo $proveedor['nombre'],'  ',$proveedor['apellido'];?> </option>
                            <?php } ?>    
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Categoria</label>
                        <select class="form-control" id="id_categoria" name="id_categoria" >
                            <option value="">Seleccionar categoria</option>
                            <?php foreach($categorias as $categoria){ ?>
                                <option value="<?php echo $categoria['id'];?>"><?php echo $categoria['nombre'];?> </option>
                            <?php } ?>    
                        </select>
                    </div>
                </div>
            </div>
            <br>

            <div class="form-group">
                <div class="row">
                <div class="col-12 col-sm-6">
                    <label>* Precio compra</label>
                    <input class="form-control" id="precio_compra" name="precio_compra" type="number" step="0.01" 
                        value="<?php echo set_value('precio_compra')?>" autofocus />
                </div>
                <div class="col-12 col-sm-6">
                    <label>* Precio venta</label>
                    <input class="form-control" id="precio_venta" name="precio_venta" type="number" step="0.01" 
                        value="<?php echo set_value('precio_venta')?>" autofocus />
                </div>


                   
                </div>
            </div>
            <br>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Stock minimo</label>
                        <input class="form-control" id="stock_minimo" name="stock_minimo" type="number" value="<?php 
                        echo set_value('stock_minimo')?>"autofocus  />
                    </div>
              
                    
                </div>
            </div>
            <br>
            
            <div class="form-group">
                    <div class="row">
                    <div class="col-12 col-sm-6">
                   
                   <input class="form-control" id="fecha_vence" name="fecha_vence" type="hidden" value="<?php 
                   echo set_value('fecha_vence')?>" autofocus  />
                    </div>
                        <div class="col-12 col-sm-6">
                   
                        <input class="form-control" id="existencias" name="existencias" type="hidden" value="<?php 
                        echo set_value('existencias')?>" autofocus  />
                         </div>

              

                    </div>
                </div>
                <div class="form-group">
                    <div class="row">

                        <div class="col-12 col-sm-6">
                        <br>
                        <label>Imagen del Producto</label>
                        <br>
                        <br>
                        <!-- Imagen de vista previa -->
                        <img id="preview" src="" class="img-responsive" width="200" style="display:none;"/>
                        <br>
                        <!-- Input para seleccionar la imagen -->
                        <input type="file" id="img_producto" name="img_producto" accept="image/*" onchange="previewImage(event)">
                        <p class="text-danger">Cargar imagen en formato JPG de 150x150 píxeles.</p>
                        </div>

                    </div>
                </div>


                <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>
                <a href="<?php echo base_url(); ?>/productos" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
        
    </main>
    <script>
function previewImage(event) {
    var reader = new FileReader();
    var preview = document.getElementById('preview');

    reader.onload = function() {
        // Asignar la imagen cargada al atributo src de la imagen de vista previa
        preview.src = reader.result;
        preview.style.display = 'block'; // Mostrar la imagen
    };

    // Leer la imagen cargada como Data URL
    reader.readAsDataURL(event.target.files[0]);
}

document.getElementById('formProducto').addEventListener('submit', function (e) {
        const precioCompra = parseFloat(document.getElementById('precio_compra').value);
        const precioVenta = parseFloat(document.getElementById('precio_venta').value);

        if (precioCompra > precioVenta) {
            e.preventDefault(); // Evita que el formulario se envíe
            Swal.fire({
                icon: 'error',
                title: 'Error en precios',
                text: 'El precio de compra no puede ser mayor que el precio de venta.',
                confirmButtonText: 'Ok',
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        }
    });
</script>
    
    