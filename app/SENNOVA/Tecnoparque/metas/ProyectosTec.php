<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();
$proyectos = $metas->obtenerProyectosTecPorTipo('Tecnológico');
$resumen = $metas->obtenerSumaProyectosTecTerminadosPorTipo('Tecnológico');
?>
<h1 class="titulo" id="titulo1">Meta: 100 Proyectos Tecnológicos Terminados</h1>
<div class="dashboard-container" id="dashboardContent">
    <div class="stats-card flex flex-wrap gap-6 mb-6">
        <div>
            <div class="text-2xl font-bold text-blue-700"><?php echo $resumen['total_terminados']; ?></div>
            <div class="text-gray-600">Terminados</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-green-700"><?php echo $resumen['avance_porcentaje']; ?>%</div>
            <div class="text-gray-600">Avance Meta (100%)</div>
        </div>
    </div>
    <div class="grafica-table-wrapper mb-8">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th>Línea</th>
                    <th>Terminados</th>
                    <th>En Proceso</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['nombre_linea']); ?></td>
                    <td><?php echo (int)$p['terminados']; ?></td>
                    <td><?php echo (int)$p['en_proceso']; ?></td>
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
const tortasContainer = document.getElementById('tortasTec');
let tortas = [];
let charts = [];
const opciones = proyectos.map((p) => ({
    value: String(p.nombre_linea),
    label: p.nombre_linea
}));
const addTortaBtn = document.getElementById('addTortaTec');

function getLineasUsadas() {
    return tortas.map(t => t.linea);
}

function crearTortaTec(lineaInicial = null) {
    // Buscar la primera línea disponible si no se pasa una
    let linea = lineaInicial;
    if (!linea) {
        const usadas = getLineasUsadas();
        const libre = opciones.find(opt => !usadas.includes(opt.value));
        linea = libre ? libre.value : opciones[0].value;
    }

    const card = document.createElement('div');
    card.className = 'torta-card';

    // Select con TODAS las opciones, pero deshabilita las ya usadas en otras tortas
    const select = document.createElement('select');
    opciones.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.label;
        // Deshabilita si ya está usada en otra torta
        if (getLineasUsadas().includes(opt.value) && opt.value !== linea) {
            option.disabled = true;
        }
        select.appendChild(option);
    });
    select.value = linea;
    select.onchange = function() {
        tortas[Array.from(tortasContainer.children).indexOf(card)].linea = this.value;
        renderTortas();
    };
    card.appendChild(select);

    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-torta';
    removeBtn.innerHTML = '&times;';
    removeBtn.onclick = function() {
        const idx = Array.from(tortasContainer.children).indexOf(card);
        if (charts[idx]) charts[idx].destroy();
        charts.splice(idx, 1);
        tortas.splice(idx, 1);
        tortasContainer.removeChild(card);
        renderTortas();
    };
    card.appendChild(removeBtn);

    const title = document.createElement('div');
    title.className = 'torta-title';
    card.appendChild(title);

    const canvas = document.createElement('canvas');
    card.appendChild(canvas);

    const infoDiv = document.createElement('div');
    infoDiv.className = 'torta-info';
    card.appendChild(infoDiv);

    tortasContainer.appendChild(card);

    tortas.push({
        linea: linea,
        select: select,
        canvas: canvas,
        title: title,
        infoDiv: infoDiv,
        card: card
    });
    renderTortas();
}

function renderTortas() {
    tortas.forEach((torta, index) => {
        // Actualiza opciones del select
        Array.from(torta.select.options).forEach(option => {
            option.disabled = getLineasUsadas().includes(option.value) && option.value !== torta.linea;
        });

        // Actualiza título y datos
        const nombre = opciones.find(o => o.value === torta.linea)?.label || '';
        torta.title.innerHTML = `<strong>${nombre}</strong>`;

        const dataObj = proyectos.find(p => String(p.nombre_linea) === String(torta.linea));
        const terminadosVal = dataObj ? Number(dataObj.terminados) : 0;
        const enProcesoVal = dataObj ? Number(dataObj.en_proceso) : 0;
        const total = terminadosVal + enProcesoVal;
        const dataPie = total > 0 ? [terminadosVal, enProcesoVal] : [0, 0];

        torta.infoDiv.innerHTML = [
            `<span style="color:#2563eb;font-weight:bold;">Terminados: ${terminadosVal}</span>`,
            `<span style="color:#fde047;font-weight:bold;">En Proceso: ${enProcesoVal}</span>`
        ].join(' <span style="color:#bbb;">-</span> ');

        // Limpia el canvas anterior y crea uno nuevo
        if (charts[index]) charts[index].destroy();
        const oldCanvas = torta.canvas;
        const newCanvas = document.createElement('canvas');
        oldCanvas.parentNode.replaceChild(newCanvas, oldCanvas);
        torta.canvas = newCanvas;

        const ctx = newCanvas.getContext('2d');
        charts[index] = new Chart(ctx, {
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
                                const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = sum > 0 ? ((value / sum) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percent}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    });
    addTortaBtn.disabled = tortas.length >= opciones.length;
}

// Inicializar con dos tortas diferentes si hay suficientes líneas
if (opciones.length > 0) {
    crearTortaTec(opciones[0].value);
    if(opciones.length > 1) {
       crearTortaTec(opciones[1].value);
    }
}

addTortaBtn.onclick = function() {
    crearTortaTec();
};
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
