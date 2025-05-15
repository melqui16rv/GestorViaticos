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
            font-size: 12.5px;
            margin: 0;
            padding: 0;
            background: #f4f7fa;
            color: #232946;
        }
        .pdf-container {
            margin: 0 auto 2rem auto;
            background: #fff;
            border-radius: 14px;
            max-width: 950px;
            min-width: 650px;
            /* Márgenes reducidos arriba y abajo */
            padding: 1rem 2.5rem 1rem 2.5rem;
            box-shadow: 0 4px 24px rgba(37,99,235,0.10);
        }
        h1, h2, h3 {
            color: #1e293b;
            margin-bottom: 0.3em;
        }
        h1 {
            text-align: center;
            font-size: 2.3em;
            margin-bottom: 0.2em;
            letter-spacing: 1px;
            font-weight: 700;
        }
        h2 {
            font-size: 1.35em;
            margin-top: 2em;
            margin-bottom: 0.7em;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 0.2em;
            font-weight: 600;
        }
        h3 {
            font-size: 1.08em;
            margin-top: 1.2em;
            margin-bottom: 0.3em;
            color: #2563eb;
            font-weight: 600;
        }
        .indicadores {
            display: flex;
            flex-wrap: wrap;
            gap: 2em;
            margin: 1.2em 0 1.5em 0;
        }
        .indicador {
            background: linear-gradient(120deg, #e0e7ff 60%, #f1f5f9 100%);
            border-radius: 10px;
            padding: 1.1em 1.7em;
            min-width: 160px;
            box-shadow: 0 1px 8px rgba(37,99,235,0.07);
            text-align: center;
            border: 1.5px solid #dbeafe;
            position: relative;
        }
        .indicador h3 {
            margin: 0 0 0.3em 0;
            font-size: 1em;
            color: #2563eb;
            font-weight: 700;
        }
        .indicador p {
            font-size: 1.35em;
            font-weight: bold;
            margin: 0;
            color: #232946;
        }
        .badge {
            display: inline-block;
            padding: 0.2em 0.7em;
            font-size: 0.95em;
            border-radius: 12px;
            background: #2563eb;
            color: #fff;
            margin-left: 0.5em;
            font-weight: 600;
        }
        .section-break {
            /* page-break-before: always; */
            margin-top: 2em;
        }
        .styled-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.2em;
            font-size: 12px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(37,99,235,0.07);
        }
        .styled-table th, .styled-table td {
            border: 1px solid #dbeafe;
            padding: 9px 12px;
            text-align: center;
        }
        .styled-table th {
            background: linear-gradient(90deg, #2563eb 80%, #60a5fa 100%);
            color: #fff;
            font-size: 1em;
            font-weight: 700;
            border-bottom: 2px solid #1e40af;
        }
        .styled-table tr:nth-child(even) td {
            background: #f1f5f9;
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
            border: 2px solid #dbeafe;
            border-radius: 10px;
            background: #f8fafc;
            padding: 0.7em;
            box-shadow: 0 2px 8px rgba(37,99,235,0.07);
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
            border-radius: 10px;
            padding: 1em 1.2em;
            min-width: 220px;
            max-width: 260px;
            flex: 1 1 220px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(37,99,235,0.07);
            border: 1.5px solid #dbeafe;
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
        .subtle {
            color: #64748b;
            font-size: 0.97em;
            margin-bottom: 0.5em;
        }
        .highlight {
            color: #059669;
            font-weight: bold;
        }
        .highlight-yellow {
            color: #eab308;
            font-weight: bold;
        }
        .highlight-blue {
            color: #2563eb;
            font-weight: bold;
        }
        .highlight-red {
            color: #b91c1c;
            font-weight: bold;
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
            <h2>Proyectos de Base Tecnológica <span class="badge">Meta: 100</span></h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/pdf/ProyectosTec.php';
            ?>
        </section>

        <div class="section-break"></div>

        <!-- Asesoramiento -->
        <section>
            <h2>Asesoramiento</h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/pdf/Asesorarmiento.php';
            ?>
        </section>

        <div class="section-break"></div>

        <!-- Proyectos de Extensionismo -->
        <section>
            <h2>Proyectos de Extensionismo <span class="badge highlight-yellow">Meta: 5</span></h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/pdf/ProyectosExt.php';
            ?>
        </section>

        <div class="section-break"></div>

        <!-- Visitas de Aprendices -->
        <section>
            <h2>Visitas de Aprendices</h2>
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/pdf/VisitasApre.php';
            ?>
        </section>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();
// Quitar scripts JS y recursos innecesarios para el PDF
$html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
// Quitar links de estilos externos
$html = preg_replace('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', '', $html);
// Eliminar saltos de página forzados en el HTML generado
$html = preg_replace('/page-break-before\s*:\s*always;?/i', '', $html);
if (ob_get_length()) ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter']);
$mpdf->WriteHTML($html);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_metas_tecnoparque.pdf"');
$mpdf->Output('reporte_metas_tecnoparque.pdf', 'D');
exit;