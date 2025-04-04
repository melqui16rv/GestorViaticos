<?php
session_start();
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';


if (isset($_SESSION['id_rol'])) {
    $rol = $_SESSION['id_rol'];
} else {
    header("Location: " . "includes/session/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BANIN</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/index.css">
    <style>

    </style>
</head>
<body class="body-index">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    
    <div class="loading-overlay" id="loadingOverlay">Cargando...</div>
    <div class="pag1">

    <div id="logoutModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h2>Cerrar Sesión</h2>
                <p>¿Está seguro que desea cerrar la sesión?</p>
                <div class="modal-buttons">
                    <button id="cancelBtn" class="btn-cancel">Cancelar</button>
                    <button id="confirmBtn" class="btn-confirm">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

        <div class="contenedor">

                <div class="main-content">
                    <h1>Bienvenido a la plataforma de Gestion Viaticos</h1>
                    <div class="actions">
                        <?php if(isset($_SESSION['id_rol'])): ?>
                            <a href="<?php echo BASE_URL; ?>includes/session/salir.php" onclick="return salir()"><button class="login-button">Cerrar Sesión</button></a>
                            <button onclick="redirigirSegunRol()" class="btn-entrar">
                                Entrar
                            </button>
                    
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

<script src="<?php echo BASE_URL; ?>assets/js/share/modalSalir.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/animaCarga.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/index/fun1.js"></script>

</body>
</html>
