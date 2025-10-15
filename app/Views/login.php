<?php 
$user_session=session();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>MICROMERCADO E-MARKET</title>
    <link href="<?php echo base_url(); ?>/css/styles.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/css/style.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/css/style_login.css" rel="stylesheet" />
    <script src="<?php echo base_url(); ?>/js/all.js"></script>
    </head>
    <body> 

    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                       
                            <div class="card shadow-lg border-0 rounded-lg mt-4 ">
                                <div class="card-header">
                     
                                    <h1 class="font-weight-light my-4">
                                        <i class="fa-solid fa-user-tie fa-1x icono "></i> INICIAR SESION EN MICROMERCADO
                                    </h1>
                                </div>
                                <div class="juntar">
                                <div class="card-body">
                                    <form method="POST" action="<?php echo base_url(); ?>/usuarios/valida">
                                    
                                      
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="usuario" name="usuario" type="text" placeholder="Ingrese el usuario" />
                                            <label for="usuario"><i class="fas fa-user-lock fa-1x icono"></i>Nombre Usuario</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Ingresa tu contraseña" />
                                            <label for="password"><i class="fa-solid fa-shop-lock fa-1x icono"></i>Contraseña</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary btn-block" type="submit">ENTRAR</button>
                                        </div>
                                      
                                    
                                    </form>
                         
                                </div>
                                
                                <div class="logo"></div>
                                
                                </div>
                             
                            </div>
                            <?php if (isset($validation)) { ?>
                                            <div class="alert alert-danger mt-3">
                                                <?php echo $validation->listErrors(); ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (isset($error)) { ?>
                                            <div class="alert alert-danger mt-2">
                                                <?php echo $error; ?>
                                            </div>
                                        <?php } ?>
                            
                        </div>
                    </div>
                </div>
            </main>
        </div>
    
    </div>
    <script src="<?php echo base_url(); ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>/js/jquery-3.5.1.min.js"></script>
    <script src="<?php echo base_url(); ?>/js/scripts.js"></script>
</body>
</html>
