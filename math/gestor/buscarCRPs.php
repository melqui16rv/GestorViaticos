<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/crpAsociados.php';

$gestor = new gestor1();
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$crps = $gestor->buscarCRPsGlobal($searchTerm);
echo json_encode($crps);
