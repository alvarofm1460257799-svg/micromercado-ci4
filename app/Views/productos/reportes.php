<div id="layoutSidenav_content">
    <main style="width: 100%; height: 100%; padding: 20px;">

        <!-- Botones de radio estilizados para seleccionar el reporte -->
        <div style="margin-bottom: 20px; display: flex; gap: 20px;">
            <label class="radio-container">Reporte de Stock Mínimo
                <input type="radio" name="reporte" value="minimos" checked>
                <span class="checkmark"></span>
            </label>
            <label class="radio-container">Reporte de Productos por Expirar
                <input type="radio" name="reporte" value="expirar">
                <span class="checkmark"></span>
            </label>
            <label class="radio-container">Reporte de Productos mas Vendidos
                <input type="radio" name="reporte" value="masvendidos">
                <span class="checkmark"></span>
            </label>
            <label class="radio-container">Reporte Ganancia por producto
                <input type="radio" name="reporte" value="margen">
                <span class="checkmark"></span>
            </label>
        </div>

        <!-- Iframes ocultos por defecto, excepto el primero -->
        <iframe id="iframeMinimos" style="width: 100%; height: 100%;"  
            src="<?php echo base_url() . '/productos/generaMinimosPdf'; ?>"></iframe>

        <iframe id="iframeExpirar" style="width: 100%; height: 100%; display: none;"  
            src="<?php echo base_url() . '/productos/generaPorExpirar'; ?>"></iframe>

        <iframe id="iframeMasVendidos" style="width: 100%; height: 100%; display: none;"  
            src="<?php echo base_url() . '/productos/generaProductosMasVendidosPdf'; ?>"></iframe>

        <iframe id="iframeMargen" style="width: 100%; height: 100%; display: none;"  
            src="<?php echo base_url() . '/productos/generaMargenGananciaPdf'; ?>"></iframe>
    </main>
</div>

<!-- Estilos CSS personalizados -->
<style>
    .radio-container {
        display: inline-block;
        position: relative;
        padding-left: 35px;
        margin-right: 20px;
        cursor: pointer;
        font-size: 18px;
        user-select: none;
        color: #333;
    }

    /* Ocultar el botón de radio original */
    .radio-container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Crear el "checkmark" personalizado */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border-radius: 50%;
        transition: background-color 0.3s ease;
    }

    /* Cuando el botón está seleccionado, cambiar el color de fondo */
    .radio-container input:checked ~ .checkmark {
        background-color: #4CAF50;
    }

    /* Agregar un estilo de círculo interior cuando se selecciona */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Mostrar el círculo interior cuando está seleccionado */
    .radio-container input:checked ~ .checkmark:after {
        display: block;
    }

    /* Estilo del círculo interior */
    .radio-container .checkmark:after {
        top: 9px;
        left: 9px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
    }

    /* Cambiar el color del texto cuando está seleccionado */
    .radio-container input:checked ~ .checkmark {
        background-color: #28a745;
    }

    /* Efecto hover en el botón de radio */
    .radio-container:hover input ~ .checkmark {
        background-color: #ccc;
    }

</style>

<!-- JavaScript para controlar los iframes -->
<script>
    document.querySelectorAll('input[name="reporte"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            mostrarIframe(this.value);
        });
    });

    function mostrarIframe(valor) {
        document.getElementById('iframeMinimos').style.display = 'none';
        document.getElementById('iframeExpirar').style.display = 'none';
        document.getElementById('iframeMasVendidos').style.display = 'none';
        document.getElementById('iframeMargen').style.display = 'none';

        if (valor === 'minimos') {
            document.getElementById('iframeMinimos').style.display = 'block';
        } else if (valor === 'expirar') {
            document.getElementById('iframeExpirar').style.display = 'block';
        } else if (valor === 'masvendidos') {
            document.getElementById('iframeMasVendidos').style.display = 'block';
        } else if (valor === 'margen') {
            document.getElementById('iframeMargen').style.display = 'block';
        }
    }
</script>
