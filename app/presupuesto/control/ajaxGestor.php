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
        
        // Modificar el manejo del límite
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        if ($limit === 'todos') {
            $limit = PHP_INT_MAX;
        } else {
            $limit = (int)$limit;
        }
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        
        $resultado = $gestor->obtenerOP($filtros, $limit, $offset);
        
        // Asegurar que la respuesta sea consistente
        if (empty($resultado)) {
            echo json_encode([]);
        } else {
            echo json_encode($resultado);
        }
    } elseif ($action === 'cargarMasCDP') {
        $numeroDocumento = $_GET['numeroDocumento'] ?? '';
        $fuente = $_GET['fuente'] ?? 'Todos';
        $reintegros = $_GET['reintegros'] ?? 'Todos';
        
        // Modificar el manejo del límite para la opción "todos"
        $limit = isset($_GET['limit']) ? (
            $_GET['limit'] === 'todos' ? PHP_INT_MAX : intval($_GET['limit'])
        ) : 10;
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

        $resultado = $gestor->obtenerCDP($numeroDocumento, $fuente, $reintegros, $limit, $offset);
        
        // Asegurar que siempre devolvemos un array
        echo json_encode($resultado ?: []);
    } else {
        throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
