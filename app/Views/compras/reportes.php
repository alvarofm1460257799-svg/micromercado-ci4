<div id="layoutSidenav_content">
    <main>
        <br>
        <div class="container">
            <h2>Reporte de Compras por Rango de Fechas</h2>

            <form method="post" action="<?php echo base_url('compras/generaReportePDF'); ?>" onsubmit="return validarFechas()">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha de Fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" required>
                        </div>
                 
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <br>
                        <button type="submit" class="btn btn-primary btn-sm">Generar Reporte PDF</button>
                        </div>
                      
                    </div>
           </div>
<br>
<h2>Gráfica de Compras a Proveedores</h2>
<div class="container mt-4" id="graficaContainer">
    <canvas id="graficaCompras" width="415" height="168"></canvas>
</div>

           </div>
    </main>
</div>


<script>
function obtenerDatosGrafica() {
    var url = "<?php echo base_url('compras/obtenerDatosGraficaProveedores'); ?>";

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
            } else {
                mostrarGrafica(data.proveedores, data.compras);
            }
        })
        .catch(error => {
            console.error('Error de conexión:', error);
            Swal.fire('Error', 'Hubo un problema al conectarse con el servidor.', 'error');
        });
}

function mostrarGrafica(proveedores, compras) {
    var ctx = document.getElementById('graficaCompras').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: proveedores,
            datasets: [{
                label: 'Total de Compras (Bs)',
                data: compras,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: 'black'
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Proveedor',
                        color: 'black'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Compras (Bs)',
                        color: 'black'
                    }
                }
            }
        }
    });
}

// Llamar a la función al cargar la página
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

