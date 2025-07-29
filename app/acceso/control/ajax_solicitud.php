<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
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

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        // Verificar si ya tiene una solicitud pendiente
        $tiene_pendiente = $miClase->tieneSolicitudPendiente($usuario['numero_documento']);
        if ($tiene_pendiente) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya tiene una solicitud en estado "enviada". Tiene dos opciones: puede eliminar la solicitud existente para crear una nueva, o esperar a que el administrador responda su solicitud actual.',
                'tiene_pendiente' => true
            ]);
            exit;
        }
        
        $numero_documento_original = $usuario['numero_documento']; // Número original de la sesión
        $numero_documento_nuevo = $_POST['numero_documento'] ?? $numero_documento_original; // Nuevo número del formulario
        $email = $usuario['email'];
        $id_rol_solicitado = $_POST['id_rol_solicitado'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        $tipo_doc = $_POST['tipo_doc'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $fecha_solicitud = date('Y-m-d H:i:s');
        
        // Validar campos requeridos
        if (empty($id_rol_solicitado) || empty($tipo_doc) || empty($telefono)) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios.'
            ]);
            exit;
        }
        
        // Usar el método que actualiza perfil y envía solicitud
        $ok = $miClase->enviarSolicitudConPerfil($numero_documento_nuevo, $numero_documento_original, $email, $id_rol_solicitado, $motivo, $fecha_solicitud, $tipo_doc, $telefono);
        
        // Si se actualizó el número de documento, actualizar la sesión
        if ($ok && $numero_documento_nuevo !== $numero_documento_original) {
            $_SESSION['numero_documento'] = $numero_documento_nuevo;
        }
        
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Tu solicitud ha sido enviada al administrador.' : 'No se pudo enviar la solicitud.'
        ]);
        exit;
    case 'edit':
        $id_solicitud = $_POST['id_solicitud'] ?? '';
        $numero_documento_original = $usuario['numero_documento']; // Número original de la sesión
        $numero_documento_nuevo = $_POST['numero_documento'] ?? $numero_documento_original; // Nuevo número del formulario
        $id_rol_solicitado = $_POST['id_rol_solicitado'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        $tipo_doc = $_POST['tipo_doc'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        
        // Validar campos requeridos
        if (empty($id_solicitud) || empty($id_rol_solicitado) || empty($tipo_doc) || empty($telefono)) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios.'
            ]);
            exit;
        }
        
        // Usar el método que actualiza perfil y edita solicitud
        $ok = $miClase->editarSolicitudConPerfil($id_solicitud, $numero_documento_nuevo, $numero_documento_original, $id_rol_solicitado, $motivo, $tipo_doc, $telefono);
        
        // Si se actualizó el número de documento, actualizar la sesión
        if ($ok && $numero_documento_nuevo !== $numero_documento_original) {
            $_SESSION['numero_documento'] = $numero_documento_nuevo;
        }
        
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'La solicitud ha sido actualizada.' : 'No se pudo actualizar la solicitud.'
        ]);
        exit;
    case 'delete':
        $id_solicitud = $_POST['id_solicitud'] ?? '';
        $ok = $miClase->eliminarSolicitud($id_solicitud, $usuario['numero_documento']);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'La solicitud ha sido eliminada.' : 'No se pudo eliminar la solicitud.'
        ]);
        exit;
    case 'verificarSolicitudPendiente':
        $tiene_pendiente = $miClase->tieneSolicitudPendiente($usuario['numero_documento']);
        echo json_encode([
            'success' => true,
            'tiene_pendiente' => $tiene_pendiente
        ]);
        exit;
    case 'validarAntesDeCrear':
        $tiene_pendiente = $miClase->tieneSolicitudPendiente($usuario['numero_documento']);
        if ($tiene_pendiente) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya tiene una solicitud en estado "enviada". Tiene dos opciones: puede eliminar la solicitud existente para crear una nueva, o esperar a que el administrador responda su solicitud actual.',
                'tiene_pendiente' => true
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Puede crear una nueva solicitud.',
                'tiene_pendiente' => false
            ]);
        }
        exit;
    case 'obtenerMisSolicitudes':
        $solicitudes = $miClase->obtenerSolicitudesPorUsuario($usuario['numero_documento']);
        echo json_encode([
            'success' => true,
            'data' => $solicitudes
        ]);
        exit;
    case 'obtenerTodasSolicitudes':
        // Solo para administradores (rol 1 o 6)
        if (isset($usuario['id_rol']) && in_array($usuario['id_rol'], [1, 6])) {
            $solicitudes = $miClase->obtenerTodasSolicitudes();
            echo json_encode([
                'success' => true,
                'data' => $solicitudes
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No tiene permisos para esta acción.'
            ]);
        }
        exit;
    case 'aprobar':
        // Solo para administradores
        if (isset($usuario['id_rol']) && in_array($usuario['id_rol'], [1, 6])) {
            $id_solicitud = $_POST['id_solicitud'] ?? '';
            $ok = $miClase->aceptarSolicitud($id_solicitud, $usuario['numero_documento']);
            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Solicitud aprobada exitosamente.' : 'No se pudo aprobar la solicitud.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No tiene permisos para esta acción.'
            ]);
        }
        exit;
    case 'rechazar':
        // Solo para administradores
        if (isset($usuario['id_rol']) && in_array($usuario['id_rol'], [1, 6])) {
            $id_solicitud = $_POST['id_solicitud'] ?? '';
            $motivo_rechazo = $_POST['motivo_rechazo'] ?? '';
            $ok = $miClase->rechazarSolicitud($id_solicitud, $usuario['numero_documento'], $motivo_rechazo);
            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Solicitud rechazada exitosamente.' : 'No se pudo rechazar la solicitud.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No tiene permisos para esta acción.'
            ]);
        }
        exit;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        exit;
}
