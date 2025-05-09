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
                tortaCard.appendChild(tortaInfo);

                container.appendChild(tortaCard);

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
    <div class="stats-card flex flex-wrap gap-6 mb-6">
        <div class="stat-item">
            <div class="stat-value text-green-700"><?php echo $resumen['total_terminados']; ?></div>
            <div class="stat-label">Terminados</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-yellow-700"><?php echo $resumen['total_en_proceso']; ?></div>
            <div class="stat-label">En Proceso</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-blue-700"><?php echo $meta_total; ?></div>
            <div class="stat-label">Meta Proyectos</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-green-700"><?php echo $porcentaje_terminados; ?>%</div>
            <div class="stat-label">Porcentaje Terminado</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-yellow-700"><?php echo $porcentaje_esperado; ?>%</div>
            <div class="stat-label">Porcentaje Esperado</div>
        </div>
    </div>

    <div class="chart-wrapper mb-6">
        <h2 class="text-xl font-semibold mb-4">Proyectos de Extensionismo Tecnológico</h2>
        <canvas id="graficaProyectosExt" width="400" height="200"></canvas>
    </div>

    <div class="chart-wrapper mb-6">
        <h2 class="text-xl font-semibold mb-4">Estado de Proyectos por Línea</h2>
        <div id="tortasExt" class="tortas-container">
            </div>
    </div>

    <div class="tabla-card">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Tabla de Proyectos de Extensionismo</h2>
        </div>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Línea Estratégica</th>
                    <th>Nombre del Proyecto</th>
                    <th>Terminados</th>
                    <th>En Proceso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyectos as $proyecto) : ?>
                    <tr>
                        <td><?php echo $proyecto['nombre_linea']; ?></td>
                        <td><?php echo $proyecto['nombre_proyecto']; ?></td>
                        <td><?php echo $proyecto['terminados']; ?></td>
                        <td><?php echo $proyecto['en_proceso']; ?></td>
                        <td><?php echo $proyecto['estado']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td><?php echo $resumen['total_terminados']; ?></td>
                    <td><?php echo $resumen['total_en_proceso']; ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {


        const proyectos = <?php echo json_encode($metas->obtenerProyectosTecPorTipo('Extensionismo')); ?>;
        generarGraficaBarras('graficaProyectosExt', proyectos, 'Proyectos de Extensionismo');
        generarGraficaTorta('tortasExt', proyectos);



    });
</script>
