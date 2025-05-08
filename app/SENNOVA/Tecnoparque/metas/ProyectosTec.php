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

function crearTortaTec(lineaInicial = null) {
    const card = document.createElement('div');
    card.className = 'torta-card';

    // Select con TODAS las opciones, pero deshabilita las ya usadas en otras tortas
    const select = document.createElement('select');
    opciones.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.label;
        select.appendChild(option);
    });
    select.value = lineaInicial;  // Establecer el valor inicial
    select.onchange = function() {
        const index = tortas.findIndex(t => t.select === this);
        if (index !== -1) {
            tortas[index].linea = this.value;
            renderTortas(); // Re-renderizar todas las tortas
        } else {
             console.error("No se encontró la torta para actualizar"); // Para Debug
        }
    };
    card.appendChild(select);

    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-torta';
    removeBtn.innerHTML = '&times;';
    removeBtn.onclick = function() {
        const index = tortas.findIndex(t => t.select === select);
        if (index !== -1) {
            charts[index].destroy();
            charts.splice(index, 1);
            tortas.splice(index, 1);
            tortasContainer.removeChild(card);
            updateAddTortaButtonState(); // Actualizar el estado del botón
        } else {
            console.error("No se encontró la torta para eliminar");  // Para Debug
        }
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

    const nuevaTorta = {
        linea: lineaInicial || opciones[0].value,
        select: select, // Guardar la referencia al select
        canvas: canvas,
        title: title,
        infoDiv: infoDiv,
        card: card
    };
    tortas.push(nuevaTorta);
    renderTortas();
    return nuevaTorta; // Devuelve la nueva torta creada
}

function renderTortas() {
    tortas.forEach((torta, index) => {
        const linea = torta.linea;
        const canvas = torta.canvas;
        const titleElement = torta.title;
        const infoDivElement = torta.infoDiv;

        console.log('Renderizando torta:', index, 'con línea:', linea); // NUEVO
        const nombre = opciones.find(o => o.value === linea)?.label || '';
        titleElement.innerHTML = `<strong>${nombre}</strong>`;

        const dataObj = proyectos.find(p => String(p.nombre_linea) === String(linea));
        const terminadosVal = dataObj ? Number(dataObj.terminados) : 0;
        const enProcesoVal = dataObj ? Number(dataObj.en_proceso) : 0;
        const total = terminadosVal + enProcesoVal;
        const dataPie = total > 0 ? [terminadosVal, enProcesoVal] : [0, 0];

        infoDivElement.innerHTML = [
            `<span style="color:#2563eb;font-weight:bold;">Terminados: ${terminadosVal}</span>`,
            `<span style="color:#fde047;font-weight:bold;">En Proceso: ${enProcesoVal}</span>`
        ].join(' <span style="color:#bbb;">-</span> ');

        if (charts[index]) {
            charts[index].destroy();
        }
        const ctx = canvas.getContext('2d');
        console.log('Contexto del canvas:', ctx); // NUEVO
        if (!ctx) {
            console.error('No se pudo obtener el contexto del canvas'); //NUEVO
            return;
        }
        const newChart = new Chart(ctx, {
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
        });
        charts[index] = newChart;
    });
    updateAddTortaButtonState();
}

function updateAddTortaButtonState() {
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
    const nuevaTorta = crearTortaTec();
    if (!nuevaTorta) {
        updateAddTortaButtonState();
    }
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
