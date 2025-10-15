<div id="layoutSidenav_content">
    <main>
    <script>
    async function mostrarAlertas() {
        // Alerta para productos con stock mínimo
        <?php if ($minimos > 0): ?>
            await Swal.fire({
                icon: 'info',
                title: 'Productos con Stock Mínimo',
                text: 'Hay <?= $minimos; ?> productos con stock mínimo.',
                confirmButtonText: 'Revisar'
            });
        <?php endif; ?>

        // Alerta de productos por vencer en los próximos 20 días
        <?php if ($cantidadPorExpirar > 0): ?>
            await Swal.fire({
                icon: 'warning',
                title: 'Productos por Vencer',
                text: 'Hay <?= $cantidadPorExpirar; ?> productos próximos a vencer.',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // Alerta para productos expirados
        <?php if (!empty($productosExpirados)): ?>
            await Swal.fire({
                icon: 'error',
                title: 'Productos Expirados',
                html: `<ul>
                    <?php foreach ($productosExpirados as $producto): ?>
                        <li><?= $producto["nombre"] ?> (Expiró el: <?= $producto["fecha_vencimiento"] ?>)</li>
                    <?php endforeach; ?>
                </ul>`,
                confirmButtonText: 'Revisar'
            });
        <?php endif; ?>
    }

    // Ejecutar las alertas al cargar la página
    document.addEventListener('DOMContentLoaded', mostrarAlertas);
</script>



        <div class="container-fluid">
            <br>
            <h3 style="text-align: center;">Reportes Operativos</h3>

            <div class="row">
                <div class="col-4">
                    <div class="card" style="background-color: #6FEFB4;">
                        <div class="card-body">
                            <?php echo $total; ?> Total de Productos
                        </div>
                        <a class="card-footer " href="<?php echo base_url() ?>/productos">Ver detalles</a>
                    </div>
                </div>

                <div class="col-4">
                    <div class="card" style="background-color: #F9E065">
                        <div class="card-body">
                            <?php echo $totalVentas['total']; ?> Monto Ventas del Día
                        </div>
                        <a class="card-footer " href="<?php echo base_url() ?>/ventas">Ver detalles</a>
                    </div>
                </div>

                <div class="col-4">
                    <div class="card" style="background-color: #FF8A66; ">
                        <div class="card-body">
                            <?php echo $minimos; ?> Productos con Stock mínimo
                        </div>
                        <a class="card-footer " href="<?php echo base_url() ?>/productos/mostrarMinimos">Ver detalles</a>
                    </div>
                </div>
            </div>
               <br>
            <div class="row">
                <div class="col-4">
                    <div class="card" style="background-color: #FF8A66; ">
                    <br>
                        <div class="card-body">
                            <?php echo $cantidadPorExpirar; ?> Productos por Vencer
                        </div>
                        <a class="card-footer " href="<?php echo base_url() ?>/productos/productosPorExpirar">Ver detalles</a>
                    </div>
                </div>



                <div class="col-4">
        <div class="card" style=" background-color: #C69EE1; ">
            <div class="card-body">
                <?php
                // Mostrar el nombre del producto más vendido
                if (!empty($productosMasVendidos)) {
                    $producto_top = $productosMasVendidos[0]; // El más vendido está en la primera posición
                    echo"El producto mas vendido  ";
                    echo"<br>";
                    echo $producto_top->nombre; 
                    // Solo mostrar el nombre del producto más vendido
                } else {
                    echo 'No hay ventas registradas.';
                }
                ?>
            </div>
            <a class="card-footer text-white" href="<?php echo base_url() ?>/productos/generaProductosMasVendidosPdf">Ver detalles</a>
        </div>
    </div>

    <!-- Producto con mayor margen de ganancia -->
    <div class="col-4">
        <div class="card " style=" background-color: #73BFF0;">
            <div class="card-body" style="">
                <?php
                // Mostrar el nombre del producto con mayor margen de ganancia
                if (!empty($margenGanancia)) {
                    $producto_margen = $margenGanancia[0]; 
                    echo"El producto que mas genera ";// El de mayor margen está en la primera posición
                    echo"<br>";
                    echo $producto_margen->nombre; // Solo mostrar el nombre del producto con mayor margen
                } else {
                    echo 'No hay margen de ganancia disponible.';
                }
                ?>
            </div>
            <a class="card-footer text-white" href="<?php echo base_url() ?>/productos/generaMargenGananciaPdf">Ver detalles</a>
        </div>
    </div>


                





        </div>
        <br>
        <h3 style="text-align: center;">Gráfica de Ventas Semanal</h3>
            <!-- Modificar para que el gráfico sea más ancho y centrado -->
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div style="max-width: 1000px; margin: 0 auto;">
                        <canvas id="myChart" style="width: 1000px; height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
    // Conexión a la base de datos
    $db = \Config\Database::connect();
    $db->query("SET lc_time_names = 'es_ES';");

    // Array de días en español
    $dias_semana_es = [
        'Monday'    => 'LUNES',
        'Tuesday'   => 'MARTES',
        'Wednesday' => 'MIÉRCOLES',
        'Thursday'  => 'JUEVES',
        'Friday'    => 'VIERNES',
        'Saturday'  => 'SÁBADO',
        'Sunday'    => 'DOMINGO'
    ];

    // Generar una lista de los últimos 7 días
    $fechas = [];
    for ($i = 6; $i >= 0; $i--) {
        $fecha = date('Y-m-d', strtotime("-$i days"));
        $dia_semana_en = date('l', strtotime($fecha));
        $dia_semana_es = $dias_semana_es[$dia_semana_en];
        $fechas[$fecha] = ['dia_semana' => $dia_semana_es, 'monto_total' => 0];
    }

    // Consulta para obtener las ventas de los últimos 7 días
    $sql = "
    SELECT 
        UPPER(DAYNAME(v.fecha_alta)) AS dia_semana,
        DATE(v.fecha_alta) AS fecha,
        SUM(v.total) AS monto_total
    FROM 
        ventas v
    WHERE 
        v.fecha_alta >= CURDATE() - INTERVAL 7 DAY
        AND v.activo = 1
    GROUP BY 
        DATE(v.fecha_alta)
    ORDER BY 
        v.fecha_alta ASC;
    ";

    // Ejecutar la consulta
    $query = $db->query($sql);
    $rs = $query->getResultArray();

    // Rellenar las fechas generadas con los datos de ventas obtenidos
    foreach ($rs as $row) {
        $fecha = $row['fecha'];
        if (isset($fechas[$fecha])) {
            $fechas[$fecha]['monto_total'] = (int)$row['monto_total'];
        }
    }

    // Separar los datos en arrays para la gráfica
    $data = [];
    $data2 = [];
    foreach ($fechas as $fecha => $info) {
        $data[] = $info['monto_total'];
        $data2[] = $info['dia_semana'];
    }

    // Convertir los datos a JSON para la gráfica
    $data_json = json_encode($data);
    $data_json2 = json_encode($data2);
    ?>

    <script>
        var montoVenta = <?php echo $data_json; ?>;
        var diaSemana = <?php echo $data_json2; ?>;
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: diaSemana,
                datasets: [{
                    label: 'Ventas por día de la semana',
                    data: montoVenta,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(39, 10, 235, 0.2)',
                        'rgba(1, 296, 86, 0.2)',
                        'rgba(10, 196, 56, 0.2)',
                        'rgba(100, 96, 156, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(39, 10, 235, 1)',
                        'rgba(1, 296, 86, 1)',
                        'rgba(10, 196, 56, 1)',
                        'rgba(100, 96, 156, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


