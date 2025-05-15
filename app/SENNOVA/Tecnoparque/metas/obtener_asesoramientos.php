<?php
// Asegurar que la sesión esté iniciada para que $_SESSION['id_rol'] esté disponible
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $filtros = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    // Filtros opcionales para asesoramiento
    $filtros = [
        'tipo' => !empty($filtros['tipo']) ? trim($filtros['tipo']) : null,
        'encargado' => !empty($filtros['encargado']) ? trim($filtros['encargado']) : null,
        'orden' => isset($filtros['orden']) ? $filtros['orden'] : 'DESC',
        'limite' => !empty($filtros['limite']) ? intval($filtros['limite']) : null
    ];

    $metas = new metas_tecnoparque();
    $asesoramientos = $metas->obtenerAsesoramientos($filtros);

    // Indicadores
    $indicadores = [
        'total' => count($asesoramientos),
        'por_tipo' => [],
        'por_encargado' => []
    ];
    foreach ($asesoramientos as $a) {
        $tipo = $a['tipo'];
        $encargado = $a['encargadoAsesoramiento'];
        $indicadores['por_tipo'][$tipo] = ($indicadores['por_tipo'][$tipo] ?? 0) + 1;
        $indicadores['por_encargado'][$encargado] = ($indicadores['por_encargado'][$encargado] ?? 0) + 1;
    }

    $response = [
        'success' => true,
        'data' => array_map(function($a) {
            return [
                'id_asesoramiendo' => $a['id_asesoramiendo'],
                'tipo' => $a['tipo'],
                'encargadoAsesoramiento' => $a['encargadoAsesoramiento'],
                'nombreEntidadImpacto' => $a['nombreEntidadImpacto'],
                'fechaAsesoramiento' => $a['fechaAsesoramiento']
            ];
        }, $asesoramientos),
        'indicadores' => $indicadores
    ];
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
