<?php
session_start();
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/graficas.php';


if (isset($_SESSION['id_rol'])) {
    $rol = $_SESSION['id_rol'];
} else {
    header("Location: " . "includes/session/login.php");
    exit;
}
$miGraficas = new graficas();
$datosCDP = $miGraficas->obtenerGraficaCDP();
$datosCRP = $miGraficas->obtenerGraficaCRP();
$datosOP = $miGraficas->obtenerGraficaOP();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gráficas de Consumo por Dependencia</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container-graficas {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            text-align: right;
        }
        th {
            background: #f5f5f5;
            text-align: center;
        }
        td:first-child, th:first-child {
            text-align: left;
        }
        h2 {
            margin-top: 0;
        }
        .chart-wrapper {
            width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    <div class="container-graficas">
        <h2>Consumo por Dependencia (CDP)</h2>
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

        <div class="chart-wrapper">
            <canvas id="graficaCDP"></canvas>
        </div>

        <!-- Gráficas de torta por dependencia (CDP) -->
        <h3>Detalle por Dependencia (CDP)</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <?php foreach ($datosCDP as $i => $fila): ?>
                <div style="flex: 1 1 250px; min-width: 250px; max-width: 300px; text-align: center;">
                    <strong><?php echo htmlspecialchars($fila['nombre_dependencia']); ?> (<?php echo htmlspecialchars($fila['codigo_dependencia']); ?>)</strong>
                    <canvas id="tortaCDP_<?php echo $i; ?>"></canvas>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Segunda gráfica: CRP -->
        <h2>Utilización por Dependencia (CRP)</h2>
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
        <div class="chart-wrapper">
            <canvas id="graficaCRP"></canvas>
        </div>

        <!-- Gráficas de torta por dependencia (CRP) -->
        <h3>Detalle por Dependencia (CRP)</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <?php foreach ($datosCRP as $i => $fila): ?>
                <div style="flex: 1 1 250px; min-width: 250px; max-width: 300px; text-align: center;">
                    <strong><?php echo htmlspecialchars($fila['nombre_dependencia']); ?> (<?php echo htmlspecialchars($fila['codigo_dependencia']); ?>)</strong>
                    <canvas id="tortaCRP_<?php echo $i; ?>"></canvas>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tercera gráfica: OP -->
        <h2>Pagos por Dependencia (OP)</h2>
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
        <div class="chart-wrapper">
            <canvas id="graficaOP"></canvas>
        </div>

        <!-- Gráficas de torta por dependencia (OP) -->
        <h3>Detalle por Dependencia (OP)</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <?php foreach ($datosOP as $i => $fila): ?>
                <div style="flex: 1 1 250px; min-width: 250px; max-width: 300px; text-align: center;">
                    <strong><?php echo htmlspecialchars($fila['nombre_dependencia']); ?> (<?php echo htmlspecialchars($fila['codigo_dependencia']); ?>)</strong>
                    <canvas id="tortaOP_<?php echo $i; ?>"></canvas>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
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

    // Gráficas de torta por dependencia (CDP)
    <?php foreach ($datosCDP as $i => $fila): ?>
    new Chart(document.getElementById('tortaCDP_<?php echo $i; ?>').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Saldo por Comprometer', 'Valor Consumido'],
            datasets: [{
                data: [
                    <?php echo $fila['saldo_por_comprometer']; ?>,
                    <?php echo $fila['valor_consumido']; ?>
                ],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
    <?php endforeach; ?>

    // Segunda gráfica: CRP
    const datosCRP = <?php echo json_encode($datosCRP); ?>;
    const labelsCRP = datosCRP.map(d => d.nombre_dependencia + ' (' + d.codigo_dependencia + ')');
    const valorActualCRP = datosCRP.map(d => d.valor_actual);
    const saldoPorUtilizar = datosCRP.map(d => d.saldo_por_utilizar);
    const saldoUtilizado = datosCRP.map(d => d.saldo_utilizado);

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
                    backgroundColor: 'rgba(75, 192, 192, 0.5)'
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

    // Gráficas de torta por dependencia (CRP)
    <?php foreach ($datosCRP as $i => $fila): ?>
    new Chart(document.getElementById('tortaCRP_<?php echo $i; ?>').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Saldo por Utilizar', 'Saldo Utilizado'],
            datasets: [{
                data: [
                    <?php echo $fila['saldo_por_utilizar']; ?>,
                    <?php echo $fila['saldo_utilizado']; ?>
                ],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
    <?php endforeach; ?>

    // Tercera gráfica: OP
    const datosOP = <?php echo json_encode($datosOP); ?>;
    const labelsOP = datosOP.map(d => d.nombre_dependencia + ' (' + d.codigo_dependencia + ')');
    const sumaCRP = datosOP.map(d => d.suma_crp);
    const sumaOP = datosOP.map(d => d.suma_op);
    const valorRestante = datosOP.map(d => d.valor_restante);

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
                    backgroundColor: 'rgba(255, 99, 132, 0.5)'
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

    // Gráficas de torta por dependencia (OP)
    <?php foreach ($datosOP as $i => $fila): ?>
    new Chart(document.getElementById('tortaOP_<?php echo $i; ?>').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Total Pagado (OP)', 'Valor Restante'],
            datasets: [{
                data: [
                    <?php echo $fila['suma_op']; ?>,
                    <?php echo $fila['valor_restante']; ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
    <?php endforeach; ?>
    </script>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>
