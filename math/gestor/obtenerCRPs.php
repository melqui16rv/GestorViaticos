<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/crpAsociados.php';

$gestor = new gestor1();

$codigoCDP = isset($_GET['codigo_cdp']) ? $_GET['codigo_cdp'] : '';

// Consultamos CRPs asociados al CDP
$crps = $gestor->obtenerCRPsPorCDP($codigoCDP);

// Retornamos en formato JSON
echo json_encode($crps);