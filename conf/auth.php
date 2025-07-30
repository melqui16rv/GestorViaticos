<?php
// Incluir config.php antes de cualquier otro códig
require_once '/home/appscide/public_html/viaticosApp/conf/auth.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['id_rol']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('/home/appscide/public_html/viaticosApp/includes/session/login.php');
        exit();
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION['id_rol'], $roles)) {
        header('/home/appscide/public_html/viaticosApp/includes/session/login.php');
        exit();
    }
}

function requireNotRole($roles) {
    if (isset($_SESSION['role'])) {
        $userRole = $_SESSION['role'];
        if (in_array($userRole, $roles)) {
            header('/home/appscide/public_html/viaticosApp/index.php');
            exit();
        }
    }
}
?>
