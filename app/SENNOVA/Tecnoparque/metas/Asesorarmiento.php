<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/graficas.php';

$miGraficas = new graficas_general_sennova();
$datosCDP = $miGraficas->obtenerGraficaCDP();
$datosCRP = $miGraficas->obtenerGraficaCRP();
$datosOP = $miGraficas->obtenerGraficaOP();
?>

<h2>Asesorar a 1 Cooperativa de Aprendices</h2>
<h2>Asesorar a 1 Cooperativa de Aprendices</h2>
<h2>Asesorar a 1 Cooperativa de Aprendices</h2>
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
 


</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
