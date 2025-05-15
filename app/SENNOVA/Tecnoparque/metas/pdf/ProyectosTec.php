<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();
$proyectos = $metas->obtenerProyectosTecPorTipo('Tecnológico');
$resumen = $metas->obtenerSumaProyectosTecTerminadosPorTipo('Tecnológico');

$meta_total = 100;
$total_esperado = 0;
foreach ($proyectos as $p) {
    $total_esperado += (int)$p['terminados'] + (int)$p['en_proceso'];
}
$porcentaje_esperado = min(100, round(($total_esperado / $meta_total) * 100, 1));

// Gráfica principal de barras
$labels = array_map(fn($p) => $p['nombre_linea'], $proyectos);
$terminados = array_map(fn($p) => (int)$p['terminados'], $proyectos);
$enProceso = array_map(fn($p) => (int)$p['en_proceso'], $proyectos);
$proyeccion = array_map(fn($p) => (int)$p['terminados'] + (int)$p['en_proceso'], $proyectos);

$chartConfig = [
    "type" => "bar",
    "data" => [
        "labels" => $labels,
        "datasets" => [
            [
                "label" => "Terminados",
                "backgroundColor" => "rgba(34,197,94,0.75)",
                "data" => $terminados
            ],
            [
                "label" => "En Proceso",
                "backgroundColor" => "rgba(253,224,71,0.65)",
                "data" => $enProceso
            ],
            [
                "label" => "Proyección",
                "backgroundColor" => "rgba(59,130,246,0.60)",
                "data" => $proyeccion
            ]
        ]
    ],
    "options" => [
        "plugins" => ["legend" => ["position" => "top"]],
        "responsive" => true,
        "scales" => ["y" => ["beginAtZero" => true]]
    ]
];
$chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
?>
<!-- Título formal -->
<div style="margin-bottom: 1.5em;">
    <h3 style="font-size:1.15em; color:#1e293b; font-weight:600; margin-bottom:0.2em;">Resumen de Proyectos de Base Tecnológica</h3>
    <p style="font-size:1em; color:#334155; margin:0;">
        <strong>Meta:</strong> <?php echo $meta_total; ?> &nbsp;|&nbsp;
        <strong>Terminados:</strong> <?php echo $resumen['total_terminados']; ?> (<?php echo $resumen['avance_porcentaje']; ?>%) &nbsp;|&nbsp;
        <strong>Proyección:</strong> <?php echo $total_esperado; ?> (<?php echo $porcentaje_esperado; ?>%)
    </p>
</div>

<img src="<?php echo $chartUrl; ?>" alt="Gráfica Proyectos de Base Tecnológica" class="grafica-img">

<!-- Tabla formal -->
<table class="styled-table" style="margin-top:1.5em;">
    <thead>
        <tr>
            <th>Línea</th>
            <th>Terminados</th>
            <th>En Proceso</th>
            <th>Proyección</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_terminados = 0;
        $total_en_proceso = 0;
        $total_proyeccion = 0;
        foreach ($proyectos as $p):
            $proy = (int)$p['terminados'] + (int)$p['en_proceso'];
            $total_terminados += (int)$p['terminados'];
            $total_en_proceso += (int)$p['en_proceso'];
            $total_proyeccion += $proy;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($p['nombre_linea']); ?></td>
            <td><?php echo (int)$p['terminados']; ?></td>
            <td><?php echo (int)$p['en_proceso']; ?></td>
            <td><?php echo $proy; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total</strong></td>
            <td><?php echo $total_terminados; ?></td>
            <td><?php echo $total_en_proceso; ?></td>
            <td><?php echo $total_proyeccion; ?></td>
        </tr>
    </tfoot>
</table>