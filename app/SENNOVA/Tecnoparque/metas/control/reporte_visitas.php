<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// /app/SENNOVA/Tecnoparque/metas/control/reporte_visitas.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'; // Asegúrate de tener mpdf instalado

// Capturar el HTML de las vistas SIN navbar ni sidebar
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Metas Tecnoparque</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; background: #f8fafc; }
        h1, h2, h3 { color: #2b3b4f; }
        h1 { text-align: center; font-size: 1.7em; margin-bottom: 0.2em; }
        h2 { font-size: 1.2em; margin-top: 1.5em; }
        .section-break { page-break-before: always; }
        .pdf-container {
            margin: 0 auto 1.5rem auto;
            background: #fff;
            border-radius: 8px;
            max-width: 900px;
            min-width: 650px;
            padding: 1.5rem 2.5rem 2rem 2.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .stats-card, .tabla-card { margin-bottom: 1rem; }
        .stat-item { display: inline-block; min-width: 100px; margin-right: 0.5rem; }
        .styled-table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; font-size: 10.5px; }
        .styled-table th, .styled-table td { border: 1px solid #bbb; padding: 4px 6px; }
        .styled-table th { background: #2563eb; color: #fff; }
        .indicadores { margin: 1em 0; }
        .indicador { display: inline-block; margin-right: 2em; }
        .torta-title { font-weight: bold; margin-top: 0.5em; }
        .torta-info { font-size: 0.95em; color: #555; }
        .no-grafica { color: #b91c1c; font-size: 0.95em; margin: 1em 0; }
        /* Ocultar botones, formularios y elementos interactivos que no tienen sentido en PDF */
        button, .actualizar-tabla-link, form, .sidebar-filament, .sidebar-link, .sidebar-toggle-btn, .sidebar-overlay { display: none !important; }
        /* Ajuste de imágenes de gráficas */
        .grafica-img {
            display: block;
            margin: 0 auto 1.5em auto;
            width: 100%;
            max-width: 700px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #fff;
            padding: 0.5em;
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
    <hr>
    <section>
        <h2>Proyectos de Base Tecnológica</h2>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosTec.php';
        // --- Gráfica de barras para Proyectos de Base Tecnológica ---
        $metasTec = new metas_tecnoparque();
        $proyectosTec = $metasTec->obtenerProyectosTecPorTipo('Tecnológico');
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
        ?>
        <div style="text-align:center;margin:1em 0;">
            <img src="<?php echo $chartUrlTec; ?>" alt="Gráfica Proyectos de Base Tecnológica" class="grafica-img">
        </div>
    </section>
    <div class="section-break"></div>
    <section>
        <h2>Asesoramiento</h2>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/Asesorarmiento.php';
        // --- Gráfica de barras para Asesoramiento (por tipo) ---
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
        ?>
        <div style="text-align:center;margin:1em 0;">
            <img src="<?php echo $chartUrlAso; ?>" alt="Gráfica Asesoramiento por tipo" class="grafica-img">
        </div>
    </section>
    <div class="section-break"></div>
    <section>
        <h2>Proyectos de Extensionismo</h2>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosExt.php';
        $metasExt = new metas_tecnoparqueExt();
        $proyectosExt = $metasExt->obtenerProyectosTecPorTipo('Extensionismo');
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
        ?>
        <div style="text-align:center;margin:1em 0;">
            <img src="<?php echo $chartUrlExt; ?>" alt="Gráfica Proyectos de Extensionismo" class="grafica-img">
        </div>
    </section>
    <div class="section-break"></div>
    <section>
        <h2>Visitas de Aprendices</h2>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/VisitasApre.php';
        $visitas = $metasTec->obtenerVisitasApre();
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
        ?>
        <div style="text-align:center;margin:1em 0;">
            <img src="<?php echo $chartUrlVis; ?>" alt="Gráfica Visitas por Encargado" class="grafica-img">
        </div>
    </section>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// Quitar scripts JS y recursos innecesarios para el PDF
$html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);

// Limpiar cualquier salida previa antes de enviar headers y PDF
if (ob_get_length()) ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter']);
$mpdf->WriteHTML($html);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_metas_tecnoparque.pdf"');
$mpdf->Output('reporte_metas_tecnoparque.pdf', 'D'); // Forzar descarga
exit;