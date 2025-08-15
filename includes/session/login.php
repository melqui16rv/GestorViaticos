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
    $email_or_doc = $_POST['email_or_doc']; // Cambiado para aceptar email o documento
    $contraseña = $_POST['contraseña'];

    try {
        $trabajo = new user();
        $resultado = $trabajo->iniciarSesion($email_or_doc, $contraseña);

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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/login.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/css/share/login.css'); ?>">
    <link rel="preload" as="image" href="<?php echo BASE_URL; ?>assets/img/public/fotoSENA.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <style>
        :root {
            --login-primary-blue: rgba(27, 60, 91, 0.8);
            --login-white: #ffffff;
        }

        .login-body {
            min-height: 100vh;
            background: url('<?php echo BASE_URL; ?>assets/img/public/fotoSENA.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }        /* Contenedor para el campo de contraseña con icono de visibilidad */
        .login-form .password-container {
            position: relative;
            display: block;
            margin-bottom: 15px;
        }

        /* Icono de toggle de contraseña - posicionado correctamente */
        .login-form .password-container .password-toggle {
            position: absolute;
            top: 50%;
            right: 1.1rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.8);
            z-index: 10;
            padding: 0;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 1.3rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 2.2rem;
            width: 2.2rem;
        }

        .login-form .password-container .password-toggle:hover {
            color: rgba(255, 255, 255, 1);
        }

        .login-form .password-container .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        /* Input de contraseña con espacio adecuado para el icono */
        .login-form .password-container input[type="password"],
        .login-form .password-container input[type="text"] {
            width: 100%;
            padding-right: 2.8rem; /* Más espacio para el icono de visibilidad */
            padding-left: 3rem; /* Más espacio para el icono izquierdo */
            box-sizing: border-box;
        }

        /* Ajuste para todos los inputs con icono a la izquierda */
        .login-form label input[type="text"],
        .login-form label input[type="password"] {
            padding-left: 3rem; /* Más espacio para el icono izquierdo */
        }

        /* Estilos para el botón secundario de regresar */
        .login-boton-secundario {
            /* display: inline-block; */
            background-color: transparent;
            color: var(--login-white);
            /* border: 2px solid rgba(255, 255, 255, 0.5); */
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            text-decoration: none;
            text-align: center;
            margin-top: 0.5rem;
        }

        /* Hover del botón secundario */
        .login-boton-secundario:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-1px);
        }        .login-boton-secundario i {
            margin-right: 0.5rem;
            font-size: 0.9rem;
        }

        /* Estilos adicionales para el formulario mejorado */
        .login-form input[type="text"], 
        .login-form input[type="password"] {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--login-white);
            padding: 12px 40px 12px 40px;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .login-form input[type="text"]:focus, 
        .login-form input[type="password"]:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.6);
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }

        .login-form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-form label {
            position: relative;
            display: block;
            margin-bottom: 15px;
        }

        .login-form label i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            z-index: 1;
        }

        .login-boton {
            background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%);
            color: var(--login-white);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-boton:hover {
            background: linear-gradient(135deg, #3182ce 0%, #4299e1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fecaca;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Nuevo grupo para input con iconos */
        .input-icon-group {
            display: flex;
            align-items: center;
            position: relative;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 0 0.5rem;
            gap: 0.7rem; /* Nuevo: separa el icono del input */
        }
        .input-icon-group input[type="text"],
        .input-icon-group input[type="password"] {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--login-white);
            font-size: 1rem;
            padding: 0.9rem 2.5rem 0.9rem 0.2rem; /* padding-left reducido, padding-right igual para el icono derecho */
            outline: none;
            z-index: 1;
        }
        .input-icon-group .input-icon-left {
            position: static; /* Ya no absolute */
            display: flex;
            align-items: center;
            height: 100%;
            color: rgba(255,255,255,0.7);
            font-size: 1.2rem;
            z-index: 2;
        }
        .input-icon-group .input-icon-right {
            position: absolute;
            right: 0.9rem;
            display: flex;
            align-items: center;
            height: 100%;
            z-index: 2;
        }
        .input-icon-group .password-toggle {
            cursor: pointer;
            color: rgba(255,255,255,0.8);
            font-size: 1.3rem;
            transition: color 0.2s;
        }
        .input-icon-group .password-toggle:hover {
            color: #fff;
        }
        .input-icon-group input[type="text"]::placeholder,
        .input-icon-group input[type="password"]::placeholder {
            color: rgba(255,255,255,0.7);
        }
        .input-icon-group input[type="text"]:focus,
        .input-icon-group input[type="password"]:focus {
            box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
            border-radius: 6px;
        }
    </style>
</head>
<body class="login-body">
    <div class="login-contenedor">
        <div class="login-imagen-contenedor"></div>
        <div class="login-formulario-contenedor">
            <a href="<?php echo BASE_URL; ?>index.php" class="login-boton-secundario">
                <i class="fa-solid fa-arrow-left"></i>inicio
            </a>
            <div class="login-formulario">
                <img src="<?php echo BASE_URL; ?>assets/img/public/logo-sena-blanco.png" alt="Logo SENA" class="login-logof">
                <form class="login-form" action="" method="post">
                <?php
                    if ($error_message != '') {
                        echo '<div class="error-message">';
                        echo '<i class="fa-solid fa-triangle-exclamation"></i> ';
                        echo htmlspecialchars($error_message);
                        echo '</div>';
                    }
                ?>
                <div class="input-icon-group">
                    <span class="input-icon-left">
                        <i class="fa-solid fa-user">
                        </i>
                    </span>
                    <input type="text" placeholder="Email o Número de documento" name="email_or_doc" required>
                </div>
                <div class="input-icon-group">
                    <span class="input-icon-left">
                        <i class="fa-solid fa-lock">
                        </i>
                    </span>
                    <input type="password" id="password" placeholder="Contraseña" name="contraseña" required>
                    <span class="input-icon-right"><i class="fa-solid fa-eye password-toggle" id="login-togglePassword"></i></span>
                </div>
                <input type="submit" value="Ingresar" name="Validar" class="login-boton">
                <?php
                /* 
                <a href="https://appscide.com/GestorCuenta/public/register" class="login-boton-secundario">
                    Registrarse  <i class="fa-solid fa-arrow-right"></i>
                </a>
                */
                ?>
            </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#login-togglePassword');
        const passwordInput = document.querySelector('#password');
        togglePassword.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>