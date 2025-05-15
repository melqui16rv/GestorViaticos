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
<!-- Título formal -->
<div style="margin-bottom: 1.5em;">
    <h3 style="font-size:1.15em; color:#1e293b; font-weight:600; margin-bottom:0.2em;">Resumen de Asesoramientos</h3>
    <p style="font-size:1em; color:#334155; margin:0;">
        <strong>Total:</strong> <?php echo count($asesoramientos); ?>
        <?php foreach ($tiposAso as $tipo => $cantidad): ?>
            &nbsp;|&nbsp; <strong><?php echo htmlspecialchars($tipo); ?>:</strong> <?php echo $cantidad; ?>
        <?php endforeach; ?>
    </p>
</div>

<img src="<?php echo $chartUrl; ?>" alt="Gráfica Asesoramiento por tipo" class="grafica-img">

<!-- Tabla formal -->
<table class="styled-table" style="margin-top:1.5em;">
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
