<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/metodosGestor.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/crpAsociados.php';

requireRole(['2']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $fechaInicio = $_POST['fecha_inicio'] ?? null;
    $fechaFin = $_POST['fecha_fin'] ?? null;
    $fechaPago = $_POST['fecha_pago'] ?? null;
    $saldoAsignado = $_POST['saldo_asignado'] ?? null;
    $codigoCDP = $_POST['codigo_cdp'] ?? null;
    $codigoCRP = $_POST['codigo_crp'] ?? null;

    // Validaciones
    if (!$nombre || !$documento || !$fechaInicio || !$fechaFin || !$saldoAsignado || !$codigoCDP || !$codigoCRP) {
        die('Error: Faltan campos obligatorios.');
    }

    $fechaPago = empty($fechaPago) ? null : $fechaPago;

    $gestor = new gestor1();
    $resultado = $gestor->insertarSaldoAsignado(
        $nombre,
        $documento,
        $fechaInicio,
        $fechaFin,
        $fechaPago,
        $saldoAsignado,
        $codigoCDP,
        $codigoCRP
    );

    if ($resultado) {
        // Mostrar modal nativo y redirigir a index.php
        echo '<script>
            alert("¡Registro exitoso! La asignación del viático se ha guardado correctamente.");
            window.location.href = BASE_URL . "app/gestor/index.php";
        </script>';
        exit;
    } else {
        // Mostrar error simple (puedes reemplazar esto por un modal si lo deseas)
        echo '<script>
            alert("Error al registrar la asignación de viático. Intente nuevamente.");
            window.history.back();
        </script>';
        exit;
    }
} else {
    die('Acceso no autorizado.');
}