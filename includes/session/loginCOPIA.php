<?php
// Mostrar errores generados por alguna acción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Incluir el archivo de configuración que maneja la sesión
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/dashboard' . '/conf/config.php');

// require_once BASE_URL . 'app/shareFolder/navbar.php';

// No es necesario llamar a session_start() aquí porque ya se maneja en auth.php

// Verificar si el usuario ya está logueado
if (isset($_SESSION['id_rol']) && isset($_SESSION['numero_documento'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$error_message = '';

if (isset($_POST['Validar'])) {
    $numero = $_POST['numero_doc'];
    $contraseña = $_POST['contraseña'];
    
    try {
        require_once BASE_URL . '/math/gen/user.php';
        $trabajo = new user();
        $resultado = $trabajo->iniciarSesion($numero, $contraseña);
    
        if (!$resultado) {
            $error_message = "Error al iniciar sesión. Por favor, verifica tus credenciales.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <title>Inicio de Sesión SENA</title>
</head>
<body>
    <div class="sub_body" style="margin-top:-50px">
        <div class="contenedor">       
            <div class="imagen-contenedor">
            </div>
            <div class="formulario-contenedor">
                <div class="formulario">
                    <h1>jd</h1>
                    <a href="<?php echo BASE_URL; ?>index.php"><img src="<?php echo BASE_URL; ?>assets/img/public/logosenaBlack.png" alt="Logo SENA" class="logof"></a>
                    
                    <form class="login-form" action="" method="post">
                        <?php
                            if ($error_message != '') {
                                echo '<p style="color:red;text-align: center;">' . htmlspecialchars($error_message) . '</p>';
                            }
                        ?>
        
                        <label for="">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" placeholder="Número de documento" name="numero_doc" required>
                        </label>
        
                        <label for="">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" placeholder="Contraseña" name="contraseña" required>
                            <i class="fa-solid fa-eye" id="togglePassword"></i>
                        </label>
                        <input type="submit" value="Ingresar" name="Validar" class="boton">
                    </form>
                </div>
            </div>
        </div>
        <?php 
            require BASE_URL . 'public\share\footer.php';
        ?>
    </div>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
