
<div id="layoutSidenav_content">
    <main>


<div class="backdrop" id="backdrop"></div>
<div class="container-fluid">
    <?php $idVentaTmp = session()->get('id_venta_tmp'); ?>
    <br>
    <form id="form_venta" name="form_venta" class="form-horizontal" method="POST" action="<?php echo base_url(); ?>/ventas/guarda" autocomplete="off">
       <input type="hidden" id="id_venta" name="id_venta" value="<?php echo $idVentaTmp ?>" />


        <div class="form-group">
            <div class="row">
             
            </div>
        </div>

        <div class="form-group">
            <div class="row">
            <div class="col-12 col-sm-3">
                    <br>
                    
                    <input type="hidden" id="id_producto" name="id_producto">
                    <label>* Código de Barras</label>
                    <div class="input-group">
                        <input class="form-control" id="codigo" name="codigo" type="number"
                            placeholder="Inserte código y presiona Enter" autofocus />
                        <button class="btn btn-secondary" type="button" id="btnBuscarProducto" 
                                data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-sm-2">
                    <br>
                    <label>* Cantidad</label>
                    <input class="form-control" id="cantidad" name="cantidad" type="number" min="1" value="1" placeholder="Ingrese la cantidad" />
                </div>
                <div class="col-12 col-sm-2">
                    <br>
                    <label>* Pago</label>
                    <input class="form-control" id="pago" name="pago" type="number" step="0.01" placeholder="Escriba el pago" />
                </div>

                <div class="col-12 col-sm-2">
                    <br>
                    <label>* Cambio</label>
                    <input class="form-control" id="cambio" name="cambio" type="number" readonly value="0.00" disabled />
                </div>

                <div class="col-12 col-sm-3">
                    <label style="font-weight: bold; font-size: 30px; text-align: center; margin-top:20px;">Total Bs</label>
                    <input type="text" id="total" name="total" size="7" readonly value="0.00" style="font-weight: bold; font-size: 30px; text-align: center;">
                </div>
            </div>
        </div>

        <div class="form-group">
                <div class="row">

                        <div class="col-sm-3">
                            <div class="ui-widget">
                            <label >Cliente: </label>
                            <input type="hidden" id="id_cliente" name="id_cliente" value="1">
                            <input type="text" class="form-control" id="cliente"  name="cliente" placeholder="Escribe el nombre del cliente" 
                            value="Público en general" onkeyup="" autocomplete="off" required />
                            </div>

                        </div>

                        <div class="col-sm-3">
                        
                            <label >Forma de Pago: </label>
                            <select name="forma_pago" id="forma_pago" class="form-control" required>
                                <option value="001">Efectivo</option>
                                <option value="002">Tarjeta</option>
                                <option value="003">Transferencia</option>
                            </select>         
  
                        </div>

                  <div class="col-12 col-sm-2 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="permitir_sin_stock" name="permitir_sin_stock">
                                <label class="form-check-label text-danger fw-bold" for="permitir_sin_stock">
                                    Permitir venta sin stock
                                </label>
                            </div>
                        </div>


                        <div class="col-sm-4">
                   <button type="button" id="completa_venta" class="btn btn-success mt-2">Completar Venta</button>


                        </div>
                       
                 </div>
         </div>

        <br>
        <div class="row">
            <table id="tablaProductos" class="table table-hover table-striped table-sm table-responsive tablaProductos" width="100%" style="background: white;">
                 <thead style=" background-color: black; color: white;  vertical-align: middle;">
                    <th>#</th>
                    <th>CODIGO</th>
                    <th>NOMBRE</th>
                    <th>PRECIO</th>
               
                     <th>CANTIDAD UNID.</th>
                          <th>CANTIDAD MAYOR</th>
                    <th>TOTAL</th>
                    <th> <button type="button" id="cancelar_venta" class="btn btn-danger">LIMPIAR</button></th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </form>
</div>
<!-- Modal para buscar productos -->
<div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-labelledby="modalBuscarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBuscarProductoLabel">Buscar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="buscarNombreProducto" 
                       placeholder="Escriba el nombre del producto" autofocus autocomplete="off">
                <ul class="list-group" id="listaProductos" 
                    style="max-height: 200px; overflow-y: auto;">
                    <!-- Opciones de productos autocompletados aparecerán aquí -->
                </ul>
            </div>
        </div>
    </div>
