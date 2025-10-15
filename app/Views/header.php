<?php 
$user_session=session();
?>
<!DOCTYPE html>
<html lang="es">

<head>   


<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Micromercado</title>

    <link href="<?php echo base_url(); ?>/css/style_inicio.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/css/style.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/css/styles.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/css/dataTables.bootstrap4.min.css" rel="stylesheet" />

    <link href="<?php echo base_url(); ?>/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

    <script src="<?php echo base_url(); ?>/js/all.js"></script>
    <script src="<?php echo base_url(); ?>/js/jquery-ui/external/jquery/jquery.js"></script>
    <script src="<?php echo base_url(); ?>/js/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo base_url(); ?>/js/chart3.js"></script>
    <script src="<?php echo base_url(); ?>/js/sweetalert2.js"></script>





 


</head>


<body class="sb-nav-fixed">
    
<nav style="background: linear-gradient(to right, #19919D, #15757F, #115C66, #0C454F);" class="sb-topnav navbar navbar-expand navbar-dark bg-oscuro">
        <div  class="principal" style="display: flex; justify-content: center; align-items: center;  width: 900px; margin-left:-300px;">
        <a href="<?php echo base_url(); ?>/inicio"><div class="logo"></div></a>
        <a class="navbar-brand ps-3" href="<?php echo base_url(); ?>/inicio" >SISTEMA DE CONTROL MICROMERCADO GOLOSO</a>
        </div>
       <button class="btn btn-link btn-sm order-1" id="sidebarToggle" href="#!" style="position: absolute; top: 50%; left: 600px; transform: translateY(-50%); font-size: 1.2rem; color: #ffffff; text-decoration: none;">
    <i class="fas fa-bars"></i>
