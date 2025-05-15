<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';


if (isset($_GET['num_doc'])) {
    $numero_documento = $_GET['num_doc'];
    $result = new user();
    $datos_instructor = $result->obtenerDatosEstructuradosPorNumeroDocumento($numero_documento);
    if (empty($datos_instructor)) {
        $datos_instructor = null;
    }
} else {
    $datos_instructor = null;
}

if (isset($_SESSION['numero_documento'])) {
    $id = $_SESSION['numero_documento'];
    $usuario_result = new user();
    $datos_usuario = $usuario_result->obtenerDatosUsuarioLogueado($id);

    if (!empty($datos_usuario)) {
        $nombreUser = $datos_usuario[0]['nombre_completo'];
    } else {
        $nombreUser = 'Nombre no disponible';
    }
} else {
    $nombreUser = null;
}
?>
<html lang="es" style="--nav-height: 70px;scrollbar-width: none; /* Oculta la barra en Firefox */-ms-overflow-style: none;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/nav.css.css">
    <title>Viaticos</title>
</head>
<body>
    <div id="logoutModal" class="modal-overlayyy">
        <div class="modal-containerrr">
            <div class="modal-contentttt">
                <div class="modal-iconnn">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h2>Cerrar Sesión</h2>
                <p>¿Está seguro que desea cerrar la sesión?</p>
                <div class="modal-buttonsss">
                    <button id="cancelBtn" class="btn-cancelll">Cancelar</button>
                    <button id="confirmBtn" class="btn-confirmmm">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar">
        <!-- Contenedor de los h2 alineados a la derecha -->
        <div class="navbar-right">
            <?php if (isset($_SESSION['id_rol']) && $nombreUser !== null): ?>
                <h2 class="indicadorRol">
                    <?php 
                        switch ($_SESSION['id_rol']) {
                            case '2':
                                echo "Gestor ";
                                break;
                            case '3':
                                echo "Presupuesto ";
                                break;
                            case '1':
                                echo "Admin ";
                                break;
                            case '4':
                                echo "SENNOVA ";
                                break;
                            case '5':
                                echo "Tecnoparque ";
                                break;
                            case '6':
                                echo "Tecnoacademia ";
                                break;
                            default:
                                echo "";
                        }
                        echo ' ' . htmlspecialchars($nombreUser);
                    ?>
                </h2>
            <?php endif; ?>
        </div>
    
        <?php
            // Definir la ruta actual una sola vez y en minúsculas
            $currentPath = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        ?>

        <div class="navbar-left">
            <nav class="menu">
                <ul class="menu-principal" id="menu-principal">
                <!-- ----- inicio para rolres 1,2 y 3--------- -->
                    <?php if (isset($_SESSION['id_rol']) && in_array($_SESSION['id_rol'], ['1', '2', '3'])): ?>
                        <li>
                            <?php
                                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);                        
                                ?>
                            <a href="<?php echo BASE_URL; ?>public/share/dashboard.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/public/share/dashboard.php') ? 'activeURL' : ''; ?>">Panel Datos</a>
                        </li>
                        <?php endif; ?>
                        <!-- ----- fin para rolres 1,2 y 3--------- -->
                        
                        <!-- ----- inicio para rol 4--------- -->
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '4'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 4 (SENNOVA) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Presupuesto - SENNOVA
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/tecnoparque/metas/index.php') ? 'activeURL' : ''; ?>">
                                    Metas - Tecnoparque
                                </a>
                            </li>
                        <!-- Puedes agregar más opciones aquí para el rol 4 -->
                        <?php endif; ?>
                        <!-- ----- fin para rol 4--------- -->
                        <!-- ----- inicio para rol 5--------- -->
                        
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '5'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 5 (Tecnoparque) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Presupuesto
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/tecnoparque/metas/index.php') ? 'activeURL' : ''; ?>">
                                    Metas
                                </a>
                            </li>

                        <?php endif; ?>
                        <!-- ----- fin para rol 5--------- -->
                        <!-- ----- inicio para rol 6--------- -->
                        
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '6'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 6 (Tecnoacademia) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Presupuesto
                                </a>
                            </li>
                        <!-- Puedes agregar más opciones aquí para el rol 6 -->
                    <?php endif; ?>
                <!-- ----- fin para rol 6--------- -->
                <!-- ----- inicio para rol 3--------- -->
                    <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '3'): ?>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/presupuesto/index.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/presupuesto/index.php') ? 'activeURL' : ''; ?>">Registros RP</a>
                            
                        </li>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/presupuesto/historialOP.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/presupuesto/historialOP.php') ? 'activeURL' : ''; ?>">Registros RP (Viáticos)</a>
                        </li>
                <!-- ----- fin para rol 3--------- -->
                <!-- ----- inicio para rol 1--------- -->
                    <?php elseif (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '1'): ?>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/admin/index.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/admin/index.php') ? 'activeURL' : ''; ?>">Panel de control</a>
                        </li>
                <!-- ----- fin para rol 1--------- -->
                <!-- ----- inicio para rol 2--------- -->
                    <?php elseif (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '2'): ?>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/gestor/index.php" 
                               class="<?php echo ($currentPath === '/viaticosApp/app/gestor/index.php') ? 'activeURL' : ''; ?>">
                               Gestor
                            </a>
                        </li>
                <!-- ----- fin para rol 2--------- -->
                    <?php endif; ?>

                </ul>
            </nav>
    
            <div class="actions">
                <?php if(isset($_SESSION['id_rol'])):?>
                    <a href="<?php echo BASE_URL; ?>includes/session/salir.php" class="boton_ir" onclick="return salir()">Cerrar sesion</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>includes/session/login.php" class="boton_ir">Iniciar sesión</a>
                <?php endif; ?>
                <button class="menu-toggle" id="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>assets/js/header.js"></script>
</body>
</html>
<script>
function salir() {
    const modal = document.getElementById('logoutModal');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    
    modal.classList.add('active');
    
    return new Promise((resolve) => {
        confirmBtn.onclick = () => {
            modal.classList.remove('active');
            resolve(true);
            window.location.href = '<?php echo BASE_URL; ?>includes/session/salir.php';
        };
        
        cancelBtn.onclick = () => {
            modal.classList.remove('active');
            resolve(false);
        };
        
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                resolve(false);
            }
        };
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const logoutButton = document.querySelector('a[href*="salir.php"]');
    if (logoutButton) {
        logoutButton.onclick = function(e) {
            e.preventDefault();
            salir();
        };
    }
});
</script>
