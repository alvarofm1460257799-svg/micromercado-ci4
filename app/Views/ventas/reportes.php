

<div id="layoutSidenav_content">
    <main>
        <br>
        <div class="container">
            <h3 style="text-align: center;">Reporte de Ventas por Rango de Fechas</h3>

            <form method="post" action="<?php echo base_url('ventas/generar'); ?>" onsubmit="return validarFechas()">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipo_reporte">Tipo de reporte:</label>
                            <select name="tipo_reporte" id="tipo_reporte" class="form-control" required onchange="actualizarGrafica()">
                                <option value="">Seleccionar tipo de reporte</option>
                                <option value="ventas">Ventas</option>
                                <option value="compras">Compras</option>
                                <option value="ganancias">Ganancias</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="anio">Año:</label>
                            <select name="anio" id="anio" class="form-control" onchange="obtenerDatosGrafica()">
                                <?php
                                    $anio_actual = date('Y');
                                    for ($i = $anio_actual; $i >= $anio_actual - 5; $i--) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha de Fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm">Generar Reporte PDF</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ✅ Único canvas para la gráfica, fuera del formulario -->
            <div class="container mt-4" id="graficaContainer">
                <canvas id="graficaUnica"></canvas>
            </div>
        </div>
    </main>
</div>





<script>
let datosGlobales = null;
let graficaActiva = null;

function obtenerDatosGrafica() {
    const anio = document.getElementById('anio').value || new Date().getFullYear();
    const url = "<?php echo base_url('ventas/obtenerDatosGraficaMensual'); ?>?anio=" + anio;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            datosGlobales = data;
            actualizarGrafica(); // Pinta con los datos nuevos
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al obtener datos de la gráfica.', 'error');
        });
}

// Al cargar la página, carga datos iniciales
document.addEventListener('DOMContentLoaded', () => {
    obtenerDatosGrafica();
});

function actualizarGrafica() {
    if (!datosGlobales) return;

    const tipo = document.getElementById('tipo_reporte').value;
    let titulo = '';
    let datasets = [];

    const etiquetas = datosGlobales.meses.map(m => {
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        return meses[parseInt(m.split("-")[1]) - 1];
    });

    if (tipo === 'ventas') {
        titulo = 'Ventas Mensuales (Bs)';
        datasets = [
            {
                label: 'Ventas normales',
                data: datosGlobales.ventas,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Ventas sin stock',
                data: datosGlobales.ventas_sin_stock,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ];
    } else if (tipo === 'ganancias') {
        titulo = 'Ganancias Mensuales (Bs)';
        datasets = [
            {
                label: 'Ganancias normales',
                data: datosGlobales.ganancias,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            {
                label: 'Ganancias sin stock',
                data: datosGlobales.ganancias_sin_stock,
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }
        ];
    } else if (tipo === 'compras') {
        titulo = 'Compras Mensuales (Bs)';
        datasets = [{
            label: 'Compras',
            data: datosGlobales.compras,
            backgroundColor: 'rgba(153, 102, 255, 0.6)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }];
    } else {
        return;
    }

    if (graficaActiva) graficaActiva.destroy();

    const ctx = document.getElementById('graficaUnica').getContext('2d');
    graficaActiva = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: titulo,
                    font: { size: 16 },
                    color: 'black'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    stacked: false,
                    title: {
                        display: true,
                        text: 'Mes',
                        color: 'black'
                    }
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Monto (Bs)',
                        color: 'black'
                    }
                }
            }
        }
    });
}



// Al cargar la página
obtenerDatosGrafica();





    function validarFechas() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        if (fechaInicio > fechaFin) {
            Swal.fire({
                icon: 'error',
                title: 'Fechas inválidas',
                text: 'La fecha de inicio no puede ser mayor que la fecha de fin.',
                confirmButtonText: 'Ok'
            });
            return false;
        }
        return true;
    }
</script>
