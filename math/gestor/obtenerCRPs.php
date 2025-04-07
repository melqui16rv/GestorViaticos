<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/crpAsociados.php';

// Habilitar logs de error para depuración
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$gestor = new gestor1();

$codigoCDP = isset($_GET['codigo_cdp']) ? trim($_GET['codigo_cdp']) : '';

// Log del CDP recibido
error_log("CDP recibido en obtenerCRPs.php: '$codigoCDP'");

if (empty($codigoCDP)) {
    error_log("No se recibió código CDP");
    echo json_encode([]);
    exit;
}
// Consultamos CRPs asociados al CDP
$crps = $gestor->obtenerCRPsPorCDP($codigoCDP);

// Log del resultado
error_log("Número de CRPs encontrados: " . count($crps));

// Retornamos en formato JSON
echo json_encode($crps);