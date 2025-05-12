<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

try {
    $metas = new metas_tecnoparque();
    $filtros = json_decode(file_get_contents('php://input'), true);
    
    // Obtener datos filtrados
    $visitas = $metas->obtenerVisitasApre($filtros);
    
    // Obtener indicadores basados en los datos filtrados
    $indicadores = $metas->obtenerIndicadoresVisitasFiltradas($visitas);
    
    echo json_encode([
        'success' => true,
        'data' => $visitas,
        'indicadores' => $indicadores
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los datos: ' . $e->getMessage()
    ]);
}
