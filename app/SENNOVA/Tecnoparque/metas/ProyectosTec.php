<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();
// Obtener solo proyectos de tipo 'Tecnológico'
$proyectos = $metas->obtenerProyectosTecPorTipo('Tecnológico');
$resumen = $metas->obtenerSumaProyectosTecPorTipo('Tecnológico');
?>
<h1 class="titulo" id="titulo1">100 Proyectos de Base Tecnológica</h1>
<div class="dashboard-container" id="dashboardContent">
    <div class="stats-card flex flex-wrap gap-6 mb-6">
        <div>
            <div class="text-2xl font-bold text-blue-700"><?php echo $resumen['total_terminados']; ?></div>
            <div class="text-gray-600">Terminados</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-yellow-600"><?php echo $resumen['total_en_proceso']; ?></div>
            <div class="text-gray-600">En Proceso</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-800"><?php echo $resumen['total']; ?></div>
            <div class="text-gray-600">Total Registrados</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-green-700"><?php echo round(($resumen['total'] / 100) * 100, 1); ?>%</div>
            <div class="text-gray-600">Avance Meta (100)</div>
        </div>
    </div>
    <div class="grafica-table-wrapper mb-8">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th>Línea</th>
                    <th>Terminados</th>
                    <th>En Proceso</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['nombre_linea']); ?></td>
                    <td><?php echo (int)$p['terminados']; ?></td>
                    <td><?php echo (int)$p['en_proceso']; ?></td>
                    <td><?php echo (int)$p['terminados'] + (int)$p['en_proceso']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="chart-wrapper mb-8">
        <canvas id="graficaProyectosTec"></canvas>
    </div>
    <h3>Detalle por Línea (Torta)</h3>
    <button class="add-torta-btn" id="addTortaTec">Agregar gráfica de torta</button>
    <div class="tortas-container" id="tortasTec"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
const proyectos = <?php echo json_encode($proyectos); ?>;
const labels = proyectos.map(p => p.nombre_linea);
const terminados = proyectos.map(p => Number(p.terminados));
const enProceso = proyectos.map(p => Number(p.en_proceso));

// Gráfica de barras por línea
const ctx = document.getElementById('graficaProyectosTec').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Terminados',
                data: terminados,
                backgroundColor: 'rgba(37, 99, 235, 0.7)'
            },
            {
                label: 'En Proceso',
                data: enProceso,
                backgroundColor: 'rgba(253, 224, 71, 0.7)'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Tortas dinámicas por línea
function crearTortaTec() {
    const container = document.getElementById('tortasTec');
    let tortas = [];
    let charts = [];
    const opciones = proyectos.map((p, i) => ({
        value: String(p.nombre_linea),
        label: p.nombre_linea,
        index: i
    }));

    function render() {
        container.innerHTML = '';
        charts.forEach(c => c.destroy());
        charts = [];
        tortas.forEach((t, idx) => {
            const card = document.createElement('div');
            card.className = 'torta-card';

            const select = document.createElement('select');
            opciones.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                select.appendChild(option);
            });
            select.value = t.linea;
            select.onchange = function() {
                tortas[idx].linea = this.value;
                render();
            };
            card.appendChild(select);

            if (tortas.length > 1) {
                const btn = document.createElement('button');
                btn.className = 'remove-torta';
                btn.innerHTML = '&times;';
                btn.onclick = function() {
                    tortas.splice(idx, 1);
                    render();
                };
                card.appendChild(btn);
            }

            const nombre = opciones.find(o => o.value === t.linea)?.label || '';
            const title = document.createElement('div');
            title.innerHTML = `<strong>${nombre}</strong>`;
            card.appendChild(title);

            const canvas = document.createElement('canvas');
            canvas.id = 'tortaTec_' + idx;
            card.appendChild(canvas);

            const dataObj = proyectos.find(p => String(p.nombre_linea) === String(t.linea));
            const dataPie = [dataObj ? Number(dataObj.terminados) : 0, dataObj ? Number(dataObj.en_proceso) : 0];
            const total = dataPie.reduce((a, b) => a + b, 0);

            const infoDiv = document.createElement('div');
            infoDiv.style.fontSize = '1em';
            infoDiv.style.marginTop = '8px';
            infoDiv.innerHTML = ['Terminados', 'En Proceso'].map((l, i) =>
                `<span style="color:${i === 0 ? '#2563eb' : '#fde047'};font-weight:bold;">${l}: ${dataPie[i]}</span>`
            ).join(' <span style="color:#bbb;">-</span> ');
            card.appendChild(infoDiv);

            container.appendChild(card);

            charts.push(new Chart(canvas.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Terminados', 'En Proceso'],
                    datasets: [{
                        data: dataPie,
                        backgroundColor: ['#2563eb', '#fde047']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 13 },
                            textStrokeColor: '#000',
                            textStrokeWidth: 1.4,
                            formatter: function(value, context) {
                                const sum = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                if (sum === 0) return '';
                                return ((value / sum) * 100).toFixed(1) + '%';
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            }));
        });

        document.getElementById('addTortaTec').disabled = tortas.length >= opciones.length;
    }

    // Inicializar con dos tortas diferentes si hay suficientes líneas
    tortas = opciones.slice(0, Math.min(2, opciones.length)).map(opt => ({ linea: opt.value }));
    render();

    document.getElementById('addTortaTec').onclick = function() {
        const usados = tortas.map(t => t.linea);
        const siguiente = opciones.find(o => !usados.includes(o.value));
        if (siguiente) {
            tortas.push({ linea: siguiente.value });
            render();
        }
    };
}
crearTortaTec();
</script>
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
.torta-card select {
    margin-bottom: 0.5rem;
}
.torta-card .remove-torta {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #eee;
    border: none;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 1.1em;
    cursor: pointer;
    color: #888;
}
</style>