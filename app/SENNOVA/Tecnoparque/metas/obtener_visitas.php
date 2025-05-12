<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

header('Content-Type: application/json');

$metas = new metas_tecnoparque();
$filtros = json_decode(file_get_contents('php://input'), true);

$visitas = $metas->obtenerVisitasApre($filtros);
echo json_encode($visitas);
