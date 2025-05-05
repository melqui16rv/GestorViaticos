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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Viáticos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto grid gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Presupuesto de Viáticos (CDP)</h2>
            <div class="flex flex-col md:flex-row gap-6">
                <div class="w-full md:w-1/2">
                    <div class="graficaContenedor">
                        <canvas id="viaticosCDPChart"></canvas>
                    </div>
                </div>
                <div class="w-full md:w-1/2 flex flex-col gap-6">
                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">Valor Total Viáticos (CDP)</div>
                        <div class="text-lg font-bold text-blue-600">
                            $<?php echo number_format($valor_actual, 2, ',', '.'); ?>
                            <span class="text-green-500 font-medium">100%</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">Saldo Disponible Viáticos (CDP)</div>
                        <div class="text-lg font-bold text-green-600">
                            $<?php echo number_format($saldo_por_comprometer, 2, ',', '.'); ?>
                            <span class="text-blue-500 font-medium"><?php echo number_format($porcentaje_disponible, 2, ',', '.'); ?>%</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">Consumo CDP Viáticos</div>
                        <div class="text-lg font-bold text-red-600">
                            $<?php echo number_format($consumo_cdp, 2, ',', '.'); ?>
                            <span class="text-blue-500 font-medium"><?php echo number_format($porcentaje_consumido, 2, ',', '.'); ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Ejecución del Presupuesto (OP)</h2>
            <div class="flex flex-col md:flex-row gap-6">
                <div class="w-full md:w-1/2">
                    <div class="graficaContenedor">
                        <canvas id="viaticosOPChart"></canvas>
                    </div>
                </div>
                <div class="w-full md:w-1/2 flex flex-col gap-6">
                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">Valor Total Comprometido (CDP)</div>
                        <div class="text-lg font-bold text-blue-600">
                            $<?php echo number_format($consumo_cdp, 2, ',', '.'); ?>
                            <span class="text-green-500 font-medium">100%</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">Saldo Disponible (CDP - OP)</div>
                        <div class="text-lg font-bold text-green-600">
                            $<?php echo number_format($saldo_op, 2, ',', '.'); ?>
                            <span class="text-blue-500 font-medium"><?php echo number_format($porcentaje_disponible_op, 2, ',', '.'); ?>%</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">Consumo OP Viáticos</div>
                        <div class="text-lg font-bold text-red-600">
                            $<?php echo number_format($valor_op, 2, ',', '.'); ?>
                            <span class="text-blue-500 font-medium"><?php echo number_format($porcentaje_consumido_op, 2, ',', '.'); ?>%</span>
                        </div>
                    </div>
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
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value, ctx) => value ? value.toFixed(2) + '%' : '',
                        textAlign: 'center'
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 13
                            },
                            padding: 15
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribución del Presupuesto de Viáticos (CDP)',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 15
                        }
                    }
                },
                layout: {
                    padding: 10
                }
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
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value, ctx) => value ? value.toFixed(2) + '%' : '',
                        textAlign: 'center'
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 13
                            },
                            padding: 15
                        }
                    },
                    title: {
                        display: true,
                        text: 'Ejecución del Presupuesto de Viáticos (OP)',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 15
                        }
                    }
                },
                layout: {
                    padding: 10
                }
            }
        });
    });
    </script>
</body>
</html>