</div>


    </main>
    <script>
//cambio
// Escuchar el evento de entrada en el campo de pago


document.getElementById('pago').addEventListener('input', function () {
    let total = parseFloat(document.getElementById('total').value) || 0;
    let pago = parseFloat(this.value) || 0;
    let cambio = pago - total;

    document.getElementById('cambio').value = cambio.toFixed(2);
});


function actualizarTotal() {
    let total = 0;
    $("#tablaProductos tbody tr").each(function () {
        total += parseFloat($(this).find('td:eq(5)').text());
    });
    $("#total").val(total.toFixed(2));
}


// Inicializar Autocomplete
$(function(){
    $("#cliente").autocomplete({
        source: "<?php echo base_url(); ?>/clientes/autocompleteData",
        minLength: 3,
        select: function(event, ui){
            event.preventDefault();
            // Actualiza el campo de cliente con el nombre seleccionado
            $("#cliente").val(ui.item.value);
            // Actualiza el campo de id_cliente con el id del cliente
            $("#id_cliente").val(ui.item.id);  // Asumiendo que `id` es el campo con el ID del cliente
        }
    });
});




$(function () {
    // Detectar cuando se presiona la tecla Enter para registrar el producto
    $("#codigo").on('keypress', function (e) {
        let enterKey = 13; // Código de la tecla Enter
        let codigo = $(this).val().trim(); // Elimina espacios innecesarios
        let cantidad = parseInt($("#cantidad").val()) || 1; // Obtiene la cantidad o 1 por defecto

        // Si la tecla Enter fue presionada y hay un código ingresado
        if (e.which == enterKey && codigo != '') {
            e.preventDefault(); // 🚫 Evita que el formulario se envíe por Enter
            buscarProductoPorCodigo(codigo, cantidad, '<?php echo $idVentaTmp; ?>');
        }
    });
});


function buscarProductoPorCodigo(codigo, cantidad, id_venta) {
    let permitirSinStock = $('#permitir_sin_stock').is(':checked') ? 'on' : 'off';

    $.ajax({
        url: '<?php echo base_url(); ?>/TemporalVenta/buscarPorCodigoPresentacion',
        type: 'POST',
        dataType: 'json',
        data: {
            codigo: codigo,
            cantidad: cantidad,
            id_venta: id_venta,
            permitir_sin_stock: permitirSinStock
        },
        success: function (resultado) {
            if (resultado.error === '') {
                $("#tablaProductos tbody").html(resultado.datos);
                $("#total").val(resultado.total);
                $("#codigo").val('');
                $("#cantidad").val(1).focus();
            } else {
                Swal.fire("Error", resultado.error, "error");
            }
        },
        error: function () {
            Swal.fire("Error", "No se pudo conectar con el servidor", "error");
        }
    });
}




document.getElementById('modalBuscarProducto').addEventListener('shown.bs.modal', function () {
    document.getElementById('buscarNombreProducto').focus();
});





$(document).on('click', '.btn-eliminar', function (e) {
    e.preventDefault(); // <- evita cualquier envío del formulario

    let id_producto = $(this).data('id');
    let id_venta = $(this).data('folio');

    eliminaProducto(id_producto, id_venta);
});

