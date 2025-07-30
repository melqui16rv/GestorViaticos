<?php
// Iniciar buffer de salida ANTES de cualquier include
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/conf/config.php';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/nav.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
    </div>    <div class="navbar" style="z-index: 999999;">
        <!-- Información del usuario a la izquierda -->
        <div class="navbar-left">
            <!-- Botón de sesión prioritario en móvil -->
            <div class="session-button-mobile">                <?php if(isset($_SESSION['id_rol'])):?>
                    <a href="<?php echo BASE_URL; ?>includes/session/salir.php" class="boton_ir mobile-session">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>includes/session/login.php" class="boton_ir mobile-session">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Entrar</span>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Información del usuario -->
            <?php if (isset($_SESSION['id_rol']) && $nombreUser !== null): ?>
                <h2 class="indicadorRol">
                    <span class="rol-text">
                        <?php 
                            switch ($_SESSION['id_rol']) {
                                case '1':
                                    echo "Admin";
                                    break;
                                case '2':
                                    echo "Gestor";
                                    break;
                                case '3':
                                    echo "Presupuesto";
                                    break;
                                case '4':
                                    echo "SENNOVA";
                                    break;
                                case '5':
                                    echo "Tecnoparque";
                                    break;
                                case '6':
                                    echo "Tecnoacademia";
                                    break;
                                case '7':
                                    echo "Acceso";
                                    break;
                                default:
                                    echo "";
                            }
                        ?>
                    </span>
                    <span class="nombre-text">
                        <?php echo htmlspecialchars($nombreUser); ?>
                    </span>
                </h2>
            <?php endif; ?>
        </div>        <!-- Navegación central -->
        <div class="navbar-center">
            <nav class="menu">
                <ul class="menu-principal" id="menu-principal">
                <?php
                    // Definir la ruta actual una sola vez y en minúsculas
                    $currentPath = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                ?>
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
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '7'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 4 (SENNOVA) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Solicitud Rol
                                </a>
                            </li>
                        <!-- Puedes agregar más opciones aquí para el rol 4 -->
                        <?php endif; ?>
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
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/admin/solicitudes/index.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/admin/solicitudes') ? 'activeURL' : ''; ?>">Solicitudes de cambio de rol</a>
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
                <!-- ----- fin para rol 2--------- -->                    <?php endif; ?>
                  <!-- ----- Enlace de perfil para todos los usuarios logueados--------- -->
                <!-- <?php //if (isset($_SESSION['numero_documento'])): ?>
                    <li>
                        <a href="<?php //echo BASE_URL; ?>public/share/cuenta.php" 
                           class="<?php //echo ($currentPath === '/viaticosapp/public/share/cuenta.php') ? 'activeURL' : ''; ?>">
                           <i class="fas fa-user-circle" style="margin-right: 8px;"></i>Mi Perfil
                        </a>
                    </li>
                <?php //endif; ?> -->
                <!-- ----- fin enlace de perfil--------- -->
                
                </ul>
            </nav>
        </div>        <!-- Acciones a la derecha -->
        <div class="navbar-right">
            <!-- Botón hamburguesa - solo visible en móvil -->
            <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú de navegación">
                <span></span>
                <span></span>
                <span></span>
            </button>
              <!-- Botón de sesión para desktop -->
            <div class="session-button-desktop">
                <?php if(isset($_SESSION['id_rol'])):?>
                    <a href="<?php echo BASE_URL; ?>includes/session/salir.php" class="boton_ir">Cerrar sesión</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>includes/session/login.php" class="boton_ir">Iniciar sesión</a>
                <?php endif; ?>
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
    // Buscar todos los enlaces de cerrar sesión
    const logoutButtons = document.querySelectorAll('a[href*="salir.php"]');
    
    logoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir navegación inmediata
            salir().then(confirmed => {
                // Solo navegar si el usuario confirmó
                if (confirmed) {
                    window.location.href = this.href;
                }
            });
        });
    });
});
</script>
