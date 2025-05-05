<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/graficas.php';

$graficas = new graficas_general_sennova();

// Totales de viáticos solo para dependencias 62, 66, 69, 70
$totalesViaticos = $graficas->obtenerTotalesViaticosPorDependencias();
$totalesViaticosOP = $graficas->obtenerTotalesViaticosOPPorDependencias();

$valor_actual = $totalesViaticos['valor_actual'];
$saldo_por_comprometer = $totalesViaticos['saldo_por_comprometer'];
$consumo_cdp = $valor_actual - $saldo_por_comprometer;

$valor_op = $totalesViaticosOP['valor_op'];
$saldo_op = $consumo_cdp - $valor_op;

$porcentaje_consumido = $valor_actual > 0 ? ($consumo_cdp / $valor_actual) * 100 : 0;
$porcentaje_disponible = $valor_actual > 0 ? ($saldo_por_comprometer / $valor_actual) * 100 : 0;
$porcentaje_consumido_op = $consumo_cdp > 0 ? ($valor_op / $consumo_cdp) * 100 : 0;
$porcentaje_disponible_op = $consumo_cdp > 0 ? ($saldo_op / $consumo_cdp) * 100 : 0;
?>

<body>
    <!-- PRIMERA SECCIÓN: Presupuesto basado en CDP -->
    <div class="contenedorPresupuestoTotal">
        <div class="graficaContenedor">
            <canvas id="viaticosCDPChart"></canvas>
        </div>
        <div class="resultados-container">
            <div class="resultado-item valor-total">
                <div class="resultado-titulo">Valor Total Viáticos (CDP)</div>
                <div class="resultado-valor">
                    $<?php echo number_format($valor_actual, 2, ',', '.'); ?>
                    <span class="resultado-porcentaje">100%</span>
                </div>
            </div>
            <div class="resultado-item saldo-disponible">
                <div class="resultado-titulo">Saldo Disponible Viáticos (CDP)</div>
                <div class="resultado-valor">
                    $<?php echo number_format($saldo_por_comprometer, 2, ',', '.'); ?>
                    <span class="resultado-porcentaje"><?php echo number_format($porcentaje_disponible, 2, ',', '.'); ?>%</span>
                </div>
            </div>
            <div class="resultado-item consumo-cdp">
                <div class="resultado-titulo">Consumo CDP Viáticos</div>
                <div class="resultado-valor">
                    $<?php echo number_format($consumo_cdp, 2, ',', '.'); ?>
                    <span class="resultado-porcentaje"><?php echo number_format($porcentaje_consumido, 2, ',', '.'); ?>%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SEGUNDA SECCIÓN: Ejecución del presupuesto (OP) sobre lo comprometido en CDP -->
    <div class="contenedorPresupuestoTotal">
        <div class="graficaContenedor">
            <canvas id="viaticosOPChart"></canvas>
        </div>
        <div class="resultados-container">
            <div class="resultado-item valor-total">
                <div class="resultado-titulo">Valor Total Comprometido (CDP)</div>
                <div class="resultado-valor">
                    $<?php echo number_format($consumo_cdp, 2, ',', '.'); ?>
                    <span class="resultado-porcentaje">100%</span>
                </div>
            </div>
            <div class="resultado-item saldo-disponible">
                <div class="resultado-titulo">Saldo Disponible (CDP - OP)</div>
                <div class="resultado-valor">
                    $<?php echo number_format($saldo_op, 2, ',', '.'); ?>
                    <span class="resultado-porcentaje"><?php echo number_format($porcentaje_disponible_op, 2, ',', '.'); ?>%</span>
                </div>
            </div>
            <div class="resultado-item consumo-cdp">
                <div class="resultado-titulo">Consumo OP Viáticos</div>
                <div class="resultado-valor">
                    $<?php echo number_format($valor_op, 2, ',', '.'); ?>
                    <span class="resultado-porcentaje"><?php echo number_format($porcentaje_consumido_op, 2, ',', '.'); ?>%</span>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.register(ChartDataLabels);

        // Gráfico CDP
        new Chart(document.getElementById('viaticosCDPChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Presupuesto Consumido', 'Presupuesto Disponible'],
                datasets: [{
                    data: [
                        <?php echo $porcentaje_consumido; ?>,
                        <?php echo $porcentaje_disponible; ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 14 },
                        formatter: (value, ctx) => value ? value.toFixed(2) + '%' : '',
                        textAlign: 'center'
                    },
                    legend: { position: 'top', labels: { font: { size: 13 }, padding: 15 } },
                    title: {
                        display: true,
                        text: 'Distribución del Presupuesto de Viáticos (CDP) - Solo dependencias 62, 66, 69, 70',
                        font: { size: 16, weight: 'bold' },
                        padding: { top: 10, bottom: 15 }
                    }
                },
                layout: { padding: 10 }
            }
        });

        // Gráfico OP
        new Chart(document.getElementById('viaticosOPChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Presupuesto Consumido (OP)', 'Saldo Disponible (CDP - OP)'],
                datasets: [{
                    data: [
                        <?php echo $porcentaje_consumido_op; ?>,
                        <?php echo $porcentaje_disponible_op; ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 14 },
                        formatter: (value, ctx) => value ? value.toFixed(2) + '%' : '',
                        textAlign: 'center'
                    },
                    legend: { position: 'top', labels: { font: { size: 13 }, padding: 15 } },
                    title: {
                        display: true,
                        text: 'Distribución del Presupuesto de Viáticos Consumidos (OP) - Solo dependencias 62, 66, 69, 70',
                        font: { size: 16, weight: 'bold' },
                        padding: { top: 10, bottom: 15 }
                    }
                },
                layout: { padding: 10 }
            }
        });
    });
    </script>
</body>
