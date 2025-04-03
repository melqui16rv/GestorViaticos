<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';

// Solo iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destruir la sesión correctamente
$_SESSION = []; // Limpiar todas las variables de sesión
session_unset();
session_destroy();

// Redirigir sin errores
header('Location: ' . BASE_URL . 'index.php');
exit(); // Asegura que el script se detenga después de la redirección
?>
