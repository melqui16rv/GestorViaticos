<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/metodosGestor.php';

requireRole(['4']);
header('Content-Type: application/json');

// Elimina cualquier salida previa
ob_clean();

try {
    $gestor = new sennova_general_presuspuestal();
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
        
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        
        $resultado = $gestor->obtenerOP($filtros, $limit, $offset);

        $dependenciasPermitidas = ['62', '66', '69', '70'];
        $resultado = array_values(array_filter($resultado, function($row) use ($dependenciasPermitidas) {
            $dep = null;
            if (isset($row['Dependencia'])) {
                $dep = $row['Dependencia'];
            } elseif (isset($row['dependencia'])) {
                $dep = $row['dependencia'];
            } else {
                return false;
            }
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($dep), $matches)) {
                return in_array($matches[1], $dependenciasPermitidas);
            }
            return false;
        }));

        // Limpia el buffer de salida antes de enviar JSON
        if (ob_get_length()) ob_end_clean();
        echo json_encode($resultado);
        exit;
    } else {
        if (ob_get_length()) ob_end_clean();
        echo json_encode(['error' => 'Acción no válida']);
        exit;
    }
} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>