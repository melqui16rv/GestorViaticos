<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/planeacion/metodosGestor.php';

requireRole(['3']);
header('Content-Type: application/json');

try {
    $gestor = new planeacion();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'buscarOP' || $action === 'cargarMas') {
        $filtros = [
            'numeroDocumento' => $_GET['numeroDocumento'] ?? '',
            'estado' => $_GET['estado'] ?? 'Todos',
            'beneficiario' => $_GET['beneficiario'] ?? '',
            'mes' => $_GET['mes'] ?? '',
            'fechaInicio' => $_GET['fechaInicio'] ?? '',
            'fechaFin' => $_GET['fechaFin'] ?? ''
        ];
        
        // Debug: registrar los filtros recibidos
        error_log("Filtros recibidos: " . json_encode($filtros));
        error_log("Action: " . $action);
        
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        
        error_log("Limit: $limit, Offset: $offset");
        
        $resultado = $gestor->obtenerOP($filtros, $limit, $offset);
        error_log("Registros encontrados: " . count($resultado));
        
        echo json_encode($resultado);
    } elseif ($action === 'cargarMasCDP') {
        $numeroDocumento = $_GET['numeroDocumento'] ?? '';
        $fuente = $_GET['fuente'] ?? 'Todos';
        $reintegros = $_GET['reintegros'] ?? 'Todos';
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

        $resultado = $gestor->obtenerCDP($numeroDocumento, $fuente, $reintegros, $limit, $offset);
        echo json_encode($resultado); // Enviar solo los datos sin estructura adicional
    } else {
        throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>