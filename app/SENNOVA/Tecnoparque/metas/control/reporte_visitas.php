<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Metas Tecnoparque</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: #f8fafc;
            color: #22223b;
        }
        .pdf-container {
            margin: 0 auto 2rem auto;
            background: #fff;
            border-radius: 10px;
            max-width: 900px;
            min-width: 650px;
            padding: 2.5rem 2.5rem 2.5rem 2.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
        }
        h1, h2, h3 {
            color: #1e293b;
            margin-bottom: 0.3em;
        }
        h1 {
            text-align: center;
            font-size: 2.1em;
            margin-bottom: 0.2em;
            letter-spacing: 1px;
        }
        h2 {
            font-size: 1.3em;
            margin-top: 2em;
            margin-bottom: 0.5em;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 0.2em;
        }
        h3 {
            font-size: 1.1em;
            margin-top: 1.2em;
            margin-bottom: 0.3em;
        }
        .indicadores {
            display: flex;
            flex-wrap: wrap;
            gap: 2.5em;
            margin: 1.2em 0 1.5em 0;
        }
        .indicador {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 1em 1.5em;
            min-width: 160px;
            box-shadow: 0 1px 4px rgba(37,99,235,0.07);
            text-align: center;
        }
        .indicador h3 {
            margin: 0 0 0.3em 0;
            font-size: 1em;
            color: #2563eb;
        }
        .indicador p {
            font-size: 1.25em;
            font-weight: bold;
            margin: 0;
            color: #22223b;
        }
        .section-break {
            page-break-before: always;
            margin-top: 2em;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.2em;
            font-size: 11.5px;
            background: #fff;
        }
        .styled-table th, .styled-table td {
            border: 1px solid #b6c2d1;
            padding: 7px 10px;
            text-align: center;
        }
        .styled-table th {
            background: #2563eb;
            color: #fff;
            font-size: 1em;
        }
        .styled-table tfoot td {
            font-weight: bold;
            background: #e0e7ef;
        }
        .grafica-img {
            display: block;
            margin: 1.5em auto 2em auto;
            width: 100%;
            max-width: 650px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            padding: 0.5em;
        }
        .torta-title {
            font-weight: bold;
            margin-top: 0.5em;
            color: #2563eb;
        }
        .torta-info {
            font-size: 0.98em;
            color: #555;
            margin-bottom: 0.5em;
        }
        .tortas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5em;
            margin-top: 1.5em;
            margin-bottom: 1.5em;
        }
        .torta-card {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 1em 1.2em;
            min-width: 220px;
            max-width: 260px;
            flex: 1 1 220px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(37,99,235,0.07);
        }
        .torta-card img {
            width: 90%;
            max-width: 180px;
            margin: 0.5em auto 0.5em auto;
        }
        .tabla-card {
            margin-bottom: 2em;
        }
        .hr {
            border: none;
            border-top: 2px solid #2563eb;
            margin: 1.5em 0;
        }
        @media print {
            body { background: #fff; }
            .pdf-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <h1>Reporte de Metas Tecnoparque</h1>
        <div style="text-align:center; color:#64748b; font-size:1.1em; margin-bottom:1.5em;">
            <strong>Fecha de generación:</strong> <?php echo date('d/m/Y H:i'); ?>
        </div>
        <hr class="hr">

        <!-- Proyectos de Base Tecnológica -->
        <section>
            <h2>Proyectos de Base Tecnológica</h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosTec.php';
            // Eliminar links de estilos que puedan venir de la vista incluida
            ob_start();
            $metasTec = new metas_tecnoparque();
            $proyectosTec = $metasTec->obtenerProyectosTecPorTipo('Tecnológico');
            $resumenTec = $metasTec->obtenerSumaProyectosTecTerminadosPorTipo('Tecnológico');
            $meta_total_tec = 100;
            $total_esperado_tec = 0;
            foreach ($proyectosTec as $p) {
                $total_esperado_tec += (int)$p['terminados'] + (int)$p['en_proceso'];
            }
            $porcentaje_esperado_tec = min(100, round(($total_esperado_tec / $meta_total_tec) * 100, 1));
            $labelsTec = array_map(fn($p) => $p['nombre_linea'], $proyectosTec);
            $terminadosTec = array_map(fn($p) => (int)$p['terminados'], $proyectosTec);
            $enProcesoTec = array_map(fn($p) => (int)$p['en_proceso'], $proyectosTec);
            $chartConfigTec = [
                "type" => "bar",
                "data" => [
                    "labels" => $labelsTec,
                    "datasets" => [
                        [
                            "label" => "Terminados",
                            "backgroundColor" => "rgba(34,197,94,0.75)",
                            "data" => $terminadosTec
                        ],
                        [
                            "label" => "En Proceso",
                            "backgroundColor" => "rgba(253,224,71,0.65)",
                            "data" => $enProcesoTec
                        ]
                    ]
                ],
                "options" => [
                    "plugins" => ["legend" => ["position" => "top"]],
                    "responsive" => true,
                    "scales" => ["y" => ["beginAtZero" => true]]
                ]
            ];
            $chartUrlTec = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfigTec));
            $section_html = ob_get_clean();
            // Quitar links de estilos
            $section_html = preg_replace('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', '', $section_html);
            echo $section_html;
            ?>
            <div class="indicadores">
                <div class="indicador">
                    <h3>Terminados</h3>
                    <p><?php echo $resumenTec['total_terminados']; ?></p>
                </div>
                <div class="indicador">
                    <h3>Terminados (%)</h3>
                    <p><?php echo $resumenTec['avance_porcentaje']; ?>%</p>
                </div>
                <div class="indicador">
                    <h3>Proyección</h3>
                    <p><?php echo $total_esperado_tec; ?></p>
                </div>
                <div class="indicador">
                    <h3>Proyección (%)</h3>
                    <p><?php echo $porcentaje_esperado_tec; ?>%</p>
                </div>
                <div class="indicador">
                    <h3>Meta Proyectos</h3>
                    <p><?php echo $meta_total_tec; ?></p>
                </div>
            </div>
            <img src="<?php echo $chartUrlTec; ?>" alt="Gráfica Proyectos de Base Tecnológica" class="grafica-img">
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
                        foreach ($proyectosTec as $p):
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
        </section>

        <div class="section-break"></div>

        <!-- Asesoramiento -->
        <section>
            <h2>Asesoramiento</h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/Asesorarmiento.php';
            ob_start();
            $asesoramientos = $metasTec->obtenerAsesoramientos([]);
            $tiposAso = [];
            foreach ($asesoramientos as $a) {
                $tipo = $a['tipo'];
                if (!isset($tiposAso[$tipo])) $tiposAso[$tipo] = 0;
                $tiposAso[$tipo]++;
            }
            $labelsAso = array_keys($tiposAso);
            $dataAso = array_values($tiposAso);
            $chartConfigAso = [
                "type" => "bar",
                "data" => [
                    "labels" => $labelsAso,
                    "datasets" => [[
                        "label" => "Cantidad",
                        "backgroundColor" => "rgba(59,130,246,0.60)",
                        "data" => $dataAso
                    ]]
                ],
                "options" => [
                    "plugins" => ["legend" => ["display" => false]],
                    "responsive" => true,
                    "scales" => ["y" => ["beginAtZero" => true]]
                ]
            ];
            $chartUrlAso = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfigAso));
            $section_html = ob_get_clean();
            $section_html = preg_replace('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', '', $section_html);
            echo $section_html;
            ?>
            <div class="indicadores">
                <div class="indicador">
                    <h3>Total Asesoramientos</h3>
                    <p><?php echo count($asesoramientos); ?></p>
                </div>
                <?php foreach ($tiposAso as $tipo => $cantidad): ?>
                <div class="indicador">
                    <h3><?php echo htmlspecialchars($tipo); ?></h3>
                    <p><?php echo $cantidad; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <img src="<?php echo $chartUrlAso; ?>" alt="Gráfica Asesoramiento por tipo" class="grafica-img">
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
                        <?php
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
                        foreach ($asesoramientos as $a): ?>
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
        </section>

        <div class="section-break"></div>

        <!-- Proyectos de Extensionismo -->
        <section>
            <h2>Proyectos de Extensionismo</h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosExt.php';
            ob_start();
            $metasExt = new metas_tecnoparqueExt();
            $proyectosExt = $metasExt->obtenerProyectosTecPorTipo('Extensionismo');
            $resumenExt = $metasExt->obtenerSumaProyectosTecPorTipo('Extensionismo');
            $meta_total_ext = 5;
            $total_esperado_ext = 0;
            foreach ($proyectosExt as $p) {
                $total_esperado_ext += (int)$p['terminados'] + (int)$p['en_proceso'];
            }
            $porcentaje_terminados_ext = min(100, round(($resumenExt['total_terminados'] / $meta_total_ext) * 100, 1));
            $porcentaje_esperado_ext = min(100, round(($total_esperado_ext / $meta_total_ext) * 100, 1));
            $labelsExt = array_map(fn($p) => $p['nombre_linea'], $proyectosExt);
            $terminadosExt = array_map(fn($p) => (int)$p['terminados'], $proyectosExt);
            $enProcesoExt = array_map(fn($p) => (int)$p['en_proceso'], $proyectosExt);
            $chartConfigExt = [
                "type" => "bar",
                "data" => [
                    "labels" => $labelsExt,
                    "datasets" => [
                        [
                            "label" => "Terminados",
                            "backgroundColor" => "rgba(34,197,94,0.75)",
                            "data" => $terminadosExt
                        ],
                        [
                            "label" => "En Proceso",
                            "backgroundColor" => "rgba(253,224,71,0.65)",
                            "data" => $enProcesoExt
                        ]
                    ]
                ],
                "options" => [
                    "plugins" => ["legend" => ["position" => "top"]],
                    "responsive" => true,
                    "scales" => ["y" => ["beginAtZero" => true]]
                ]
            ];
            $chartUrlExt = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfigExt));
            $section_html = ob_get_clean();
            $section_html = preg_replace('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', '', $section_html);
            echo $section_html;
            ?>
            <div class="indicadores">
                <div class="indicador">
                    <h3>Terminados</h3>
                    <p><?php echo $resumenExt['total_terminados']; ?></p>
                </div>
                <div class="indicador">
                    <h3>Porcentaje Terminado</h3>
                    <p><?php echo $porcentaje_terminados_ext; ?>%</p>
                </div>
                <div class="indicador">
                    <h3>En Proceso</h3>
                    <p><?php echo $resumenExt['total_en_proceso']; ?></p>
                </div>
                <div class="indicador">
                    <h3>Porcentaje Esperado</h3>
                    <p><?php echo $porcentaje_esperado_ext; ?>%</p>
                </div>
                <div class="indicador">
                    <h3>Meta Proyectos</h3>
                    <p><?php echo $meta_total_ext; ?></p>
                </div>
            </div>
            <img src="<?php echo $chartUrlExt; ?>" alt="Gráfica Proyectos de Extensionismo" class="grafica-img">
            <div class="tabla-card">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Línea Estratégica</th>
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
                        foreach ($proyectosExt as $p):
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
        </section>

        <div class="section-break"></div>

        <!-- Visitas de Aprendices -->
        <section>
            <h2>Visitas de Aprendices</h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/VisitasApre.php';
            ob_start();
            $visitas = $metasTec->obtenerVisitasApre();
            $indicadoresVis = $metasTec->obtenerIndicadoresVisitas();
            $encargados = [];
            foreach ($visitas as $v) {
                $enc = $v['encargado'];
                if (!isset($encargados[$enc])) $encargados[$enc] = 0;
                $encargados[$enc]++;
            }
            $labelsVis = array_keys($encargados);
            $dataVis = array_values($encargados);
            $chartConfigVis = [
                "type" => "bar",
                "data" => [
                    "labels" => $labelsVis,
                    "datasets" => [[
                        "label" => "Visitas",
                        "backgroundColor" => "rgba(59,130,246,0.60)",
                        "data" => $dataVis
                    ]]
                ],
                "options" => [
                    "plugins" => ["legend" => ["display" => false]],
                    "responsive" => true,
                    "scales" => ["y" => ["beginAtZero" => true]]
                ]
            ];
            $chartUrlVis = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfigVis));
            $section_html = ob_get_clean();
            $section_html = preg_replace('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', '', $section_html);
            echo $section_html;
            ?>
            <div class="indicadores">
                <div class="indicador">
                    <h3>Total de Asistentes</h3>
                    <p><?php echo $indicadoresVis['total_asistentes']; ?></p>
                </div>
                <div class="indicador">
                    <h3>Total de Charlas</h3>
                    <p><?php echo $indicadoresVis['total_charlas']; ?></p>
                </div>
                <div class="indicador">
                    <h3>Promedio de Asistentes por Charla</h3>
                    <p><?php echo $indicadoresVis['promedio_asistentes']; ?></p>
                </div>
                <div class="indicador">
                    <h3>Visitas por Nodo</h3>
                    <p style="font-size:0.95em;">
                        <?php
                        $visitasPorNodo = [];
                        foreach($visitas as $v) {
                            $nodo = $v['nodo'] ?? 'Desconocido';
                            if (!isset($visitasPorNodo[$nodo])) $visitasPorNodo[$nodo] = 0;
                            $visitasPorNodo[$nodo]++;
                        }
                        foreach($visitasPorNodo as $nodo => $cant){
                            echo htmlspecialchars($nodo) . ": " . $cant . "<br>";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <img src="<?php echo $chartUrlVis; ?>" alt="Gráfica Visitas por Encargado" class="grafica-img">
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
                        <?php
                        function formatearFechaVisitaPDF($fecha) {
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
                        foreach ($visitas as $visita): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($visita['id_visita']); ?></td>
                            <td><?php echo htmlspecialchars($visita['encargado']); ?></td>
                            <td><?php echo htmlspecialchars($visita['numAsistentes']); ?></td>
                            <td><?php echo formatearFechaVisitaPDF($visita['fechaCharla']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();
$html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
$html = preg_replace('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', '', $html);
if (ob_get_length()) ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter']);
$mpdf->WriteHTML($html);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_metas_tecnoparque.pdf"');
$mpdf->Output('reporte_metas_tecnoparque.pdf', 'D');
exit;