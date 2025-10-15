

<?php $id_compra = session()->get('id_compra_tmp'); ?>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= session()->getFlashdata('error'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>





<br>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <p> </p>
            <form method="POST" id="form_compra" name="form_compra" action="<?php echo base_url(); ?>/compras/guarda"
            autocomplete="off">
            <?php csrf_field();?>
            <div class="form-group">
                <div class="row">
             
               
                <div class="col-12 col-sm-3">
                    <label>* Código</label>   
                    <input type="hidden" id="id_presentacion" name="id_presentacion">
                     <input type="hidden" id="id_producto" name="id_producto">
                    <input type="hidden" id="id_compra" name="id_compra" value="<?php echo $id_compra; ?>">

                    <div class="input-group">
                        <input class="form-control" id="codigo" name="codigo" type="number"
                            placeholder="Inserte código Y  presiona Enter" 
                            onkeyup="buscarProducto(event, this, this.value)" autofocus />

                        <button class="btn btn-secondary" type="button" id="btnBuscarProducto" 
                                data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                        <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <label for="codigo" id="resultado_error" style="color: red;"></label>
                    </div>


                    <div class="col-12 col-sm-3">
                        <label>Nombre del Producto</label>
                        <input class="form-control" id="nombre" name="nombre"  type="text" disabled
                         />
                    </div>
                    <div class="col-12 col-sm-1">
                        <label>* Cantidad</label>
                        <input class="form-control" id="cantidad" name="cantidad" type="number" step="1" min="0" />
                    </div>

                    <div class="col-12 col-sm-1">
                        <label>* Monto</label>
                        <input class="form-control" id="montocompra" name="montocompra"  type="number" 
                         />
                         
                    </div>
                   <div class="col-12 col-sm-2">
    <label>* Fecha Vence</label>
    <input class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" type="date" />
    <div class="form-check mt-1">
        <input class="form-check-input" type="checkbox" id="sin_vencimiento" name="sin_vencimiento">
        <label class="form-check-label" for="sin_vencimiento">No requiere fecha de vencimiento</label>
    </div>
</div>


                    <div class="col-12 col-sm-1">
                        <label>Stock Actual Unit.</label>
                        <input class="form-control" id="existencias" name="existencias" type="number" disabled />

                    </div>
                     <div class="col-12 col-sm-1">
                        <label>Cantidad Unidad </label>
                        <input class="form-control" id="cant_u" name="cant_u" type="number" step="0.01" readonly 
                       />
                    </div>
                    
                    
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                            <div class="col-md-1">
                    <label for="margen">Margen (%)</label>
                    <input type="number" id="margen" name="margen" class="form-control" value="13" min="0" step="0.01">
                </div>
                    <div class="col-12 col-sm-2">
                        <label>Precio Compra por Mayor</label>
                        <input class="form-control" id="precio_compra_m" name="precio_compra_m" type="number" step="0.01" disabled
                       />
                    </div>
                    <div class="col-12 col-sm-2">
                        <label>Precio Venta por Mayor</label>
                        <input class="form-control" id="precio_venta_m" name="precio_venta_m" type="number" step="0.01" 
                       />
                    </div>

                    
                    <div class="col-12 col-sm-2">
                        <label>Precio Compra Unit. </label>
                        <input class="form-control" id="precio_compra" name="precio_compra" type="number" step="0.01" disabled
                       />
                    </div>
                    <div class="col-12 col-sm-2">
                        <label>Precio Venta Unit. </label>
                        <input class="form-control" id="precio_venta" name="precio_venta" type="number" step="0.01" 
                       />
                    </div>
            

                   
                    <div class="col-12 col-sm-2" style="display:none;" >
                        <label>* Cantidad Mayor</label>
                        <input class="form-control" id="cantidad_mayor" name="cantidad_mayor" type="number" step="0.01"  readonly />

                    </div>
                

                    <div class="col-12 col-sm-1">
          
                        <label><br>&nbsp;</label>
                        <button id="agregar_producto" name="agregar_producto" type="button" 
    class="btn btn-primary" onclick="agregarProductoDesdeFormulario()">
    Agregar
</button>

                    
                    </div>
                </div>
            </div>
            <br>
                <div class="row">
                    <table id="tablaProductos" class="table table-hover table-striped table-sm 
                    table-responsive tablaProductos" width="100%" style=" background: white;">
                    <thead style=" background-color: black; color: white;  vertical-align: middle;">
                    <th>#</th>
                    <th>CODIGO</th>
                    <th>NOMBRE</th>
                    <th>PRECIO</th>
                    <th>CANT. MAYOR</th>
                    <th>CANT. UNID.</th>
                    <th>SUBTOTAL</th>
                    <th>FECHA VENCE</th>
                    <th><button type="button" id="cancelar_compra" class="btn btn-danger">LIMPIAR</button></th>
                    <th></th>
                  
                    </thead>

                    <tbody></tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6 offset-md-6">
                        <label style="font-weight: bold; font-size: 30px; text-align: center; ">Total Bs</label>
                        <input type="text" id="total" name="total" size="7" readonly="true"
                        value="0.00" style="font-weight: bold; font-size: 30px; text-align: center; ">
                        <button type="button" id="completa_compra" class="btn btn-success">Completar compra</button>
                    </div>

                </div>
     
            </form>
        </div>

        
    </main>

<!-- Modal para buscar productos -->
<div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-labelledby="modalBuscarProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBuscarProductoLabel">Buscar Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input  type="text" class="form-control mb-3" id="buscarNombreProducto" placeholder="Escriba el nombre del producto" autofocus autocomplete="off">
        <ul class="list-group" id="listaProductos" style="max-height: 200px; overflow-y: auto;">
          <!-- Opciones de productos autocompletados aparecerán aquí -->
        </ul>
      </div>
    </div>
  </div>
</div>





<script>

$(document).ready(function () {
    // Completar compra
    $('#completa_compra').click(function (e) {
        e.preventDefault(); // Evita envío automático

        // Habilitar campos antes de enviar
        $('#precio_compra, #precio_venta').prop('disabled', false);

        let nFila = $("#tablaProductos tr").length;

        if (nFila < 2) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe agregar al menos un producto para completar la compra.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Obtener valores de precios
        let precioCompra = parseFloat($('#precio_compra').val()) || 0;
        let precioVenta = parseFloat($('#precio_venta').val()) || 0;
        let precioCompraM = parseFloat($('#precio_compra_m').val()) || 0;
        let precioVentaM = parseFloat($('#precio_venta_m').val()) || 0;

        // Validar precios
        if (precioVenta < precioCompra) {
            Swal.fire({
                icon: 'warning',
                title: 'Precio incorrecto',
                text: 'El precio de venta no puede ser menor al precio de compra.',
                confirmButtonText: 'Corregir'
            });
            return;
        }

        if (precioVentaM < precioCompraM) {
            Swal.fire({
                icon: 'warning',
                title: 'Precio por mayor incorrecto',
                text: 'El precio de venta por mayor no puede ser menor al precio de compra por mayor.',
                confirmButtonText: 'Corregir'
            });
            return;
        }

        // Si pasa validación
        $("#form_compra").submit();
    });
});

function buscarProducto(e, tagCodigo, codigo) {
    const enterKey = 13;

    if (codigo !== '' && e.which === enterKey) {
        console.log("Buscando presentación con:", codigo);

        $.ajax({
            url: '<?php echo base_url(); ?>/TemporalCompra/buscarPorCodigo/' + codigo,
            type: 'POST',
            dataType: 'json',
            data: {
                id_compra: $("#id_compra").val()
            },
          success: function (resultado) {
    if (resultado.existe) {
        const datos = resultado.datos;

        // Enviar el producto base siempre
        let idProductoBase = datos.id_producto;
        let idPresentacion = datos.id; // la presentación seleccionada (puede ser variante)

        // Guardamos ambos
        $("#id_producto").val(idProductoBase);
        $("#id_presentacion").val(idPresentacion);

        $("#nombre").val(`${datos.nombre}${datos.descripcion ? ' - ' + datos.descripcion : ''}${datos.tipo ? ' - ' + datos.tipo : ''}`);
        $("#descripcion").val(datos.descripcion);
        $("#tipo").val(datos.tipo);

        $("#precio_compra").val(parseFloat(datos.precio_compra).toFixed(2));
        $("#precio_venta").val(parseFloat(datos.precio_venta).toFixed(2));
        $("#existencias").val(parseInt(datos.cantidad_total));

        // 🔹 Ajuste: respetar cantidad escrita por el usuario
        let cantidadInput = $("#cantidad");
        let cantidadActual = parseFloat(cantidadInput.val()) || 0;

        if (cantidadActual <= 0) {
            cantidadInput.val(1); // si está vacío o 0, poner 1
        }

        // 🔹 Disparar input para que cualquier listener lo detecte
        cantidadInput.trigger("input");
        $("#montocompra").trigger("input");

        // 👇 Aquí forzamos el cálculo de cantidad mayor
        actualizarCantidadMayor();

        cantidadInput.focus();

    } else {
        Swal.fire({
            icon: 'error',
            title: 'No encontrado',
            text: resultado.error,
            confirmButtonText: 'OK'
        });
        limpiarCampos();
    }
}
,
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX:", textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo completar la búsqueda.',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
}





function agregarProductoDesdeFormulario() {
    let id_presentacion = $('#id_presentacion').val();
    let id_producto = $('#id_producto').val();
    let id_compra = $('#id_compra').val();
    let cantidad = parseFloat($('#cantidad').val()) || 0;
    let monto = parseFloat($('#montocompra').val()) || 0;
    let precio_compra = parseFloat($('#precio_compra').val()) || 0;
    let precio_venta = parseFloat($('#precio_venta').val()) || 0;
    let precio_compra_m = parseFloat($('#precio_compra_m').val()) || 0;
    let precio_venta_m = parseFloat($('#precio_venta_m').val()) || 0;
    let cantidad_mayor = parseFloat($('#cantidad_mayor').val()) || 0;

    let fecha_vencimiento = $('#fecha_vencimiento').val();
    
    let cant_u = parseFloat($('#cant_u').val()) || 0; // 🔴 Agregado
    

 let sinVencimiento = $('#sin_vencimiento').is(':checked');

if (!cantidad || cantidad <= 0) {
    Swal.fire({
        icon: 'warning',
        title: 'Cantidad inválida',
        text: 'Debe ingresar una cantidad válida.',
    });
    return;
}

if (!sinVencimiento && !fecha_vencimiento) {
    Swal.fire({
        icon: 'warning',
        title: 'Falta fecha',
        text: 'Debe ingresar una fecha de vencimiento o marcar "No requiere".',
    });
    return;
}

    const fechaVenc = new Date(fecha_vencimiento);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    if (!sinVencimiento && fecha_vencimiento) {
    const fechaVenc = new Date(fecha_vencimiento);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    if (fechaVenc < hoy) {
        Swal.fire({
            icon: 'error',
            title: 'Fecha inválida',
            text: 'La fecha de vencimiento no puede ser anterior al día actual.',
        });
        return;
    }
}



$('#agregar_producto').prop('disabled', true);

// 🔹 Imprimir todos los datos antes de enviarlos
console.log("Datos a enviar al servidor:", {
    id_presentacion: id_presentacion,
    cant_u: cant_u,
    id_compra: id_compra,
    precio_compra: precio_compra,
    precio_venta: precio_venta,
    precio_compra_m: precio_compra_m,
    precio_venta_m: precio_venta_m,
    fecha_vencimiento: fecha_vencimiento,
    cantidad_mayor: cantidad_mayor
});

$.ajax({
    url: `<?php echo base_url(); ?>/TemporalCompra/inserta/${id_presentacion}/${cant_u}/${id_compra}`,
    type: 'POST',
    data: {
        id_producto: id_producto, // 🔹 AGREGADO
        precio_compra: precio_compra,
        precio_venta: precio_venta,
        precio_compra_m: precio_compra_m,
        precio_venta_m: precio_venta_m,
        fecha_vencimiento: sinVencimiento ? null : fecha_vencimiento,
        cant_u: cant_u,
        cantidad_mayor: cantidad_mayor
    },
    success: function(resultado) {
        let res = typeof resultado === 'string' ? JSON.parse(resultado) : resultado;

        if (res.error === '') {
            $('#tablaProductos tbody').html(res.datos);
            $('#total').val(res.total);
            limpiarCampos();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res.error
            });
        }

        $('#agregar_producto').prop('disabled', false);
    },
    error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error AJAX:", textStatus, errorThrown);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al agregar el producto.'
        });
        $('#agregar_producto').prop('disabled', false);
    }
});



}







