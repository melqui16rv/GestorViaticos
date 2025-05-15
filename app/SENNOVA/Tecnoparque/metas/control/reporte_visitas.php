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
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .dashboard-container { padding: 1rem; margin-bottom: 2rem; background: #f8fafc; border-radius: 12px; }
        .stats-card, .tabla-card, .chart-wrapper { margin-bottom: 1rem; }
        .stat-item { display: inline-block; min-width: 120px; margin-right: 1rem; }
        .styled-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .styled-table th, .styled-table td { border: 1px solid #ccc; padding: 6px; }
        .styled-table th { background: #2563eb; color: #fff; }
        h2 { color: #2563eb; margin-top: 2rem; }
        .torta-title { font-weight: bold; margin-top: 1rem; }
        .torta-info { font-size: 0.95em; color: #555; }
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
    <section>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/Asesorarmiento.php'; ?>
    </section>
    <section>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/Tecnoparque/metas/ProyectosExt.php'; ?>
    </section>
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