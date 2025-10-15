<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo;?></h4>
            

            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>

            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>/configuracion/actualizar"autocomplete="off">
            <?php csrf_field();?>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Nombre de la tienda</label>
                        <input class="form-control" id="tienda_nombre" name="tienda_nombre" type="text" 
                        value="<?php  echo $nombre['valor'];?>" autofocus required  />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* RCF</label>
                        <input class="form-control" id="tienda_rfc" name="tienda_rfc" 
                        value="<?php  echo $rfc['valor'];?>" type="text" required />
                    </div>
                </div>
            </div>
                <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Telefono de la tienda</label>
                        <input class="form-control" id="tienda_telefono" name="tienda_telefono" type="number" 
                        value="<?php  echo $telefono['valor'];?>" required  />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Email de la tienda</label>
                        <input class="form-control" id="tienda_email" name="tienda_email" 
                        value="<?php  echo $email['valor'];?>" type="email" required />
                    </div>
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>* Direccion de la tienda</label>
                        <textarea class="form-control" name="tienda_direccion" id="tienda_direccion" 
                        required><?php  echo $direccion['valor'];?></textarea>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>* Leyenda del ticket</label>
                        <textarea class="form-control" name="ticket_leyenda" id="ticket_leyenda" 
                        required><?php  echo $leyenda['valor'];?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                        <br>
                        <label>* Logotipo</label>
                        <br>
                        <img src="<?php echo base_url() . '/images/logotipo.png';?>" class="img-resposive" width="200"/>
                        <br>
                        <input type="file" id="tienda_logo" name="tienda_logo" accept="image/png"/>
                    <p class="text-danger">Cargar Imagen en formato PNG de 150x150 pixeles</p>
                        </div>
                    </div>
                </div>
            </div>
            <br>
                <i class="campo-obligatorio">(*) Campo obligatorio</i>
                <br><br>
               
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
         


    </main>
    