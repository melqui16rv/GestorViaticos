<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

if (isset($_SESSION['id_rol']) && isset($_SESSION['numero_documento'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$error_message = '';

if (isset($_POST['Validar'])) {
    $numero = $_POST['numero_doc'];
    $contraseña = $_POST['contraseña'];
    
    try {
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
    <title>Inicio de Sesión SENA</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/login.css">
    <link rel="preload" as="image" href="<?php echo BASE_URL; ?>assets/img/public/foto.jpeg">
</head>
<body class="login-body">
    <style>
        .login-body {
            min-height: 100vh;
            background: url('<?php echo BASE_URL; ?>assets/img/public/foto.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
    </style>

    <div class="login-contenedor">
        <div class="login-imagen-contenedor"></div>
        <div class="login-formulario-contenedor">
            <div class="login-formulario">
                <img src="<?php echo BASE_URL; ?>assets/img/public/logo-sena-blanco.png" alt="Logo SENA" class="login-logof">
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
                        <i class="fa-solid fa-eye" id="login-togglePassword"></i>
                    </label>
                    <input type="submit" value="Ingresar" name="Validar" class="login-boton">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
