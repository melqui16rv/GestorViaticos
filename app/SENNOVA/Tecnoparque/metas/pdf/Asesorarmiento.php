<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();
$asesoramientos = $metas->obtenerAsesoramientos([]);
$tiposAso = [];
foreach ($asesoramientos as $a) {
    $tipo = $a['tipo'];
    if (!isset($tiposAso[$tipo])) $tiposAso[$tipo] = 0;
    $tiposAso[$tipo]++;
}
?>
<div class="indicadores">
    <div class="indicador">
        <h3>Total Asesoramientos</h3>
        <p class="highlight"><?php echo count($asesoramientos); ?></p>
    </div>
    <?php foreach ($tiposAso as $tipo => $cantidad): ?>
    <div class="indicador">
        <h3><?php echo htmlspecialchars($tipo); ?></h3>
        <p class="highlight-blue"><?php echo $cantidad; ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php
$labels = array_keys($tiposAso);
$data = array_values($tiposAso);
$chartConfig = [
    "type" => "bar",
    "data" => [
        "labels" => $labels,
        "datasets" => [[
            "label" => "Cantidad",
            "backgroundColor" => "rgba(59,130,246,0.60)",
            "data" => $data
        ]]
    ],
    "options" => [
        "plugins" => ["legend" => ["display" => false]],
        "responsive" => true,
        "scales" => ["y" => ["beginAtZero" => true]]
    ]
];
$chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
function formatearFechaAsoPDF($fecha) {
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    $timestamp = strtotime($fecha);
    $dia = date('j', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);
    $hora = strtolower(date('g:i a', $timestamp));
    return $dia . ' de ' . $mes . ' ' . $anio . ' ' . $hora;
}
?>
<img src="<?php echo $chartUrl; ?>" alt="Gráfica Asesoramiento por tipo" class="grafica-img">
<div class="tabla-card">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Encargado</th>
                <th>Entidad Impactada</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($asesoramientos as $a): ?>
            <tr>
                <td><?php echo htmlspecialchars($a['id_asesoramiendo'] ?? $a['id']); ?></td>
                <td><?php echo htmlspecialchars($a['tipo']); ?></td>
                <td><?php echo htmlspecialchars($a['encargadoAsesoramiento'] ?? $a['encargado']); ?></td>
                <td><?php echo htmlspecialchars($a['nombreEntidadImpacto'] ?? $a['entidad']); ?></td>
                <td><?php echo formatearFechaAsoPDF($a['fechaAsesoramiento'] ?? $a['fecha']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
