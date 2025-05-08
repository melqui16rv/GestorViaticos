<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();
$proyectos = $metas->obtenerProyectosTecPorTipo('Tecnológico');
$resumen = $metas->obtenerSumaProyectosTecTerminadosPorTipo('Tecnológico');

// Calcular el total esperado (terminados + en proceso)
$total_esperado = 0;
foreach ($proyectos as $p) {
    $total_esperado += (int)$p['terminados'] + (int)$p['en_proceso'];
}
$porcentaje_esperado = min(100, round(($total_esperado / 100) * 100, 1));
?>
<head>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/metas.css">
</head>
<h1 class="titulo" id="titulo1">Meta: 100 Proyectos Tecnológicos Terminados</h1>
<div class="dashboard-container" id="dashboardContent">
    <div class="stats-card flex flex-wrap gap-6 mb-6">
        <div class="stat-item">
            <div class="stat-value text-green-700"><?php echo $resumen['total_terminados']; ?></div>
            <div class="stat-label">Terminados</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-green-700"><?php echo $resumen['avance_porcentaje']; ?>%</div>
            <div class="stat-label">Terminados (%)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-yellow-500"><?php echo $porcentaje_esperado; ?>%</div>
            <div class="stat-label">Proyección (%)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-blue-500"><?php echo $total_esperado; ?></div>
            <div class="stat-label">Proyección (Terminados + En Proceso)</div>
        </div>
    </div>
    <div class="grafica-table-wrapper mb-8">
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
    <div class="chart-wrapper mb-8">
        <canvas id="graficaProyectosTec"></canvas>
    </div>
    <h3 class="tortas-title">Detalle por Línea (Torta)</h3>
    <div class="tortas-container" id="tortasTec"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
const proyectos = <?php echo json_encode($proyectos); ?>;
const labels = proyectos.map(p => p.nombre_linea);
const terminados = proyectos.map(p => Number(p.terminados));
const enProceso = proyectos.map(p => Number(p.en_proceso));

// Paleta de colores suaves y agradables
const verdeSuave = 'rgba(34,197,94,0.75)';      // verde pastel
const verdeBorde = 'rgba(34,197,94,1)';
const amarilloSuave = 'rgba(253,224,71,0.65)';  // amarillo pastel
const amarilloBorde = 'rgba(253,224,71,1)';
const azulSuave = 'rgba(59,130,246,0.60)';      // azul pastel
const azulBorde = 'rgba(59,130,246,1)';

// Gráfica de barras por línea (terminados y en proceso)
const ctx = document.getElementById('graficaProyectosTec').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Terminados',
                data: terminados,
                backgroundColor: verdeSuave,
                borderColor: verdeBorde,
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.7,
                categoryPercentage: 0.6
            },
            {
                label: 'En Proceso',
                data: enProceso,
                backgroundColor: amarilloSuave,
                borderColor: amarilloBorde,
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.7,
                categoryPercentage: 0.6
            },
            {
                label: 'Proyección',
                data: proyectos.map(p => Number(p.terminados) + Number(p.en_proceso)),
                backgroundColor: azulSuave,
                borderColor: azulBorde,
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.7,
                categoryPercentage: 0.6
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.06)'
                },
                ticks: {
                    color: '#64748b',
                    font: { size: 14 }
                }
            },
            x: {
                grid: {
                    color: 'rgba(0,0,0,0.04)'
                },
                ticks: {
                    color: '#64748b',
                    font: { size: 14 }
                }
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#334155',
                    font: { size: 15, weight: 'bold' }
                }
            }
        }
    }
});

// Tortas dinámicas por línea
const tortasContainer = document.getElementById('tortasTec');
let charts = [];

function crearTortaTec(linea) {
    const card = document.createElement('div');
    card.className = 'torta-card';

    const title = document.createElement('div');
    title.className = 'torta-title';
    card.appendChild(title);

    const canvas = document.createElement('canvas');
    card.appendChild(canvas);

    const infoDiv = document.createElement('div');
    infoDiv.className = 'torta-info';
    card.appendChild(infoDiv);

    tortasContainer.appendChild(card);

    return {
        linea: linea,
        canvas: canvas,
        title: title,
        infoDiv: infoDiv,
        card: card
    };
}

function renderTortas() {
    tortasContainer.innerHTML = '';
    charts.forEach(chart => chart.destroy());
    charts = [];

    proyectos.forEach((proyecto, index) => {
        const torta = crearTortaTec(proyecto.nombre_linea);
        const linea = torta.linea;
        const canvas = torta.canvas;
        const titleElement = torta.title;
        const infoDivElement = torta.infoDiv;
        const card = torta.card;

        const nombre = proyecto.nombre_linea;
        titleElement.innerHTML = `<strong>${nombre}</strong>`;

        const terminadosVal = Number(proyecto.terminados);
        const enProcesoVal = Number(proyecto.en_proceso);
        const total = terminadosVal + enProcesoVal;
        const dataPie = total > 0 ? [terminadosVal, enProcesoVal] : [0, 0];

        infoDivElement.innerHTML = [
            `<span class="terminados-label">Terminados: ${terminadosVal}</span>`,
            `<span class="en-proceso-label">En Proceso: ${enProcesoVal}</span>`
        ].join(' <span class="separator"> - </span> ');

        const ctx = canvas.getContext('2d');
        const newChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Terminados', 'En Proceso'],
                datasets: [{
                    data: dataPie,
                    backgroundColor: [verdeSuave, amarilloSuave],
                    borderColor: [verdeBorde, amarilloBorde],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        textStrokeColor: '#334155',
                        textStrokeWidth: 1.2,
                        formatter: function(value, context) {
                            const sum = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            if (sum === 0) return '';
                            return ((value / sum) * 100).toFixed(1) + '%';
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(51,65,85,0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#64748b',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = sum > 0 ? ((value / sum) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percent}%)`;
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
        charts.push(newChart);
    });
}

// Inicializar las tortas
renderTortas();
</script>
<style>
#graficaProyectosTec {
    width: 100% !important;
    height: auto !important;
    max-height: 350px !important;
    display: block;
    margin: 0 auto;
}
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
</style>
