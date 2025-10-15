<?php 
$id_compra = uniqid();
?>
<br>
<div id="layoutSidenav_content">
    <main>
    <div class="container-fluid">
        <p></p>
    <form method="POST" id="form_compra" name="form_compra" action="<?php echo base_url(); ?>/compras/generarKardexPdf" autocomplete="off">
    <?php csrf_field(); ?>
    <input type="hidden" id="id_producto" name="id_producto">
    <input type="hidden" id="id_compra" name="id_compra" value="<?php echo $id_compra; ?>">

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label>* Código</label>
                <div class="input-group">
                    <input class="form-control" id="codigo" name="codigo" type="number" 
                           placeholder="Escribe el código y presiona Enter" 
                           onkeyup="buscarProducto(event, this, this.value)" autofocus require />
                    <button class="btn btn-secondary" type="button" id="btnBuscarProducto" 
                            data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <label for="codigo" id="resultado_error" style="color: red;"></label>
            </div>
        </div>
    </div>



    <div class="form-group mt-3">
        <button type="submit" class="btn btn-primary">Generar Kardex Completo</button>
      
    </div>
    </div>
</form>

</main>
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
        <input type="text" class="form-control mb-3" id="buscarNombreProducto" placeholder="Escriba el nombre del producto" autofocus autocomplete="off">
        <ul class="list-group" id="listaProductos" style="max-height: 200px; overflow-y: auto;">
          <!-- Opciones de productos autocompletados aparecerán aquí -->
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
function calcularKardex() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;

    if (fechaInicio === '' || fechaFin === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Fechas incompletas',
            text: 'Por favor selecciona ambas fechas para continuar.',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#3498db',
            timerProgressBar: true
        });
        return;
    }

    const fechaInicioObj = new Date(fechaInicio);
    const fechaFinObj = new Date(fechaFin);

    if (fechaInicioObj > fechaFinObj) {
        Swal.fire({
            icon: 'error',
            title: 'Error en las fechas',
            text: 'La fecha de inicio no puede ser posterior a la fecha de fin.',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#e74c3c',
            timerProgressBar: true
        });
        return;
    }

    const form = document.getElementById('form_compra');
    form.action = '<?php echo base_url(); ?>/compras/generarKardexPdf?conFechas=true';
    form.submit();
}

function buscarProducto(e, tagCodigo, codigo) {
    if (e.keyCode === 13 && codigo.trim() !== '') {
        $.ajax({
            url: '<?php echo base_url(); ?>/productos/buscarPorCodigo/' + codigo,
            dataType: 'json',
            success: function(resultado) {
                if (resultado.existe) {
                    $('#id_producto').val(resultado.datos.id);
                    $('#codigo').val(resultado.datos.codigo);
                    $('#modalBuscarProducto').modal('hide');
                } else {
                    $('#resultado_error').text('Producto no encontrado.');
                }
            }
        });
    }
}

$('#buscarNombreProducto').on('keyup', function () {
    const nombre = $(this).val().trim();
    if (nombre.length >= 2) {
        $.ajax({
            url: '<?php echo base_url(); ?>/productos/autocompletar',
            type: 'GET',
            data: { term: nombre },
            dataType: 'json',
            success: function (productos) {
                const opciones = productos.map(p => `
                    <li class="list-group-item list-group-item-action"
                        onclick="seleccionarProducto(${p.id}, '${p.nombre}', '${p.codigo}')">
                        ${p.nombre} - ${p.codigo}
                    </li>
                `).join('');
                $('#listaProductos').html(opciones);
            }
        });
    } else {
        $('#listaProductos').html('');
    }
});
document.getElementById('modalBuscarProducto').addEventListener('shown.bs.modal', function () {
    document.getElementById('buscarNombreProducto').focus();
});


function seleccionarProducto(id, nombre, codigo) {
    $('#id_producto').val(id);
    $('#codigo').val(codigo);
    $('#modalBuscarProducto').modal('hide');
    $('#fecha_inicio').focus();
}
</script>
