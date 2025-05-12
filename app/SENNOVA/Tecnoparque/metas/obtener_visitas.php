<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

header('Content-Type: application/json');

try {
    $metas = new metas_tecnoparque();
    $filtros = json_decode(file_get_contents('php://input'), true) ?? [];
    
    // Obtener datos filtrados
    $visitas = $metas->obtenerVisitasApre($filtros);
    
    // Calcular indicadores con los datos filtrados
    $indicadores = [
        'total_charlas' => count($visitas),
        'total_asistentes' => array_sum(array_column($visitas, 'numAsistentes')),
        'promedio_asistentes' => count($visitas) > 0 ? round(array_sum(array_column($visitas, 'numAsistentes')) / count($visitas)) : 0
    ];

    // Agrupar por encargado para el gráfico
    $asistentes_por_encargado = [];
    foreach ($visitas as $visita) {
        if (!isset($asistentes_por_encargado[$visita['encargado']])) {
            $asistentes_por_encargado[$visita['encargado']] = 0;
        }
        $asistentes_por_encargado[$visita['encargado']] += $visita['numAsistentes'];
    }

    $indicadores['encargados'] = array_keys($asistentes_por_encargado);
    $indicadores['asistentes_por_encargado'] = array_values($asistentes_por_encargado);

    echo json_encode([
        'success' => true,
        'data' => $visitas,
        'indicadores' => $indicadores
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