// Función para limpiar los campos después de agregar un producto
function limpiarCampos() {
    $('#id_presentacion, #codigo, #nombre, #cantidad, #precio_compra,#precio_venta, #existencias, #fecha_vencimiento').val('');
    $('#cantidad').focus(); // Focaliza la cantidad para agilizar el ingreso
}





function eliminaProductoUno(id_presentacion, id_compra) {
    $.ajax({
        url: `<?php echo base_url(); ?>/TemporalCompra/eliminarUno/${id_presentacion}/${id_compra}`,
        type: 'POST',
        success: function (resultado) {
            let res = JSON.parse(resultado);
            if (res.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.error,
                    confirmButtonText: 'OK'
                });
            } else {
                // Actualizar la tabla de productos sin enviar o completar la compra
                $("#tablaProductos tbody").html(res.datos); 
                $("#total").val(res.total);
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo eliminar el producto.',
                confirmButtonText: 'OK'
            });
        }
    });
}

// Asegura que el botón no envíe el formulario accidentalmente
$(document).on('click', '.btn-eliminar', function (e) {
    e.preventDefault(); // Previene cualquier acción de envío en los botones de eliminar
});


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$('#buscarNombreProducto').on('keyup', function () {
    let nombre = $(this).val().trim();
    if (nombre.length >= 2) {
        $.ajax({
            url: "<?php echo base_url('TemporalCompra/autocompletarPresentaciones'); ?>",
            type: "GET",
            data: { term: nombre },
            dataType: 'json',
            success: function (presentaciones) {
                let opciones = presentaciones.map(p =>
                        `<li class="list-group-item list-group-item-action"
                            onclick="seleccionarProducto(${p.id}, ${p.id_producto}, '${p.nombre_producto}', '${p.tipo}', '${p.codigo}', ${p.precio_compra}, ${p.precio_venta}, ${p.existencias})">
                            ${p.nombre_producto} - ${p.tipo} - ${p.codigo}
                        </li>`).join('');

                $('#listaProductos').html(opciones);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error:", textStatus, errorThrown);
                $('#listaProductos').html('<li class="list-group-item text-danger">Error al buscar presentaciones</li>');
            }
        });
    } else {
        $('#listaProductos').html('');
    }
});

