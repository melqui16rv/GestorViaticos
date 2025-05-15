<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/metodosGestor.php';

requireRole(['4']);
header('Content-Type: application/json');

// --- Quitar ob_clean y ob_end_clean para evitar problemas de buffer ---
// ob_clean();

try {
    $gestor = new sennova_general_presuspuestal();
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    // --- Log temporal para depuración ---
    // file_put_contents('/tmp/ajaxgestor.log', "action: $action\n", FILE_APPEND);

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

        // --- Siempre retornar JSON válido ---
        echo json_encode($resultado);
        exit;
    } else {
        echo json_encode(['error' => 'Acción no válida']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>