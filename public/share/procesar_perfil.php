<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/metodos_perfil.php';

// Configurar respuesta JSON
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['numero_documento'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida. Por favor, inicie sesión nuevamente.'
    ]);
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit();
}

// Verificar acción
if (!isset($_POST['action']) || $_POST['action'] !== 'actualizar_perfil') {
    echo json_encode([
        'success' => false,
        'message' => 'Acción no válida'
    ]);
    exit();
}

try {
    $metodosPerfilUsuario = new metodosPerfilUsuario();
    
    // Obtener datos del formulario
    $numero_documento_actual = $_SESSION['numero_documento'];
    $nuevo_numero_documento = trim($_POST['numero_documento'] ?? '');
    $tipo_doc = trim($_POST['tipo_doc'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    
    // Validaciones básicas
    if (empty($nuevo_numero_documento) || empty($tipo_doc) || empty($telefono)) {
        echo json_encode([
            'success' => false,
            'message' => 'Todos los campos son obligatorios'
        ]);
        exit();
    }
    
    // Validar formato del número de documento
    $validacionNumero = $metodosPerfilUsuario->validarNumeroDocumento($nuevo_numero_documento);
    if (!$validacionNumero['success']) {
        echo json_encode($validacionNumero);
        exit();
    }
    
    // Validar formato del teléfono
    $validacionTelefono = $metodosPerfilUsuario->validarTelefono($telefono);
    if (!$validacionTelefono['success']) {
        echo json_encode($validacionTelefono);
        exit();
    }
    
    // Validar tipo de documento
    $tiposValidos = array_keys($metodosPerfilUsuario->obtenerTiposDocumento());
    if (!in_array($tipo_doc, $tiposValidos)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tipo de documento no válido'
        ]);
        exit();
    }
    
    // Actualizar perfil
    $resultado = $metodosPerfilUsuario->actualizarPerfilUsuario(
        $numero_documento_actual,
        $nuevo_numero_documento,
        $tipo_doc,
        $telefono
    );
    
    if ($resultado['success']) {
        // Verificar si cambió el número de documento para indicar recarga de página
        $numero_documento_cambio = ($numero_documento_actual !== $nuevo_numero_documento);
        
        echo json_encode([
            'success' => true,
            'message' => $resultado['message'],
            'numero_documento_cambio' => $numero_documento_cambio
        ]);
    } else {
        echo json_encode($resultado);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor. Por favor, intente nuevamente.'
    ]);
}
?>
