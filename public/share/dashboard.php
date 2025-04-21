<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

requireRole(['1']); 

$miClase = new user();
$estadisticas = $miClase->obtenerEstadisticasActualizaciones();
$estadisticasUsuarios = $miClase->obtenerEstadisticasPorUsuario();
$totalesRegistros = $miClase->obtenerTotalRegistros();

// Procesar fechas del filtro
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'));
$estadisticasPorFecha = $miClase->obtenerEstadisticasPorFecha($fecha_inicio, $fecha_fin);

// Preparar datos para los gráficos
$datosGraficoBarras = [
    'labels' => array_column($estadisticas, 'tipo_tabla'),
    'actualizados' => array_column($estadisticas, 'total_registros_actualizados'),
    'nuevos' => array_column($estadisticas, 'total_registros_nuevos')
];

$datosGraficoLineas = [];
foreach ($estadisticasPorFecha as $estadistica) {
    $datosGraficoLineas[$estadistica['fecha']][$estadistica['tipo_tabla']] = [
        'actualizados' => $estadistica['actualizados'],
        'nuevos' => $estadistica['nuevos']
    ];
}

$actualizaciones = obtenerUltimasActualizaciones($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Actualizaciones</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }

        .last-update {
            color: #666;
            font-size: 0.9em;
        }

        .updates-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .updates-table th,
        .updates-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .updates-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .updates-table tr:hover {
            background-color: #f9f9f9;
        }

        .chart-container {
            position: relative;
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chart-wrapper {
            height: 400px;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-box h4 {
            margin: 0;
            color: #666;
        }

        .stat-box .number {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }

        .filtros-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filtros-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .filtro-grupo {
            flex: 1;
        }

        .filtro-grupo label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        .filtro-grupo input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-filtrar {
            padding: 8px 20px;
            background-color: #4a6fa5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-filtrar:hover {
            background-color: #3a5982;
        }
    </style>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <div class="dashboard-container">
        <h1>Dashboard de Actualizaciones</h1>
        
        <!-- Filtros de fecha -->
        <div class="filtros-container">
            <form class="filtros-form" id="filtrosFecha">
                <div class="filtro-grupo">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           value="<?php echo $fecha_inicio; ?>" 
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="filtro-grupo">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           value="<?php echo $fecha_fin; ?>" 
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="btn-filtrar">Filtrar</button>
            </form>
        </div>

        <!-- Resumen de estadísticas -->
        <div class="stats-summary">
            <?php foreach($totalesRegistros as $tabla => $total): ?>
            <div class="stat-box">
                <h4>Total <?php echo strtoupper($tabla); ?></h4>
                <div class="number"><?php echo number_format($total); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Gráfico de barras -->
        <div class="chart-container">
            <h2>Registros por Tipo de Tabla</h2>
            <div class="chart-wrapper">
                <canvas id="registrosChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de líneas -->
        <div class="chart-container">
            <h2>Actividad en los Últimos 30 Días</h2>
            <div class="chart-wrapper">
                <canvas id="actividadChart"></canvas>
            </div>
        </div>

        <div class="stats-grid">
            <?php foreach(['CDP', 'CRP', 'OP'] as $tipo): 
                $ultimaActualizacion = array_filter($actualizaciones, function($a) use ($tipo) {
                    return $a['tipo_tabla'] === $tipo;
                });
                $ultima = !empty($ultimaActualizacion) ? reset($ultimaActualizacion) : null;
            ?>
            <div class="stat-card">
                <h3>Tabla <?php echo $tipo; ?></h3>
                <?php if ($ultima): ?>
                    <p class="last-update">
                        Última actualización: <?php echo date('d/m/Y H:i', strtotime($ultima['fecha_actualizacion'])); ?>
                    </p>
                    <p>Registros nuevos: <?php echo $ultima['registros_nuevos']; ?></p>
                    <p>Registros actualizados: <?php echo $ultima['registros_actualizados']; ?></p>
                <?php else: ?>
                    <p>No hay actualizaciones registradas</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <h2>Historial de Actualizaciones</h2>
        <table class="updates-table">
            <thead>
                <tr>
                    <th>Tabla</th>
                    <th>Archivo</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Registros Nuevos</th>
                    <th>Registros Actualizados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($actualizaciones as $actualizacion): ?>
                <tr>
                    <td><?php echo htmlspecialchars($actualizacion['tipo_tabla']); ?></td>
                    <td><?php echo htmlspecialchars($actualizacion['nombre_archivo']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($actualizacion['fecha_actualizacion'])); ?></td>
                    <td><?php echo htmlspecialchars($actualizacion['usuario']); ?></td>
                    <td><?php echo $actualizacion['registros_nuevos']; ?></td>
                    <td><?php echo $actualizacion['registros_actualizados']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Gráfico de barras
    const ctxBarras = document.getElementById('registrosChart').getContext('2d');
    new Chart(ctxBarras, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($datosGraficoBarras['labels']); ?>,
            datasets: [{
                label: 'Registros Actualizados',
                data: <?php echo json_encode($datosGraficoBarras['actualizados']); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Registros Nuevos',
                data: <?php echo json_encode($datosGraficoBarras['nuevos']); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de líneas
    const ctxLineas = document.getElementById('actividadChart').getContext('2d');
    new Chart(ctxLineas, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($datosGraficoLineas)); ?>,
            datasets: [{
                label: 'CDP',
                data: <?php echo json_encode(array_map(function($fecha) use ($datosGraficoLineas) {
                    return isset($datosGraficoLineas[$fecha]['CDP']) ? 
                        $datosGraficoLineas[$fecha]['CDP']['actualizados'] + $datosGraficoLineas[$fecha]['CDP']['nuevos'] : 0;
                }, array_keys($datosGraficoLineas))); ?>,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            }, {
                label: 'CRP',
                data: <?php echo json_encode(array_map(function($fecha) use ($datosGraficoLineas) {
                    return isset($datosGraficoLineas[$fecha]['CRP']) ? 
                        $datosGraficoLineas[$fecha]['CRP']['actualizados'] + $datosGraficoLineas[$fecha]['CRP']['nuevos'] : 0;
                }, array_keys($datosGraficoLineas))); ?>,
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
            }, {
                label: 'OP',
                data: <?php echo json_encode(array_map(function($fecha) use ($datosGraficoLineas) {
                    return isset($datosGraficoLineas[$fecha]['OP']) ? 
                        $datosGraficoLineas[$fecha]['OP']['actualizados'] + $datosGraficoLineas[$fecha]['OP']['nuevos'] : 0;
                }, array_keys($datosGraficoLineas))); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const formFiltros = document.getElementById('filtrosFecha');
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');

        // Validar fechas
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && this.value > fechaFin.value) {
                alert('La fecha de inicio no puede ser posterior a la fecha fin');
                this.value = fechaFin.value;
            }
        });

        fechaFin.addEventListener('change', function() {
            if (fechaInicio.value && this.value < fechaInicio.value) {
                alert('La fecha fin no puede ser anterior a la fecha de inicio');
                this.value = fechaInicio.value;
            }
        });

        // Manejar el envío del formulario
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const queryParams = new URLSearchParams(window.location.search);
            queryParams.set('fecha_inicio', fechaInicio.value);
            queryParams.set('fecha_fin', fechaFin.value);
            
            window.location.href = window.location.pathname + '?' + queryParams.toString();
        });

        // Actualizar gráficos cuando cambian las fechas
        function actualizarGraficos() {
            // ... existing chart code ...
            // Los gráficos se actualizarán automáticamente al recargar la página
        }
    });
    </script>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>
