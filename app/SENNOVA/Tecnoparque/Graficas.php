<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/sennova/tecnoparque/graficas.php';

$miGraficas = new graficas_tecnoparque();
$datosCDP = $miGraficas->obtenerGraficaCDP();
$datosCRP = $miGraficas->obtenerGraficaCRP();
$datosOP = $miGraficas->obtenerGraficaOP();
?>

<div class="container-graficas">
    <h2>Consumo por Dependencia (CDP)</h2>
    <div class="grafica-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Dependencia</th>
                    <th>Valor Actual</th>
                    <th>Saldo por Comprometer</th>
                    <th>Valor Comprometido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosCDP as $fila): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['codigo_dependencia']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre_dependencia']); ?></td>
                    <td><?php echo number_format($fila['valor_actual'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($fila['saldo_por_comprometer'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($fila['valor_consumido'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="chart-wrapper">
        <canvas id="graficaCDP"></canvas>
    </div>

    <!-- Tortas dinámicas para CDP -->
    <h3>Detalle por Dependencia (CDP)</h3>
    <button class="add-torta-btn" id="addTortaCDP">Agregar gráfica de torta</button>
    <div class="tortas-container" id="tortasCDP"></div>

    <!-- Segunda gráfica: CRP -->
    <h2>Utilización por Dependencia (CRP)</h2>
    <div class="grafica-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Dependencia</th>
                    <th>Valor Actual</th>
                    <th>Saldo por Utilizar</th>
                    <th>Saldo Utilizado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosCRP as $fila): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['codigo_dependencia']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre_dependencia']); ?></td>
                    <td><?php echo number_format($fila['valor_actual'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($fila['saldo_por_utilizar'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($fila['saldo_utilizado'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="chart-wrapper">
        <canvas id="graficaCRP"></canvas>
    </div>

    <!-- Tortas dinámicas para CRP -->
    <h3>Detalle por Dependencia (CRP)</h3>
    <button class="add-torta-btn" id="addTortaCRP">Agregar gráfica de torta</button>
    <div class="tortas-container" id="tortasCRP"></div>

    <!-- Tercera gráfica: OP -->
    <h2>Pagos por Dependencia (OP)</h2>
    <div class="grafica-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Dependencia</th>
                    <th>Total CRP</th>
                    <th>Total Pagado (OP)</th>
                    <th>Valor Restante</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosOP as $fila): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['codigo_dependencia']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre_dependencia']); ?></td>
                    <td><?php echo number_format($fila['suma_crp'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($fila['suma_op'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($fila['valor_restante'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="chart-wrapper">
        <canvas id="graficaOP"></canvas>
    </div>

    <!-- Tortas dinámicas para OP -->
    <h3>Detalle por Dependencia (OP)</h3>
    <button class="add-torta-btn" id="addTortaOP">Agregar gráfica de torta</button>
    <div class="tortas-container" id="tortasOP"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    const datosCDP = <?php echo json_encode($datosCDP); ?>;
    const labels = datosCDP.map(d => d.nombre_dependencia + ' (' + d.codigo_dependencia + ')');
    const valorActual = datosCDP.map(d => d.valor_actual);
    const saldoPorComprometer = datosCDP.map(d => d.saldo_por_comprometer);
    const valorConsumido = datosCDP.map(d => d.valor_consumido);

    const ctx = document.getElementById('graficaCDP').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Valor Actual',
                    data: valorActual,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                },
                {
                    label: 'Saldo por Comprometer',
                    data: saldoPorComprometer,
                    backgroundColor: 'rgba(255, 206, 86, 0.5)'
                },
                {
                    label: 'Valor Comprometido',
                    data: valorConsumido,
                    backgroundColor: 'rgba(255, 114, 79, 0.56)'
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

    // Segunda gráfica: CRP
    const datosCRP = <?php echo json_encode($datosCRP); ?>;
    const labelsCRP = datosCRP.map(d => d.nombre_dependencia + ' (' + d.codigo_dependencia + ')');
    const valorActualCRP = datosCRP.map(d => d.valor_actual); // Debe ser 'valor_actual'
    const saldoPorUtilizar = datosCRP.map(d => d.saldo_por_utilizar); // Debe ser 'saldo_por_utilizar'
    const saldoUtilizado = datosCRP.map(d => d.saldo_utilizado); // Debe ser 'saldo_utilizado'

    const ctxCRP = document.getElementById('graficaCRP').getContext('2d');
    new Chart(ctxCRP, {
        type: 'bar',
        data: {
            labels: labelsCRP,
            datasets: [
                {
                    label: 'Valor Actual',
                    data: valorActualCRP,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                },
                {
                    label: 'Saldo por Utilizar',
                    data: saldoPorUtilizar,
                    backgroundColor: 'rgba(255, 206, 86, 0.5)'
                },
                {
                    label: 'Saldo Utilizado',
                    data: saldoUtilizado,
                    backgroundColor: 'rgba(255, 114, 79, 0.56)'
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

    // Tercera gráfica: OP
    const datosOP = <?php echo json_encode($datosOP); ?>;
    const labelsOP = datosOP.map(d => d.nombre_dependencia + ' (' + d.codigo_dependencia + ')');
    const sumaCRP = datosOP.map(d => d.suma_crp); // Debe ser 'suma_crp'
    const sumaOP = datosOP.map(d => d.suma_op); // Debe ser 'suma_op'
    const valorRestante = datosOP.map(d => d.valor_restante); // Debe ser 'valor_restante'

    const ctxOP = document.getElementById('graficaOP').getContext('2d');
    new Chart(ctxOP, {
        type: 'bar',
        data: {
            labels: labelsOP,
            datasets: [
                {
                    label: 'Total CRP',
                    data: sumaCRP,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                },
                {
                    label: 'Total Pagado (OP)',
                    data: sumaOP,
                    backgroundColor: 'rgba(255, 114, 79, 0.56)'
                },
                {
                    label: 'Valor Restante',
                    data: valorRestante,
                    backgroundColor: 'rgba(255, 206, 86, 0.5)'
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

    // Utilidad para tortas dinámicas
    function crearTorta(containerId, datos, labelsPie, camposPie, coloresPie, maxTortas = 2) {
        const container = document.getElementById(containerId);
        let tortas = [];
        let charts = [];

        // Opciones para el select
        const opciones = datos.map((d, i) => ({
            value: String(d.codigo_dependencia),
            label: d.nombre_dependencia + ' (' + d.codigo_dependencia + ')',
            index: i
        }));

        function render() {
            container.innerHTML = '';
            charts.forEach(c => c.destroy());
            charts = [];
            tortas.forEach((t, idx) => {
                // Card
                const card = document.createElement('div');
                card.className = 'torta-card';

                // Select
                const select = document.createElement('select');
                opciones.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.label;
                    select.appendChild(option);
                });
                select.value = t.codigo;
                select.onchange = function() {
                    tortas[idx].codigo = this.value;
                    render();
                };
                card.appendChild(select);

                // Remove button
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

                // Título
                const nombre = opciones.find(o => o.value === t.codigo)?.label || '';
                const title = document.createElement('div');
                title.innerHTML = `<strong>${nombre}</strong>`;
                card.appendChild(title);

                // Canvas
                const canvas = document.createElement('canvas');
                canvas.id = containerId + '_pie_' + idx;
                card.appendChild(canvas);

                // Porcentajes y valores
                const dataObj = datos.find(d => String(d.codigo_dependencia) === String(t.codigo));
                const dataPie = camposPie.map(c => dataObj ? dataObj[c] : 0);
                const total = dataPie.reduce((a, b) => a + b, 0);

                // Info de valores debajo de la torta (solo valor, color igual al label, con separador)
                const infoDiv = document.createElement('div');
                infoDiv.style.fontSize = '1em';
                infoDiv.style.marginTop = '8px';
                infoDiv.innerHTML = labelsPie.map((l, i) =>
                    `<span style="color:${coloresPie[i]};font-weight:bold;">$${dataPie[i].toLocaleString('es-CO', {minimumFractionDigits:2})}</span>`
                ).join(' <span style="color:#bbb;">-</span> ');
                card.appendChild(infoDiv);

                container.appendChild(card);

                // Chart.js con porcentaje en blanco, negrilla y sombra negra
                charts.push(new Chart(canvas.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: labelsPie,
                        datasets: [{
                            data: dataPie,
                            backgroundColor: coloresPie
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
                                        return `${label}: $${value.toLocaleString('es-CO', {minimumFractionDigits:2})} (${percent}%)`;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                }));
            });

            // Botón de agregar
            const addBtn = document.getElementById('addTorta' + containerId.replace('tortas', ''));
            addBtn.disabled = tortas.length >= opciones.length;
        }

        // Inicializar con dos tortas diferentes si hay suficientes dependencias
        tortas = opciones.slice(0, Math.min(maxTortas, opciones.length)).map(opt => ({ codigo: opt.value }));
        render();

        // Botón agregar
        document.getElementById('addTorta' + containerId.replace('tortas', '')).onclick = function() {
            // Buscar la primera dependencia no usada
            const usados = tortas.map(t => t.codigo);
            const siguiente = opciones.find(o => !usados.includes(o.value));
            if (siguiente) {
                tortas.push({ codigo: siguiente.value });
                render();
            }
        };
    }

    // CDP
    crearTorta(
        'tortasCDP',
        <?php echo json_encode($datosCDP); ?>,
        ['Saldo por Comprometer', 'Valor Comprometido'],
        ['saldo_por_comprometer', 'valor_consumido'],
        ['rgba(255, 185, 65, 0.88)', 'rgba(255, 99, 132, 0.7)']
    );
    // CRP
    crearTorta(
        'tortasCRP',
        <?php echo json_encode($datosCRP); ?>,
        ['Saldo por Utilizar', 'Saldo Utilizado'],
        ['saldo_por_utilizar', 'saldo_utilizado'],
        ['rgba(255, 185, 65, 0.88)', 'rgba(255, 99, 132, 0.7)']
    );
    // OP
    crearTorta(
        'tortasOP',
        <?php echo json_encode($datosOP); ?>,
        ['Total Pagado (OP)', 'Valor Restante'],
        ['suma_op', 'valor_restante'],
        ['rgba(255, 99, 132, 0.7)', 'rgba(255, 185, 65, 0.88)']
    );
</script>