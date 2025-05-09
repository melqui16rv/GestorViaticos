<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metasExt.php';

$metas = new metas_tecnoparqueExt();
$proyectos = $metas->obtenerProyectosTecPorTipo('Extensionismo');
// Cambia aquí para obtener ambos valores:
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        // Función para generar la gráfica de barras
        function generarGraficaBarras(canvasId, proyectos, titulo) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            const labels = proyectos.map(p => p.nombre_linea);
            const terminados = proyectos.map(p => Number(p.terminados));
            const enProceso = proyectos.map(p => Number(p.en_proceso));
            const proyeccion = proyectos.map(p => Number(p.terminados) + Number(p.en_proceso)); // Nueva columna

            const verdeSuave = 'rgba(34,197,94,0.75)';
            const verdeBorde = 'rgba(34,197,94,1)';
            const amarilloSuave = 'rgba(253,224,71,0.65)';
            const amarilloBorde = 'rgba(253,224,71,1)';
            const azulSuave = 'rgba(59,130,246,0.60)';
            const azulBorde = 'rgba(59,130,246,1)';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Terminados',
                        data: terminados,
                        backgroundColor: verdeSuave,
                        borderColor: verdeBorde,
                        borderWidth: 1
                    }, {
                        label: 'En Proceso',
                        data: enProceso,
                        backgroundColor: amarilloSuave,
                        borderColor: amarilloBorde,
                        borderWidth: 1
                    }, {
                        label: 'Proyección', // Nueva columna
                        data: proyeccion,
                        backgroundColor: azulSuave,
                        borderColor: azulBorde,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: titulo,
                            font: {
                                size: 16
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            font: {
                                size: 12
                            },
                            color: '#222',
                            formatter: (value) => {
                                return value > 0 ? value : '';
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function generarGraficaTorta(containerId, proyectos, titulo) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            proyectos.forEach(proyecto => {
                const tortaCard = document.createElement('div');
                tortaCard.className = 'torta-card';

                const canvas = document.createElement('canvas');
                const canvasId = `torta-${proyecto.id_linea}`;
                canvas.id = canvasId;
                tortaCard.appendChild(canvas);

                const tortaTitle = document.createElement('div');
                tortaTitle.className = 'torta-title';
                tortaTitle.textContent = proyecto.nombre_linea;
                tortaCard.appendChild(tortaTitle);

                const totalLinea = Number(proyecto.terminados) + Number(proyecto.en_proceso);
                const tortaInfo = document.createElement('div');
                tortaInfo.className = 'torta-info';
                tortaInfo.textContent = `Total: ${totalLinea}`;
                tortaCard.appendChild(tortaInfo); // Corregido: Agregar `tortaInfo` al `tortaCard`

                container.appendChild(tortaCard); // Corregido: Agregar `tortaCard` al `container`

                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Terminados', 'En Proceso'],
                        datasets: [{
                            label: 'Proyectos',
                            data: [Number(proyecto.terminados), Number(proyecto.en_proceso)],
                            backgroundColor: [
                                'rgba(34,197,94,0.85)',
                                'rgba(253,224,71,0.75)',
                            ],
                            borderColor: [
                                'rgba(34,197,94,1)',
                                'rgba(253,224,71,1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: false,
                                text: `Gráfica de Torta para ${proyecto.nombre_linea}`,
                                font: {
                                    size: 14
                                }
                            },
                            legend: {
                                position: 'top'
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b);
                                    const percentage = value / total;
                                    return percentage > 0.1 ? `${(percentage * 100).toFixed(1)}%` : '';
                                },
                                color: '#111',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                });
            });
        }
    </script>
</head>
<div class="dashboard-container" id="dashboardContent">
    <div class="stats-card">
        <div class="flex flex-wrap gap-6 mb-6">
            <div class="stat-item">
                <div class="stat-value text-green-700"><?php echo $resumen['total_terminados']; ?></div>
                <div class="stat-label">Terminados</div>
            </div>
            <div class="stat-item">
                <div class="stat-value text-green-700"><?php echo $porcentaje_terminados; ?>%</div>
                <div class="stat-label">Porcentaje Terminado</div>
            </div>
            <div class="stat-item">
                <div class="stat-value text-yellow-700"><?php echo $resumen['total_en_proceso']; ?></div>
                <div class="stat-label">En Proceso</div>
            </div>
            <div class="stat-item">
                <div class="stat-value text-yellow-700"><?php echo $porcentaje_esperado; ?>%</div>
                <div class="stat-label">Porcentaje Esperado</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-blue-700"><?php echo $meta_total; ?></div>
            <div class="stat-label">Meta Proyectos</div>
        </div>
    </div>
    <div class="tabla-card">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Tabla de Proyectos de Extensionismo</h2>
        </div>
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
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Línea Estratégica</th>
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
                    <td class="td-proyeccion"><?php echo $proy; ?></td>
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
    <h2 class="text-xl font-semibold mb-4">Proyectos de Extensionismo Tecnológico</h2>
    <div class="chart-wrapper mb-6">
        <canvas id="graficaProyectosExt" width="400" height="200"></canvas>
    </div>
    <h2 class="text-xl font-semibold mb-4">Estado de Proyectos Detallado</h2>
    <div id="tortasExt" class="tortas-container"></div>


</div>
<script>
    $(document).ready(function() {
        const proyectos = <?php echo json_encode($metas->obtenerProyectosTecPorTipo('Extensionismo')); ?>;
        generarGraficaBarras('graficaProyectosExt', proyectos, 'Proyectos de Extensionismo');
        generarGraficaTorta('tortasExt', proyectos);
    });
</script>
