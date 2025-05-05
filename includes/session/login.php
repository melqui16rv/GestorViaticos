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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .login-body {
            min-height: 100vh;
            background: url('<?php echo BASE_URL; ?>assets/img/public/foto.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        .login-form .password-container { /* Nuevo contenedor para el input y el icono */
            position: relative;
            display: block;
            margin-bottom: 15px;
        }

        .login-form .password-container i { /* Estilo para el icono dentro del contenedor */
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #fff; /* Color blanco para el icono */
        }

        .login-form .password-container i:active {
            transform: translateY(-50%) scale(0.9); /* Pequeña animación al hacer clic */
            transition: transform 0.1s ease-in-out;
        }

        .login-form .password-container input[type="password"],
        .login-form .password-container input[type="text"] { /* Asegurar que el input ocupe todo el ancho */
            width: 100%;
            padding-right: 35px; /* Espacio para el icono */
            box-sizing: border-box; /* Evitar que el padding aumente el ancho total */
        }
    </style>
</head>
<body class="login-body">
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
                    <div class="password-container"> <label for="">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" placeholder="Contraseña" name="contraseña" required>
                            <i class="fa-solid fa-eye" id="login-togglePassword"></i>
                        </label>
                    </div>
                    <input type="submit" value="Ingresar" name="Validar" class="login-boton">
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#login-togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // Cambia el tipo del input de password
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Cambia el icono del ojo
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>