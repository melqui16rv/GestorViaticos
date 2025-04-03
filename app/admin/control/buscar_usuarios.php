<?php
require_once '../../sql/class.php';
require_once '../../app/config.php';

header('Content-Type: application/json');

$trabajo = new user();
$termino = isset($_GET['termino']) ? $_GET['termino'] : '';

$resultados = $trabajo->buscar_usuario($termino);

echo json_encode($resultados);
exit;