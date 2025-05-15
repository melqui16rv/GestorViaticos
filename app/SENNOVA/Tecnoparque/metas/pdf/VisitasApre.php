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

// Gráfica: Visitas por encargado
$visitasPorEncargado = [];
foreach ($visitas as $v) {
    $enc = $v['encargado'];
    $visitasPorEncargado[$enc] = ($visitasPorEncargado[$enc] ?? 0) + 1;
}
$labelsVisitasEnc = array_keys($visitasPorEncargado);
$dataVisitasEnc = array_values($visitasPorEncargado);

// Gráficas con quickchart.io
$chartAsistentesEnc = [
    "type" => "bar",
    "data" => [
        "labels" => $labelsAsistentesEnc,
        "datasets" => [[
            "label" => "Asistentes por Encargado",
            "backgroundColor" => "rgba(59,130,246,0.60)",
            "data" => $dataAsistentesEnc
        ]]
    ],
    "options" => [
        "plugins" => [
            "legend" => ["display" => false],
            "title" => [
                "display" => true,
                "text" => "Nivel Impacto X Encargado",
                "font" => ["size" => 18]
            ]
        ],
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
            "label" => "Visitas realizadas por Encargado",
            "backgroundColor" => "rgba(255,159,64,0.60)",
            "data" => $dataVisitasEnc
        ]]
    ],
    "options" => [
        "plugins" => [
            "legend" => ["display" => false],
            "title" => [
                "display" => true,
                "text" => "Visitas realizadas X Encargado",
                "font" => ["size" => 18]
            ]
        ],
        "responsive" => true,
        "scales" => ["y" => ["beginAtZero" => true]]
    ]
];
$chartUrlVisitasEnc = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartVisitasEnc));
?>
<!-- Título formal -->
<div style="margin-bottom: 1em; margin-top: 1em;">
    <h3 style="font-size:1.15em; color:#1e293b; font-weight:600; margin-bottom:0.2em;">Resumen de Visitas de Aprendices</h3>
    <p style="font-size:1em; color:#334155; margin:0;">
        <strong>Visitas por Nodo:</strong>
        <?php foreach($visitasPorNodo as $nodo => $cant){
            echo htmlspecialchars($nodo) . ": " . $cant . " &nbsp; ";
        } ?>
    </p>
</div>

<img src="<?php echo $chartUrlAsistentesEnc; ?>" alt="Nivel Impacto X Encargado" class="grafica-img">
<img src="<?php echo $chartUrlVisitasEnc; ?>" alt="Visitas realizadas X Encargado" class="grafica-img">

<!-- Tabla formal -->
<table class="styled-table" style="margin-top:1em;">
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
