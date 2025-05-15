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
?>
<div class="indicadores">
    <div class="indicador">
        <h3>Terminados</h3>
        <p class="highlight"><?php echo $resumen['total_terminados']; ?></p>
    </div>
    <div class="indicador">
        <h3>Terminados (%)</h3>
        <p class="highlight"><?php echo $resumen['avance_porcentaje']; ?>%</p>
    </div>
    <div class="indicador">
        <h3>Proyección</h3>
        <p class="highlight-blue"><?php echo $total_esperado; ?></p>
    </div>
    <div class="indicador">
        <h3>Proyección (%)</h3>
        <p class="highlight-blue"><?php echo $porcentaje_esperado; ?>%</p>
    </div>
    <div class="indicador">
        <h3>Meta Proyectos</h3>
        <p class="highlight-yellow"><?php echo $meta_total; ?></p>
    </div>
</div>
<?php
$labels = array_map(fn($p) => $p['nombre_linea'], $proyectos);
$terminados = array_map(fn($p) => (int)$p['terminados'], $proyectos);
$enProceso = array_map(fn($p) => (int)$p['en_proceso'], $proyectos);
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
<img src="<?php echo $chartUrl; ?>" alt="Gráfica Proyectos de Base Tecnológica" class="grafica-img">
<div class="tabla-card">
    <table class="styled-table">
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
                <td>Total</td>
                <td><?php echo $total_terminados; ?></td>
                <td><?php echo $total_en_proceso; ?></td>
                <td><?php echo $total_proyeccion; ?></td>
            </tr>
        </tfoot>
    </table>
</div>