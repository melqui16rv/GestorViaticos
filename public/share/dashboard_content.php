<?php


require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/graficas.php';



$miClase = new user();
$estadisticas = $miClase->obtenerEstadisticasActualizaciones();
$estadisticasUsuarios = $miClase->obtenerEstadisticasPorUsuario();
$totalesRegistros = $miClase->obtenerTotalRegistros();
$actualizaciones = $miClase->obtenerUltimasActualizaciones(); // Cambiado para usar el método de la clase

$miGraficas = new graficas();
$conteoCDP = $miGraficas->contarRegistrosPorDependenciaCDP();
$conteoCRP = $miGraficas->contarRegistrosPorDependenciaCRP();
$conteoOP  = $miGraficas->contarRegistrosPorDependenciaOP();

// Mostrar totales globales de los conteos por dependencia para validación
$totalCDP = array_sum(array_column($conteoCDP, 'total'));
$totalCRP = array_sum(array_column($conteoCRP, 'total'));
$totalOP  = array_sum(array_column($conteoOP, 'total'));

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

?>
<div class="dashboard-container" id="dashboardContent">    
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
    <div class="stats-summary" id="statsSummary">
        <?php
        $labels = ['cdp' => 'CDP', 'crp' => 'RP', 'op' => 'OP'];
        $first = true;
        foreach ($labels as $key => $label) {
            echo '<div class="stat-box stat-selectable'.($first ? ' active' : '').'" data-tipo="'.$key.'"><h4>Total ' . $label . '</h4><div class="number">' . number_format($totalesRegistros[$key] ?? 0) . '</div></div>';
            $first = false;
        }
        ?>
    </div>

    <!-- Conteo de registros por dependencia: solo uno visible según selección -->
    <div id="conteoDependenciaCDP" class="conteo-dependencia" style="display:block;">
        <h3 style="margin-top:2em;">Registros por Dependencia (CDP)</h3>
        <div class="stats-summary">
            <?php foreach($conteoCDP as $dep): ?>
            <div class="stat-box">
                <h4><?php echo htmlspecialchars($dep['nombre_dependencia']); ?> (<?php echo htmlspecialchars($dep['codigo_dependencia']); ?>)</h4>
                <div class="number"><?php echo number_format($dep['total']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="conteoDependenciaCRP" class="conteo-dependencia" style="display:none;">
        <h3 style="margin-top:2em;">Registros por Dependencia (CRP)</h3>
        <div class="stats-summary">
            <?php foreach($conteoCRP as $dep): ?>
            <div class="stat-box">
                <h4><?php echo htmlspecialchars($dep['nombre_dependencia']); ?> (<?php echo htmlspecialchars($dep['codigo_dependencia']); ?>)</h4>
                <div class="number"><?php echo number_format($dep['total']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="conteoDependenciaOP" class="conteo-dependencia" style="display:none;">
        <h3 style="margin-top:2em;">Registros por Dependencia (OP)</h3>
        <div class="stats-summary">
            <?php foreach($conteoOP as $dep): ?>
            <div class="stat-box">
                <h4><?php echo htmlspecialchars($dep['nombre_dependencia']); ?> (<?php echo htmlspecialchars($dep['codigo_dependencia']); ?>)</h4>
                <div class="number"><?php echo number_format($dep['total']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
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
        <?php foreach(['CDP', 'RP', 'OP'] as $tipo): // Cambiado CRP por RP aquí 
            $ultimaActualizacion = array_filter($actualizaciones, function($a) use ($tipo) {
                // Si es RP en la vista, buscar CRP en los datos
                $searchTipo = ($tipo === 'RP') ? 'CRP' : $tipo;
                return $a['tipo_tabla'] === $searchTipo;
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
    <div class="updates-table-wrapper">
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
                    <td><?php echo htmlspecialchars($actualizacion['tipo_tabla'] === 'CRP' ? 'RP' : $actualizacion['tipo_tabla']); ?></td>
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
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                label: 'RP', // Cambiado de CRP a RP
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

        // Forzar visibilidad si se carga directamente o el JS principal falla
        var parentDashboardView = window.parent && window.parent.document.getElementById('dashboardView');
        var localDashboardView = document.getElementById('dashboardView');
        if (localDashboardView) {
            localDashboardView.style.display = 'block';
        }

        // Selección de tarjetas de resumen
        const statBoxes = document.querySelectorAll('.stat-selectable');
        const conteos = {
            cdp: document.getElementById('conteoDependenciaCDP'),
            crp: document.getElementById('conteoDependenciaCRP'),
            op: document.getElementById('conteoDependenciaOP')
        };

        statBoxes.forEach(box => {
            box.addEventListener('click', function() {
                // Quitar clase activa de todas
                statBoxes.forEach(b => b.classList.remove('active'));
                // Activar la seleccionada
                this.classList.add('active');
                // Mostrar solo el conteo correspondiente
                Object.keys(conteos).forEach(tipo => {
                    conteos[tipo].style.display = (tipo === this.dataset.tipo) ? 'block' : 'none';
                });
            });
        });
    });
</script>
<style>
/* ...existing code... */
.stat-selectable {
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.stat-selectable.active {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #2563eb33;
    background: #f0f6ff;
}
</style>