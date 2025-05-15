<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();

function formatearFecha($fecha) {
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

$visitas = $metas->obtenerVisitasApre();
$indicadores = $metas->obtenerIndicadoresVisitas();

// Visitas por nodo
$visitasPorNodo = [];
foreach($visitas as $v) {
    $nodo = $v['nodo'] ?? 'Desconocido';
    if (!isset($visitasPorNodo[$nodo])) $visitasPorNodo[$nodo] = 0;
    $visitasPorNodo[$nodo]++;
}

// Gráfica: Asistentes por encargado
$asistentesPorEncargado = [];
foreach ($visitas as $v) {
    $enc = $v['encargado'];
    if (!isset($asistentesPorEncargado[$enc])) $asistentesPorEncargado[$enc] = 0;
    $asistentesPorEncargado[$enc] += (int)$v['numAsistentes'];
}
$labelsAsistentesEnc = array_keys($asistentesPorEncargado);
$dataAsistentesEnc = array_values($asistentesPorEncargado);

// Gráfica: Visitas por encargado (corregido el error)
$visitasPorEncargado = [];
foreach ($visitas as $v) {
    $enc = $v['encargado'];
    $visitasPorEncargado[$enc] = ($visitasPorEncargado[$enc] ?? 0) + 1;
}
$labelsVisitasEnc = array_keys($visitasPorEncargado);
$dataVisitasEnc = array_values($visitasPorEncargado);

// Gráfica: Visitas por semana (corregido para cubrir el rango real de fechas)
$fechas = array_column($visitas, 'fechaCharla');
if (!empty($fechas)) {
    $minFecha = min(array_map(fn($f) => strtotime($f), $fechas));
    $maxFecha = max(array_map(fn($f) => strtotime($f), $fechas));
    // Ajustar al lunes de la semana de la fecha mínima
    $start = new DateTime();
    $start->setTimestamp($minFecha);
    $start->modify('monday this week');
    // Ajustar al domingo de la semana de la fecha máxima
    $end = new DateTime();
    $end->setTimestamp($maxFecha);
    $end->modify('sunday this week');
    // Generar semanas desde $start hasta $end (máximo 5)
    $semanas = [];
    $current = clone $end;
    for ($i = 0; $i < 5; $i++) {
        $weekStart = clone $current;
        $weekStart->modify('monday this week');
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');
        $label = $weekStart->format('d M') . " - " . $weekEnd->format('d M');
        $semanas[$label] = ['start' => clone $weekStart, 'end' => clone $weekEnd, 'count' => 0];
        $current->modify('-1 week');
    }
    $semanas = array_reverse($semanas, true);
    foreach($visitas as $v) {
        $fecha = new DateTime($v['fechaCharla']);
        foreach ($semanas as $label => &$datos) {
            if ($fecha >= $datos['start'] && $fecha <= $datos['end']) {
                $datos['count']++;
            }
        }
    }
    $labelsSemanales = array_keys($semanas);
    $dataSemanales = array_map(fn($d) => $d['count'], $semanas);
} else {
    $labelsSemanales = [];
    $dataSemanales = [];
}

// Gráficas con quickchart.io
$chartAsistentesEnc = [
    "type" => "bar",
    "data" => [
        "labels" => $labelsAsistentesEnc,
        "datasets" => [[
            "label" => "Asistentes",
            "backgroundColor" => "rgba(59,130,246,0.60)",
            "data" => $dataAsistentesEnc
        ]]
    ],
    "options" => [
        "plugins" => ["legend" => ["display" => false]],
        "responsive" => true,
        "scales" => ["y" => ["beginAtZero" => true]]
    ]
];
$chartUrlAsistentesEnc = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartAsistentesEnc));

$chartVisitasEnc = [
    "type" => "bar",
    "data" => [
        "labels" => $labelsVisitasEnc,
        "datasets" => [[
            "label" => "Visitas",
            "backgroundColor" => "rgba(255,159,64,0.60)",
            "data" => $dataVisitasEnc
        ]]
    ],
    "options" => [
        "plugins" => ["legend" => ["display" => false]],
        "responsive" => true,
        "scales" => ["y" => ["beginAtZero" => true]]
    ]
];
$chartUrlVisitasEnc = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartVisitasEnc));

$chartSemanal = [
    "type" => "line",
    "data" => [
        "labels" => $labelsSemanales,
        "datasets" => [[
            "label" => "Visitas por semana",
            "backgroundColor" => "rgba(54,162,235,0.25)",
            "borderColor" => "rgba(54,162,235,1)",
            "fill" => true,
            "data" => $dataSemanales
        ]]
    ],
    "options" => [
        "plugins" => ["legend" => ["display" => true, "position" => "top"]],
        "responsive" => true,
        "scales" => ["y" => ["beginAtZero" => true]]
    ]
];
$chartUrlSemanal = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartSemanal));
?>
<div class="indicadores">
    <div class="indicador">
        <h3>Total de Asistentes</h3>
        <p><?php echo $indicadores['total_asistentes']; ?></p>
    </div>
    <div class="indicador">
        <h3>Total de Charlas</h3>
        <p><?php echo $indicadores['total_charlas']; ?></p>
    </div>
    <div class="indicador">
        <h3>Promedio de Asistentes por Charla</h3>
        <p><?php echo $indicadores['promedio_asistentes']; ?></p>
    </div>
    <div class="indicador">
        <h3>Visitas por Nodo</h3>
        <p>
            <?php foreach($visitasPorNodo as $nodo => $cant){
                echo htmlspecialchars($nodo) . ": " . $cant . "<br>";
            } ?>
        </p>
    </div>
</div>

<img src="<?php echo $chartUrlAsistentesEnc; ?>" alt="Asistentes por Encargado" class="grafica-img">
<div class="subtle" style="text-align:center;">Asistentes por Encargado</div>

<img src="<?php echo $chartUrlVisitasEnc; ?>" alt="Visitas por Encargado" class="grafica-img">
<div class="subtle" style="text-align:center;">Visitas realizadas por Encargado</div>

<img src="<?php echo $chartUrlSemanal; ?>" alt="Visitas por Semana" class="grafica-img">
<div class="subtle" style="text-align:center;">Visitas por Semana (últimas 5 semanas)</div>

<div class="tabla-card">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Encargado</th>
                <th>Número de Asistentes</th>
                <th>Fecha de la Charla</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visitas as $visita): ?>
            <tr>
                <td><?php echo htmlspecialchars($visita['id_visita']); ?></td>
                <td><?php echo htmlspecialchars($visita['encargado']); ?></td>
                <td><?php echo htmlspecialchars($visita['numAsistentes']); ?></td>
                <td><?php echo formatearFecha($visita['fechaCharla']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