function seleccionarProducto(idPresentacion, idProducto, nombre, tipo, codigo, precioCompra, precioVenta, existencias) {
    // 🔹 Guardar valor actual de cantidad y monto
    let cantidadActual = parseFloat($("#cantidad").val()) || 0;
    let montoActual = parseFloat($("#montocompra").val()) || 0;

    // Llenar datos del producto
    $("#id_presentacion").val(idPresentacion);
    $("#id_producto").val(idProducto);
    $("#nombre").val(`${nombre} - ${tipo}`);
    $("#codigo").val(codigo);
    $("#precio_compra").val(parseFloat(precioCompra).toFixed(2));
    $("#precio_venta").val(parseFloat(precioVenta).toFixed(2));
    $("#existencias").val(parseInt(existencias));

    // 🔹 Restaurar valor previo de cantidad y monto
    if (cantidadActual > 0) {
        $("#cantidad").val(cantidadActual).trigger('input');
    }
    if (montoActual > 0) {
        $("#montocompra").val(montoActual).trigger('input');
    }

    // 🔹 Disparar cálculo de cantidad mayor
    actualizarCantidadMayor();

    // Esperar que se cierre el modal antes de enfocar
    $('#modalBuscarProducto').one('hidden.bs.modal', function () {
        setTimeout(() => {
            $("#cantidad").trigger('focus');
        }, 100);
    });

    $('#modalBuscarProducto').modal('hide');
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function () {
    // Validar fecha de vencimiento
    $('#fecha_vencimiento').on('change', function () {
        const fechaVencimiento = new Date($(this).val());
        const fechaHoy = new Date();

        // Asegurarse de que la comparación sea solo de fechas (sin horas)
        fechaHoy.setHours(0, 0, 0, 0);

        if (fechaVencimiento < fechaHoy) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha Inválida',
                text: 'La fecha de vencimiento no puede ser anterior al día de hoy.',
                confirmButtonText: 'OK'
            });

            // Limpia el campo de fecha para forzar la corrección
            $(this).val('');
        }
    });
});
// Validar que el precio de venta no sea menor que el precio de compra
$('#precio_compra, #precio_venta').on('change', function () {
        const precioCompra = parseFloat($('#precio_compra').val()) || 0;
        const precioVenta = parseFloat($('#precio_venta').val()) || 0;

        if (precioVenta < precioCompra) {
            Swal.fire({
                icon: 'error',
                title: 'Precios Inválidos',
                text: 'El precio de venta no puede ser menor al precio de compra.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Devolver el foco al campo de precio de venta
                $('#precio_venta').focus();
            });
        }
    });



    $.ajax({
    url: '<?php echo base_url(); ?>/TemporalCompra/cargarProductos/<?php echo $id_compra; ?>',
    type: 'GET',
    success: function (res) {
        let resultado = JSON.parse(res);
        $("#tablaProductos tbody").html(resultado.datos);
        $("#total").val(resultado.total);
    }
});


