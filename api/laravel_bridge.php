<?php
/**
 * API Bridge para integración Laravel - Sistema Viáticos
 * Este archivo maneja la comunicación entre Laravel y el sistema de viáticos
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

// Configuración de headers para API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para responder con JSON
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Verificar método de petición
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método no permitido'], 405);
}

// Obtener datos JSON del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    jsonResponse(['error' => 'Datos JSON inválidos'], 400);
}

// Verificar acción requerida
if (!isset($input['action'])) {
    jsonResponse(['error' => 'Acción no especificada'], 400);
}

$user = new user();

switch ($input['action']) {
    
    case 'sync_user':
        // Sincronizar usuario desde Laravel
        if (!isset($input['user_id'], $input['name'], $input['email'], $input['password'])) {
            jsonResponse(['error' => 'Datos de usuario incompletos'], 400);
        }
        
        $resultado = $user->sincronizarDesdeUsers(
            $input['user_id'],
            $input['name'],
            $input['email'],
            $input['password']
        );
        
        jsonResponse($resultado);
        break;
    
    case 'verify_access':
        // Verificar si un usuario tiene acceso al sistema de viáticos
        if (!isset($input['email_or_doc'])) {
            jsonResponse(['error' => 'Email o documento no proporcionado'], 400);
        }
        
        $resultado = $user->verificarAccesoViaticos($input['email_or_doc']);
        jsonResponse($resultado);
        break;
    
    case 'login_check':
        // Verificar credenciales de login
        if (!isset($input['email_or_doc'], $input['password'])) {
            jsonResponse(['error' => 'Credenciales incompletas'], 400);
        }
        
        // Obtener usuario por email o documento
        $sql = "SELECT * FROM usuario WHERE email = :email_or_doc OR numero_documento = :email_or_doc";
        $conexion = (new Conexion())->obtenerConexion();
        $consult = $conexion->prepare($sql);
        $consult->bindParam(':email_or_doc', $input['email_or_doc'], PDO::PARAM_STR);
        $consult->execute();
        
        $result = $consult->fetch(PDO::FETCH_ASSOC);
        
        if ($result && password_verify($input['password'], $result['contraseña'])) {
            jsonResponse([
                'success' => true,
                'user' => [
                    'numero_documento' => $result['numero_documento'],
                    'nombre_completo' => $result['nombre_completo'],
                    'email' => $result['email'],
                    'id_rol' => $result['id_rol']
                ]
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Credenciales inválidas'], 401);
        }
        break;
    
    case 'get_user_info':
        // Obtener información de usuario
        if (!isset($input['email_or_doc'])) {
            jsonResponse(['error' => 'Email o documento no proporcionado'], 400);
        }
        
        $sql = "SELECT numero_documento, nombre_completo, email, telefono, id_rol 
                FROM usuario WHERE email = :email_or_doc OR numero_documento = :email_or_doc";
        $conexion = (new Conexion())->obtenerConexion();
        $consult = $conexion->prepare($sql);
        $consult->bindParam(':email_or_doc', $input['email_or_doc'], PDO::PARAM_STR);
        $consult->execute();
        
        $result = $consult->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            jsonResponse(['success' => true, 'user' => $result]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        break;
    
    default:
        jsonResponse(['error' => 'Acción no reconocida'], 400);
        break;
}
?>
