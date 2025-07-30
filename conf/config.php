<?php
// Iniciar sesión ANTES de cualquier otra cosa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SERVER['DOCUMENT_ROOT'] = '/home/appscide/public_html/viaticosApp';

// Definir BASE_URL
define('BASE_URL', '/viaticosApp/');

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/auth.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
