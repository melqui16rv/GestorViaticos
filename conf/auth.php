<?php
// auth.php - Funciones de autenticación

function isLoggedIn() {
    return isset($_SESSION['id_rol']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'includes/session/login.php');
        exit();
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION['id_rol'], $roles)) {
        header('Location: ' . BASE_URL . 'includes/session/login.php');
        exit();
    }
}

function requireNotRole($roles) {
    if (isset($_SESSION['role'])) {
        $userRole = $_SESSION['role'];
        if (in_array($userRole, $roles)) {
            header('Location: ' . BASE_URL . 'index.php');
            exit();
        }
    }
}
?>
