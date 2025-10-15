<div id="layoutSidenav_content">
    <main>
        <br>
        <div class="container">
            <h3 class="text-center">Reporte de Productos Vencidos / Por Vencer / Desechados</h3>

            <div class="row">
                <div class="col-md-3">
                    <label for="tipo_reporte">Tipo de reporte:</label>
                    <select id="tipo_reporte" class="form-control" onchange="obtenerDatosVencimiento()">
                        <option value="vencidos">Vencidos (activos)</option>
                        <option value="desechados">Desechados</option>
                        <option value="por_vencer">Por vencer</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="categoria_filtro">Categoría:</label>
                    <select id="categoria_filtro" class="form-control" onchange="obtenerDatosVencimiento()">
                        <option value="todas">Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>"><?= esc($categoria['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="anio_reporte">Año:</label>
                    <select id="anio_reporte" class="form-control" onchange="obtenerDatosVencimiento()">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-2 text-center">
                    <br>
                    <button class="btn btn-danger mt-2" onclick="generarPDF()">Generar PDF</button>

                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" onchange="obtenerDatosVencimiento()">
                </div>

                <div class="col-md-3">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" onchange="obtenerDatosVencimiento()">
                </div>
            </div>

            <div class="container mt-4">
                <canvas id="graficoVencimiento"></canvas>
            </div>
        </div>
    </main>
</div>

<script>
let grafico = null;

function obtenerDatosVencimiento() {
    const tipo = document.getElementById('tipo_reporte').value;
    const anio = document.getElementById('anio_reporte').value;
    const categoria = document.getElementById('categoria_filtro').value;
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;

    let url = `<?= base_url('lotesProductos/datosVencimiento') ?>?tipo=${tipo}&anio=${anio}`;
    if (categoria !== 'todas') url += `&categoria=${categoria}`;
    if (fechaInicio && fechaFin) url += `&desde=${fechaInicio}&hasta=${fechaFin}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (grafico) grafico.destroy();

            const mesesNombres = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                                  "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            const etiquetas = data.meses.map(m => {
                const [year, mes] = m.split("-");
                return mesesNombres[parseInt(mes) - 1] + ' ' + year;
            });

            let datasets = [];

            if (tipo === 'vencidos') {
                datasets.push({
                    label: 'Vencidos (activos)',
                    data: data.vencidos_activos,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                });
            } else if (tipo === 'desechados') {
                datasets.push({
                    label: 'Desechados',
                    data: data.vencidos_desechados,
                    backgroundColor: 'rgba(100, 100, 100, 0.6)',
                    borderColor: 'rgba(100, 100, 100, 1)',
                    borderWidth: 1
                });
            } else if (tipo === 'por_vencer') {
                datasets.push({
                    label: 'Productos por vencer',
                    data: data.cantidades,
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                });
            }

            const ctx = document.getElementById('graficoVencimiento').getContext('2d');
            grafico = new Chart(ctx, {
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
                            text: `${document.getElementById('tipo_reporte').selectedOptions[0].text} - ${anio}`,
                            font: { size: 16 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Cantidad' }
                        },
                        x: {
                          
                        }
                    }
                }
            });
        });
}


function generarPDF() {
    const tipo = document.getElementById('tipo_reporte').value;
    const anio = document.getElementById('anio_reporte').value;
    const categoria = document.getElementById('categoria_filtro').value;
    const desde = document.getElementById('fecha_inicio').value;
    const hasta = document.getElementById('fecha_fin').value;

    let url = `<?= base_url('lotesProductos/reporteVencimientoPDF') ?>?tipo=${tipo}&anio=${anio}&categoria=${categoria}`;
    if (desde && hasta) url += `&desde=${desde}&hasta=${hasta}`;

    window.open(url, '_blank');
}


</script>