// Modifica eliminaProducto para que retorne el objeto jqXHR para poder usar always()
function eliminaProducto(id_temporal, folio) {
    return $.ajax({
        url: '<?= base_url(); ?>/TemporalVenta/eliminar/' + id_temporal + '/' + folio,
        type: 'GET',
        dataType: 'json',
        success: function(resultado) {
            if (resultado.error && resultado.error !== '') {
                Swal.fire('Error', resultado.error, 'error');
            } else {
                $("#tablaProductos tbody").html(resultado.datos);
                $("#total").val(resultado.total);
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            console.error("Error AJAX eliminar:", status, error);
        }
    });
}




 $(function () {
    $("#form_venta").submit(function (e) {
        let nFilas = $("#tablaProductos tbody tr").length;

        if (nFilas < 1) {
            e.preventDefault(); // evita el envío del formulario
            Swal.fire({
                icon: 'warning',
                title: 'Producto faltante',
                text: 'Debe agregar un Producto antes de completar la venta.',
                confirmButtonText: 'Entendido',
                backdrop: `rgba(0,0,0,0.4)`,
            });
        }
        // Si hay productos, se envía el formulario normalmente
    });
});







$(document).ready(function () {
    $('#buscarNombreProducto').on('keyup', function () {
        let nombre = $(this).val().trim();
        if (nombre.length >= 2) {
            $.ajax({
                url: "<?php echo base_url('presentaciones/autocompletar'); ?>",
                type: "GET",
                data: { term: nombre },
                dataType: 'json',
                success: function (presentaciones) {
                    let opciones = presentaciones.map(p =>
                        `<li class="list-group-item list-group-item-action"
                            onclick="seleccionarPresentacion(${p.id_presentacion}, ${p.id_producto}, '${p.nombre_producto}', '${p.codigo}')">
                            ${p.nombre_producto} - ${p.codigo} - ${p.tipo}

                        </li>`
                    ).join('');
                    $('#listaProductos').html(opciones);
                },

                error: function () {
                    $('#listaProductos').html('<li class="list-group-item text-danger">Error al buscar presentaciones</li>');
                }
            });
        } else {
            $('#listaProductos').html('');
        }
    });

    // Seleccionar presentación desde el modal y simular el mismo flujo que Enter
    window.seleccionarPresentacion = function (id_presentacion, id_producto, nombre, codigo) {
        let cantidad = parseInt($("#cantidad").val()) || 1;
        let id_venta = '<?php echo $idVentaTmp; ?>';
        buscarProductoPorCodigo(codigo, cantidad, id_venta);
        $('#modalBuscarProducto').modal('hide');
    };
});






// Validar que la cantidad no sea menor a 1
document.getElementById('cantidad').addEventListener('input', function () {
        let cantidad = parseInt(this.value) || 0; // Obtén el valor ingresado o 0 si está vacío
        if (cantidad < 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad no válida',
                text: 'La cantidad no puede ser menor a 1.',
                confirmButtonText: 'Entendido',
                backdrop: `rgba(0,0,0,0.4)`,
            });

            this.value = 1; // Reinicia el valor a 1
        }
    });







$(document).ready(function () {
    let idVentaTmp = '<?php echo session()->get("id_venta_tmp"); ?>';

    if (idVentaTmp) {
        $.ajax({
            url: '<?php echo base_url(); ?>/TemporalVenta/obtenerProductos/' + idVentaTmp,
            method: 'GET',
            success: function (resultado) {
                if (resultado.datos) {
                    $("#tablaProductos tbody").empty().append(resultado.datos);
                    $("#total").val(resultado.total);
                }
            }
        });
    }
});






$('#cancelar_venta').click(function () {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Se eliminarán todos los productos de esta venta.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo base_url(); ?>/TemporalVenta/eliminarTodo/<?php echo $idVentaTmp; ?>',
                type: 'POST',
                success: function (response) {
                    let res = JSON.parse(response);
                    if (res.error === '') {
                        $("#tablaProductos tbody").html('');
                        $("#total").val("0.00");
                        limpiarCampos();
                        Swal.fire({
                            icon: 'success',
                            title: 'Venta cancelada',
                            text: 'Todos los productos han sido eliminados.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.error
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar la venta temporal.'
                    });
                }
            });
        }
    });
});


$("#completa_venta").click(function () {
    let nFilas = $("#tablaProductos tbody tr").length;

    if (nFilas < 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Producto faltante',
            text: 'Debe agregar un Producto antes de completar la venta.',
            confirmButtonText: 'Entendido',
            backdrop: `rgba(0,0,0,0.4)`,
        });
    } else {
        $("#form_venta").submit(); // <- solo aquí se hace submit
    }
});

// Prevenir cualquier envío del formulario que no sea desde el botón Completar
$("#form_venta").on("submit", function (e) {
    const btnSubmit = document.activeElement.id;
    if (btnSubmit !== "completa_venta") {
        console.warn("Formulario bloqueado para evitar envío accidental desde otro botón");
        e.preventDefault();
    }
});


    </script>  