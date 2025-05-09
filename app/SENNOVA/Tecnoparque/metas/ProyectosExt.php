<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metasExt.php';

$metas = new metas_tecnoparqueExt();
$proyectos = $metas->obtenerProyectosTecPorTipo('Extensionismo');
$resumen = $metas->obtenerSumaProyectosTecPorTipo('Extensionismo');

$meta_total = 5;

$total_esperado = 0;
foreach ($proyectos as $p) {
    $total_esperado += (int)$p['terminados'] + (int)$p['en_proceso'];
}
$porcentaje_terminados = min(100, round(($resumen['total_terminados'] / $meta_total) * 100, 1));
$porcentaje_esperado = min(100, round(($total_esperado / $meta_total) * 100, 1));
?>
<head>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/metas.css">
<style>
.torta-card {
    background: #fff;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: inline-block;
    margin-right: 1rem;
    min-width: 220px;
    vertical-align: top;
    position: relative;
}
.torta-card .torta-title{
    min-height: 1.5em;
    font-size: 1.1em;
    font-weight: bold;
    margin-bottom: 0.5em;
    text-align: center;
}
.torta-card .torta-info{
    font-size: 1em;
    margin-top: 0.5em;
}
/* Botón de actualizar tabla */
.actualizar-tabla-link {
    text-decoration: none;
}
.actualizar-tabla-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(90deg, #34d399 0%, #60a5fa 100%);
    color: #fff;
    font-weight: 600;
    font-size: 1.08rem;
    padding: 0.65rem 1.4rem;
    border: none;
    border-radius: 0.7rem;
    box-shadow: 0 2px 8px rgba(52,211,153,0.08), 0 1.5px 6px rgba(96,165,250,0.08);
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
    outline: none;
}
.actualizar-tabla-btn:hover, .actualizar-tabla-btn:focus {
    background: linear-gradient(90deg, #60a5fa 0%, #34d399 100%);
    box-shadow: 0 4px 16px rgba(52,211,153,0.13), 0 3px 12px rgba(96,165,250,0.13);
    transform: translateY(-2px) scale(1.03);
}
.icon-refresh {
    width: 1.3em;
    height: 1.3em;
    stroke-width: 2.2;
}
/* Tarjeta blanca para tabla y botón */
.tabla-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.07);
    padding: 2rem 1.5rem 1.5rem 1.5rem;
    margin-bottom: 2rem;
}
</style>
<!-- Mueve los scripts de Chart.js y ChartDataLabels al <head> para asegurar su carga antes de usarlos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>
<div class="dashboard-container" id="dashboardContent">
    <div class="stats-card flex flex-wrap gap-6 mb-6">
        <!-- Indicadores de metas -->
        <div class="stat-item">
            <div class="stat-value text-green-700"><?php echo $resumen['total_terminados']; ?></div>
            <div class="stat-label">Terminados</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-green-700"><?php echo $porcentaje_terminados; ?>%</div>
            <div class="stat-label">Terminados (%)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-blue-500"><?php echo $total_esperado; ?></div>
            <div class="stat-label">Proyección</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-blue-500"><?php echo $porcentaje_esperado; ?>%</div>
            <div class="stat-label">Proyección (%)</div>
        </div>
    </div>

    <!-- Tarjeta para botón y tabla -->
    <div class="tabla-card mb-8">
        <div class="flex justify-end mb-4">
            <a href="<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/control/actualizarMetaProExt.php" id="actualizarTablaBtn" class="actualizar-tabla-link">
                <button type="button" class="actualizar-tabla-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.582 9A7.003 7.003 0 0112 5c3.866 0 7 3.134 7 7 0 1.657-.573 3.182-1.535 4.382M18.418 15A7.003 7.003 0 0112 19c-3.866 0-7-3.134-7-7 0-1.657.573-3.182 1.535-4.382"/>
                    </svg>
                    Actualizar tabla
                </button>
            </a>
        </div>
        <div class="grafica-table-wrapper">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Línea</th>
                        <th>Terminados</th>
                        <th>En Proceso</th>
                        <th>Proyección</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_terminados = 0;
                    $total_en_proceso = 0;
                    $total_proyeccion = 0;
                    foreach ($proyectos as $p):
                        $proy = (int)$p['terminados'] + (int)$p['en_proceso'];
                        $total_terminados += (int)$p['terminados'];
                        $total_en_proceso += (int)$p['en_proceso'];
                        $total_proyeccion += $proy;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nombre_linea']); ?></td>
                        <td class="td-terminados"><?php echo (int)$p['terminados']; ?></td>
                        <td class="td-enproceso"><?php echo (int)$p['en_proceso']; ?></td>
                        <td class="td-proyeccion">
                            <?php echo $proy; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td class="td-terminados"><?php echo $total_terminados; ?></td>
                        <td class="td-enproceso"><?php echo $total_en_proceso; ?></td>
                        <td class="td-proyeccion"><?php echo $total_proyeccion; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="chart-wrapper mb-8">
        <canvas id="graficaProyectosTec"></canvas>
    </div>
    <h3 class="tortas-title">Detalle por Línea (Torta)</h3>
    <div class="tortas-container" id="tortasTec"></div>
