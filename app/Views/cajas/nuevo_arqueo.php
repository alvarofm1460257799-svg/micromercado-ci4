<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            
            <?php if(isset($validation)){?>
                <div class="alert alert-danger">
            <?php echo $validation->listErrors();?>
            </div>
            <?php  }?>

            <form method="POST" action="<?php echo base_url(); ?>/cajas/nuevo_arqueo"
            autocomplete="off">
            <?php csrf_field();?>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>Numero de Caja</label>
                        <input class="form-control" id="numero_caja" name="numero_caja" type="number" value="<?php 
                        echo $caja['numero_caja'];?>" readonly   />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Nombre </label>
                        <input class="form-control" id="usuario" name="usuario" value="<?php echo $session->usuario;?>" 
                        type="text" readonly  />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>Monto Inicial </label>
                        <input class="form-control" id="monto_inicial" name="monto_inicial" value="" 
                        type="number" step="0.01"   />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>Folio inicial </label>
                        <input class="form-control" id="folio" name="folio" value="<?php echo $caja['folio'];?>" 
                        type="text" readonly  />
                    </div>
                    <div class="col-12 col-sm-6">
                        <label>Fecha actual</label>
                        <input class="form-control" id="folio" name="folio" value="<?php echo date('Y-m-d');?>" 
                        type="date" readonly  />
                    </div>
                    
                    <div class="col-12 col-sm-6">
                        <label>Hora actual </label>
                        <input class="form-control" id="folio" name="folio" value="<?php echo date('H:i:s');?>" 
                        type="time" readonly  />
                    </div>
                    
                    
                </div>
            </div>
                <br>
                <a href="javascript:history.back()" class="btn btn-primary" 
               data-placement="top" title="Volver">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </form>
        </div>
        
    </main>
    
    