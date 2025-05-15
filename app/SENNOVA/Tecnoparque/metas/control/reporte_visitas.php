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
    <!-- CSS principales para que las vistas se vean bien en el PDF -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard_content.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/metas.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/proyecTecStyle.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/asesoramientoStyle.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/visApreStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .dashboard-container { padding: 0.5rem 1rem; margin: 0 auto 1.5rem auto; background: #f8fafc; border-radius: 8px; max-width: 1000px; }
        .stats-card, .tabla-card, .chart-wrapper { margin-bottom: 0.8rem; }
        .stat-item { display: inline-block; min-width: 100px; margin-right: 0.5rem; }
        .styled-table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; font-size: 10.5px; }
        .styled-table th, .styled-table td { border: 1px solid #bbb; padding: 4px 6px; }
        .styled-table th { background: #2563eb; color: #fff; }
        h1 { color: #2b3b4f; font-size: 1.5em; margin-bottom: 0.5em; }
        h2 { color: #2563eb; margin-top: 1.2em; font-size: 1.15em; }
        .torta-title { font-weight: bold; margin-top: 0.5em; }
        .torta-info { font-size: 0.95em; color: #555; }
        .section-break { page-break-before: always; }
        /* Ocultar botones, formularios y elementos interactivos que no tienen sentido en PDF */
        button, .actualizar-tabla-link, form, .sidebar-filament, .sidebar-link, .sidebar-toggle-btn, .sidebar-overlay { display: none !important; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Reporte de Metas Tecnoparque</h1>
    <hr>
    <section>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosTec.php'; ?>
    </section>
    <div class="section-break"></div>
    <section>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/Asesorarmiento.php'; ?>
    </section>
    <div class="section-break"></div>
    <section>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosExt.php'; ?>
    </section>
    <div class="section-break"></div>
    <section>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/VisitasApre.php'; ?>
    </section>
</body>
</html>
<?php
$html = ob_get_clean();

// Quitar scripts JS y recursos innecesarios para el PDF
$html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);

// Limpiar cualquier salida previa antes de enviar headers y PDF
if (ob_get_length()) ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
$mpdf->WriteHTML($html);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_metas_tecnoparque.pdf"');
$mpdf->Output('reporte_metas_tecnoparque.pdf', 'D'); // Forzar descarga
exit;