</div>
<script>
// Paleta de colores suaves y agradables (variables únicas para Extensionismo)
const verdeSuaveExt = 'rgba(34,197,94,0.75)';
const verdeBordeExt = 'rgba(34,197,94,1)';
const amarilloSuaveExt = 'rgba(253,224,71,0.65)';
const amarilloBordeExt = 'rgba(253,224,71,1)';

// Usar los valores correctos del resumen
const terminadosExt = <?php echo (int)$resumen['total_terminados']; ?>;
const enProcesoExt = <?php echo (int)$resumen['total_en_proceso']; ?>;
const metaExt = <?php echo (int)$meta_total; ?>;

// Espera a que el DOM esté listo antes de crear los gráficos
document.addEventListener('DOMContentLoaded', function() {
    // Gráfica de barra horizontal de avance sobre la meta
    const canvasBarra = document.getElementById('graficaProyectosTec');
    if (canvasBarra) {
        const ctxExt = canvasBarra.getContext('2d');
        new Chart(ctxExt, {
            type: 'bar',
            data: {
                labels: ['Meta Extensionismo'], // Cambia el nombre de la variable si tienes otra llamada 'labels'
                datasets: [
                    {
                        label: 'Terminados',
                        data: [terminadosExt],
                        backgroundColor: verdeSuaveExt,
                        borderColor: verdeBordeExt,
                        borderWidth: 2,
                        borderRadius: 8,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'En Proceso',
                        data: [enProcesoExt],
                        backgroundColor: amarilloSuaveExt,
                        borderColor: amarilloBordeExt,
                        borderWidth: 2,
                        borderRadius: 8,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: metaExt,
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: { color: '#64748b', font: { size: 14 } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { color: '#64748b', font: { size: 14 } }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#334155', font: { size: 15, weight: 'bold' } }
                    }
                }
            }
        });
    }

    // Gráfica de torta: terminados vs en proceso
    const tortasContainerExt = document.getElementById('tortasTec');
    if (tortasContainerExt) {
        tortasContainerExt.innerHTML = '<canvas id="tortaExtensionismo"></canvas>';
        const canvasTorta = document.getElementById('tortaExtensionismo');
        if (canvasTorta) {
            const ctxTortaExt = canvasTorta.getContext('2d');
            new Chart(ctxTortaExt, {
                type: 'pie',
                data: {
                    labels: ['Terminados', 'En Proceso'], // Cambia el nombre de la variable si tienes otra llamada 'labels'
                    datasets: [{
                        data: [terminadosExt, enProcesoExt],
                        backgroundColor: [verdeSuaveExt, amarilloSuaveExt],
                        borderColor: [verdeBordeExt, amarilloBordeExt],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#334155', font: { size: 15, weight: 'bold' } }
                        },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 14 },
                            textStrokeColor: '#334155',
                            textStrokeWidth: 1.2,
                            formatter: function(value, context) {
                                const sum = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                if (sum === 0) return '';
                                return ((value / sum) * 100).toFixed(1) + '%';
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    }
});
</script>
