<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/metodos_solicitud.php';
header('Content-Type: application/json');

$miClase = new solicitudRol();
$usuario = null;
if (isset($_SESSION['numero_documento'])) {
    $usuario = $miClase->obtenerDatosUsuarioLogueado($_SESSION['numero_documento']);
    if ($usuario && is_array($usuario)) {
        $usuario = $usuario[0];
    }
}

if (!$usuario) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida.']);
    exit;
}

// Verificar que el usuario sea administrador
if (!$miClase->esAdministrador($usuario['numero_documento'])) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'aceptar_solicitud':
        $id_solicitud = $_POST['id_solicitud'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        $ok = $miClase->aceptarSolicitud($id_solicitud, $usuario['numero_documento'], $observaciones);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Solicitud aceptada correctamente. El rol del usuario ha sido actualizado.' : 'No se pudo aceptar la solicitud.'
        ]);
        exit;
        
    case 'rechazar_solicitud':
        $id_solicitud = $_POST['id_solicitud'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        $ok = $miClase->rechazarSolicitud($id_solicitud, $usuario['numero_documento'], $observaciones);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Solicitud rechazada correctamente.' : 'No se pudo rechazar la solicitud.'
        ]);
        exit;
        
    case 'obtener_solicitudes':
        $estado = $_POST['estado'] ?? null;
        $fecha_inicio = $_POST['fecha_inicio'] ?? null;
        $fecha_fin = $_POST['fecha_fin'] ?? null;
        
        $solicitudes = $miClase->obtenerTodasSolicitudes($estado, $fecha_inicio, $fecha_fin);
        echo json_encode([
            'success' => true,
            'data' => $solicitudes
        ]);
        exit;
        
    case 'obtener_resumen':
        $resumen = $miClase->obtenerResumenSolicitudes();
        echo json_encode([
            'success' => true,
            'data' => $resumen
        ]);
        exit;
        
    case 'obtener_solicitud_detalle':
        $id_solicitud = $_POST['id_solicitud'] ?? '';
        
        $solicitud = $miClase->obtenerSolicitudCompleta($id_solicitud);
        echo json_encode([
            'success' => $solicitud ? true : false,
            'data' => $solicitud
        ]);
        exit;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        exit;
}