$('#cancelar_compra').click(function () {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Se eliminarán todos los productos de esta compra.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo base_url(); ?>/TemporalCompra/eliminarTodo/<?php echo $id_compra; ?>',
                type: 'POST',
                success: function (response) {
                    let res = JSON.parse(response);
                    if (res.error === '') {
                        $("#tablaProductos tbody").html('');
                        $("#total").val("0.00");
                        limpiarCampos();
                        Swal.fire({
                            icon: 'success',
                            title: 'Compra cancelada',
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
                        text: 'No se pudo eliminar la compra temporal.'
                    });
                }
            });
        }
    });
});





// Escuchar cambios en cantidad, monto o margen
$('#cantidad, #montocompra, #margen').on('input', function () {
    calcularPrecios();
});

function calcularPrecios() {
    let cantidad = parseFloat($('#cantidad').val()) || 0;
    let monto = parseFloat($('#montocompra').val()) || 0;
    let idPresentacion = parseInt($('#id_presentacion').val()) || 0;
    let margenPorcentaje = parseFloat($('#margen').val()) || 13; // 👈 lee el input del margen

    // Convertir porcentaje a decimal
    let margen = margenPorcentaje / 100;

    // Limpiar estilos primero
    $('#precio_compra_m, #precio_venta_m, #precio_venta').removeClass('campo-valido');

    if (cantidad > 0 && monto > 0 && idPresentacion > 0) {
        $.ajax({
            url: '<?php echo base_url(); ?>/TemporalCompra/jerarquiaUnidades/' + idPresentacion,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res.success) return limpiarCampos();

                const jerarquia = res.jerarquia;
                const indexActual = jerarquia.findIndex(p => p.id == idPresentacion);
                if (indexActual === -1) return limpiarCampos();

                let unidadesPorPresentacion = 1;
                for (let i = indexActual; i < jerarquia.length; i++) {
                    unidadesPorPresentacion *= jerarquia[i].cantidad_unidades;
                }

                let totalUnidades = cantidad * unidadesPorPresentacion;
                let precioCompraUnidad = monto / totalUnidades;
                let precioVentaUnidad = precioCompraUnidad * (1 + margen);

                let precioCompraMayor = '';
                let precioVentaMayor = '';
                let agrupado = false;

                const esUnidad = indexActual === jerarquia.length - 1;

                if (esUnidad) {
                    for (let i = indexActual - 1; i >= 0; i--) {
                        let unidadesParaAgrupar = 1;
                        for (let j = i; j < indexActual; j++) {
                            unidadesParaAgrupar *= jerarquia[j].cantidad_unidades;
                        }

                        if (cantidad % unidadesParaAgrupar === 0) {
                            let cantidadMayor = cantidad / unidadesParaAgrupar;
                            precioCompraMayor = monto / cantidadMayor;
                            precioVentaMayor = precioCompraMayor * (1 + margen);
                            agrupado = true;
                            break;
                        }
                    }
                } else {
                    precioCompraMayor = monto / cantidad;
                    precioVentaMayor = precioCompraMayor * (1 + margen);
                    agrupado = true;
                }

                // Mostrar valores
                $('#precio_compra').val(precioCompraUnidad.toFixed(2));
                $('#precio_venta').val(precioVentaUnidad.toFixed(2));
                $('#cant_u').val(totalUnidades);
                $('#precio_compra_m').val(precioCompraMayor !== '' ? precioCompraMayor.toFixed(2) : '');
                $('#precio_venta_m').val(precioVentaMayor !== '' ? precioVentaMayor.toFixed(2) : '');
            },
            error: limpiarCampos
        });
    } else {
        limpiarCampos();
    }

    function limpiarCampos() {
        $('#precio_compra').val('');
        $('#precio_venta').val('');
        $('#cant_u').val('');
        $('#precio_compra_m').val('');
        $('#precio_venta_m').val('');
    }
}

