<?php
$_SERVER['DOCUMENT_ROOT'] = '/Users/melquiromero/Documents/GitHub/viaticosApp';

define('BASE_URL', '/'); // Ajusta según sea necesario

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/auth.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
