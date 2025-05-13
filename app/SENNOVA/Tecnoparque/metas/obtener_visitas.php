<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

header('Content-Type: application/json');

try {
    // Obtener y validar datos de entrada
    $input = file_get_contents('php://input');
    error_log("Datos recibidos: " . $input); // Debug

    $filtros = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    // Validar y limpiar filtros
    $filtros = [
        'orden' => isset($filtros['orden']) ? $filtros['orden'] : 'DESC',
        'limite' => !empty($filtros['limite']) ? intval($filtros['limite']) : null,
        'encargado' => !empty($filtros['encargado']) ? trim($filtros['encargado']) : null,
        'mes' => !empty($filtros['mes']) ? intval($filtros['mes']) : null,
        'anio' => !empty($filtros['anio']) ? intval($filtros['anio']) : null
    ];

    error_log("Filtros procesados: " . print_r($filtros, true)); // Debug

    $metas = new metas_tecnoparque();
    $visitas = $metas->obtenerVisitasApre($filtros);
    
    // Si no hay visitas, devolver array vacío pero con success
    if (empty($visitas)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'indicadores' => [
                'total_charlas' => 0,
                'total_asistentes' => 0,
                'promedio_asistentes' => 0,
                'encargados' => [],
                'asistentes_por_encargado' => [],
                'visitas_por_encargado_labels' => [],
                'visitas_por_encargado_data' => []
            ]
        ]);
        exit;
    }

    // Calcular indicadores con los datos filtrados
    $indicadores = [
        'total_charlas' => count($visitas),
        'total_asistentes' => array_sum(array_column($visitas, 'numAsistentes')),
        'promedio_asistentes' => count($visitas) > 0 ? 
            round(array_sum(array_column($visitas, 'numAsistentes')) / count($visitas)) : 0
    ];

    // Agrupar por encargado para el gráfico
    $asistentes_por_encargado = [];
    foreach ($visitas as $visita) {
        if (!isset($asistentes_por_encargado[$visita['encargado']])) {
            $asistentes_por_encargado[$visita['encargado']] = 0;
        }
        $asistentes_por_encargado[$visita['encargado']] += intval($visita['numAsistentes']);
    }

    $indicadores['encargados'] = array_keys($asistentes_por_encargado);
    $indicadores['asistentes_por_encargado'] = array_values($asistentes_por_encargado);

    // $visitasFiltradas contiene solo los registros filtrados
    $visitasPorEncargado = [];
    foreach ($visitas as $v) {
        $encargado = $v['encargado'];
        if (!isset($visitasPorEncargado[$encargado])) {
            $visitasPorEncargado[$encargado] = 0;
        }
        $visitasPorEncargado[$encargado]++;
    }
    $indicadores['visitas_por_encargado_labels'] = array_keys($visitasPorEncargado);
    $indicadores['visitas_por_encargado_data'] = array_values($visitasPorEncargado);

    // Preparar respuesta
    $response = [
        'success' => true,
        'data' => array_map(function($visita) {
            return [
                'id_visita' => $visita['id_visita'],
                'encargado' => $visita['encargado'],
                'numAsistentes' => intval($visita['numAsistentes']),
                'fechaCharla' => $visita['fechaCharla'],
            ];
        }, $visitas),
        'indicadores' => $indicadores
    ];

    error_log("Respuesta: " . print_r($response, true)); // Debug
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error en obtener_visitas.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