function calcularCantidadMayor(cantidad, jerarquia, idPresentacionSeleccionado) {
    const indiceActual = jerarquia.findIndex(j => j.id == idPresentacionSeleccionado);
    if (indiceActual === -1) return cantidad;

    // Solo convertir si es el hijo más menor de la jerarquía
    if (indiceActual === jerarquia.length - 1 && indiceActual > 0) {
        const padreInmediato = jerarquia[indiceActual - 1];
        const factor = padreInmediato.cantidad_unidades;

        // Convertir aunque no sea exacto
        return cantidad / factor;
    }

    // Si no es el más menor → dejar igual
    return cantidad;
}



function actualizarCantidadMayor() {
    const idPresentacion = parseInt($('#id_presentacion').val());
    const cantidad = parseFloat($('#cantidad').val()) || 0;

    if (!idPresentacion || cantidad <= 0) {
        $('#cantidad_mayor').val('');
        return;
    }

    $.ajax({
        url: `<?php echo base_url(); ?>/TemporalCompra/jerarquiaUnidades/${idPresentacion}`,
        method: 'GET',
        dataType: 'json',
        success: function(res) {
            if (!res.success) {
                $('#cantidad_mayor').val('');
                return;
            }
            const jerarquia = res.jerarquia;
            const cantidadMayor = calcularCantidadMayor(cantidad, jerarquia, idPresentacion);
            $('#cantidad_mayor').val(cantidadMayor.toFixed(2));
        },
        error: function() {
            $('#cantidad_mayor').val('');
        }
    });
}

$('#id_presentacion, #cantidad').on('change keyup', actualizarCantidadMayor);

</script>
