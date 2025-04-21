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
            'numeroDocumento' => isset($_GET['numeroDocumento']) ? trim($_GET['numeroDocumento']) : '',
            'estado' => isset($_GET['estado']) ? trim($_GET['estado']) : 'Todos',
            'beneficiario' => isset($_GET['beneficiario']) ? trim($_GET['beneficiario']) : '',
            'mes' => isset($_GET['mes']) ? trim($_GET['mes']) : '',
            'fechaInicio' => isset($_GET['fechaInicio']) ? trim($_GET['fechaInicio']) : '',
            'fechaFin' => isset($_GET['fechaFin']) ? trim($_GET['fechaFin']) : ''
        ];
        
        // Validar y sanitizar los filtros
        $filtros = array_map('htmlspecialchars', $filtros);
        
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        
        $resultado = $gestor->obtenerOP($filtros, $limit, $offset);
        echo json_encode($resultado); // Enviar solo los datos sin estructura adicional
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