</button>

        

        <ul class="navbar-nav ms-auto  me-3 me-lg-4 my-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link2 dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-circle-user"></i>
                    <?php echo $user_session->usuario ?> 
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                 
                    <li><a class="dropdown-item" href="<?php echo base_url(); ?>/usuarios/cambia_password"><i class="fa-solid fa-key"></i> Cambiar Contraseña</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="<?php echo base_url(); ?>/usuarios/logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar Session</a></li>
                </ul>
            </li>
        </ul>

        
    </nav>
    
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav  style="background: linear-gradient(to left, #19D19D, #17B7B7, #149D9D);" class="sb-sidenav accordion" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                       


                    
                <?php if (in_array('GESTION PRODUCTOS', session()->get('permisos'))): ?>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                        <div class="card2 ">
                            <div class="sb-nav-link-icon logo_producto "></div>  Gestión Productos
                        </div>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">

                                <?php if (in_array('___Lista_productos', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/productos">Lista Productos</a>
                                <?php endif; ?>
                                <?php if (in_array('___Lista_proveedores', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/proveedores">Lista Proveedores</a>
                                <?php endif; ?>
                                <?php if (in_array('___Lista_categorizada', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/categorias">Lista Categorizada</a>
                                <?php endif; ?>
                                <?php if (in_array('___Lista_categorizada', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/presentaciones">Lista Tamaños</a>
                                <?php endif; ?>
                                <?php if (in_array('___Lista_categorizada', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/variantesproducto">Variantes Productos</a>
                                <?php endif; ?>
                            </nav>
                        </div>

                <?php endif; ?>



                <?php if (in_array('MOVIMIENTO STOCK', session()->get('permisos'))): ?>


                        <a class="nav-link" href="<?php echo base_url(); ?>/compras/stockActual">
                        <div class="card2">
                            <div class="sb-nav-link-icon logo_stock"></div>Stock
                        </div>
                        
                        </a>
                <?php endif; ?>   


                <?php if (in_array('GESTION COMPRAS', session()->get('permisos'))): ?>
                         <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" 
                         data-bs-target="#menuCompras" aria-expanded="false" 
                         aria-controls="menuCompras">
                         <div class="card2">
                            <div class="sb-nav-link-icon logo_compras"></div> Gestión Compras
                        </div>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="menuCompras" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                            <?php if (in_array('___Nueva_compra', session()->get('permisos'))): ?>
                                <a class="nav-link color" href="<?php echo base_url(); ?>/compras/nuevo">Nueva compra</a>
                                <?php endif; ?>
                                <?php if (in_array('___Historial_compra', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/compras">Historial Compras</a>
                                <?php endif; ?>
                            
                            </nav>
                        </div>
                 <?php endif; ?>



                <?php if (in_array('ATENCION CAJA', session()->get('permisos'))): ?>
                        <a class="nav-link" href="<?php echo base_url(); ?>/ventas/venta">
                        <div class="card2">
                            <div class="sb-nav-link-icon logo_caja"></div>Caja
                        </div>
                        </a>
                <?php endif; ?>
    







               
                <?php if (in_array('GESTION VENTAS', session()->get('permisos'))): ?>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" 
                         data-bs-target="#menuVentas" aria-expanded="false" 
                         aria-controls="menuVentas">
                        <div class="card2">
                            <div class="sb-nav-link-icon logo_ventas"></div>
                            Gestión Ventas
                        </div>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="menuVentas" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                            <?php if (in_array('___Historial_ventas', session()->get('permisos'))): ?>
                                <a class="nav-link color" href="<?php echo base_url(); ?>/ventas">Historial Ventas</a>
                                <?php endif; ?>
                                <?php if (in_array('___Lista_clientes', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/clientes">Lista Clientes</a>
                                <?php endif; ?>
                                <?php if (in_array('___Arqueo_cajas', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/cajas">Arqueo cajas</a>
                                <?php endif; ?>
                            </nav>
                        </div>
                <?php endif; ?>




                <?php if (in_array('REPORTES', session()->get('permisos'))): ?>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" 
                         data-bs-target="#menuReportes" aria-expanded="false" 
                         aria-controls="menuReportes">
                        <div class="card2">
                            <div class="sb-nav-link-icon logo_reportes"></div>
                            Reportes
                        </div>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="menuReportes" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                            <?php if (in_array('___Reporte_productos', session()->get('permisos'))): ?>
                                <a class="nav-link color" href="<?php echo base_url(); ?>/productos/reportes">Reportes de Productos</a>
                                <?php endif; ?>
                                <?php if (in_array('___Reporte_ventas', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/ventas/reportes">Reporte de Actividad </a>
                                <?php endif; ?>
                                <?php if (in_array('___Reporte_compras', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/lotesProductos/reporte_vencimiento
                                    ">Reporte de Vencimientos</a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>



                    <?php if (in_array('GESTION USUARIOS', session()->get('permisos'))): ?>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" 
                         data-bs-target="#menuUsuarios" aria-expanded="false" 
                         aria-controls="menuUsuarios">
                        <div class="card2">
                            <div class="sb-nav-link-icon logo_usuario"></div>
                            Gestión Usuarios
                        </div>
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="menuUsuarios" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                            <?php if (in_array('___Lista_usuarios', session()->get('permisos'))): ?>
                                <a class="nav-link color" href="<?php echo base_url(); ?>/usuarios">Lista Usuarios</a>
                                <?php endif; ?>
                                <?php if (in_array('___Lista_personal', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/empleados">Lista Personal</a>
                                <?php endif; ?>
                                <?php if (in_array('___Roles_accesos', session()->get('permisos'))): ?>
                                    <a class="nav-link color" href="<?php echo base_url(); ?>/roles">Roles y Acessos</a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                     
                        
                    
                    <?php if (in_array('CONFIGURACION', session()->get('permisos'))): ?>
                        <a class="nav-link" href="<?php echo base_url(); ?>/configuracion">
                        <div class="card2">
                            <div class="sb-nav-link-icon logo_administracion"></div>Configuracion
                        </div>
                        
                        </a>
                    <?php endif; ?>
                       <!-- <a class="nav-link" href="<?php echo base_url(); ?>/clientes"><div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>Clientes</a>-->
                        <!--<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Pages
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                    Authentication
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="login.html">Login</a>
                                        <a class="nav-link" href="register.html">Register</a>
                                        <a class="nav-link" href="password.html">Forgot Password</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Error
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="401.html">401 Page</a>
                                        <a class="nav-link" href="404.html">404 Page</a>
                                        <a class="nav-link" href="500.html">500 Page</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading">Addons</div>
                        <a class="nav-link" href="charts.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                            Charts
                        </a>
                        <a class="nav-link" href="tables.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Tables
                        </a>-->
                    </div>
                </div>
                <!--<div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Start Bootstrap
                </div>-->
            
            </nav>
        </div>
        
  