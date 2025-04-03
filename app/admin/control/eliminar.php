<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

header('Content-Type: application/json');

requireRole(['1']);
$dato = new user();
if (isset($_GET['numero'])) {
    $numero = $_GET['numero'];
    $resultado = $dato->eliminarUsuario($numero);
    if (is_array($resultado) && isset($resultado['success'])) {
        echo json_encode($resultado);
        exit; // Detener el script después de enviar la respuesta
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar el usuario.", "error" => "Resultado inesperado."]);
        exit; // Detener el script después de enviar la respuesta
    }
} else {
    echo json_encode(["success" => false, "message" => "Parámetro número faltante"]);
    exit; // Detener el script después de enviar la respuesta
}