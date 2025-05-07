<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
requireRole(['1']);
$dato = new user();

if (isset($_POST['Registrar'])) {
    $num_doc = $_POST['num_doc'];
    $tipo_doc = $_POST['tipo_doc'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $nombre_completo = $nombres . ' ' . $apellidos;
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $id_rol = $_POST['id_rol'];
    $contraseña = $_POST['contraseña'];
    $contraseña_confirmation = $_POST['contraseña_confirmation']; // Get the confirmation

    // Validate password confirmation
    if ($contraseña !== $contraseña_confirmation) {
        $error_message = "Las contraseñas no coinciden."; // Set an error message
    } else {
        $dato->crearUsuario($num_doc, $tipo_doc, $nombre_completo, $contraseña, $email, $telefono, $id_rol);
        //  potentially redirect or show success
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="../../assets/css/links/agregarUsuario.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .form-label {
            font-weight: 600;
            color: #4b5563; /* text-gray-700 */
        }
        .form-input, .form-select {
            border-radius: 0.375rem; /* rounded-md */
            border-width: 1px;
            border-color: #d1d5db; /* border-gray-300 */
            padding: 0.75rem 1rem; /* px-4 py-3 */
            font-size: 1rem; /* text-base */
            line-height: 1.5rem; /* leading-5 */
            width: 100%;
            transition: border-color 0.15s ease-in-out, shadow-sm 0.15s ease-in-out;
            outline: none;
        }
        .form-input:focus, .form-select:focus {
            border-color: #3b82f6; /* focus:border-blue-500 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); /* focus:ring-blue-500, focus:ring-opacity-20 */
        }
        .form-input.error {
            border-color: #dc2626; /* border-red-500 */
        }
        .error-message {
            color: #dc2626; /* text-red-500 */
            font-size: 0.875rem; /* text-sm */
            margin-top: 0.5rem; /* mt-2 */
        }
        .password-container {
            position: relative;
            display: flex;
            width: 100%;
        }
        .password-input {
            width: 100%;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280; /* text-gray-500 */
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php';
    ?>
    <div class="flex justify-center items-center min-h-screen py-10">
        <form action="" method="POST" class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl space-y-6">
            <h2 class="text-2xl font-semibold text-gray-900">Agregar Usuario</h2>
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="documento" class="form-label">Número de documento:</label>
                    <input type="text" id="documento" name="num_doc" placeholder="Ingrese el número de documento" required class="form-input">
                </div>
                <div>
                    <label for="tipo_doc" class="form-label">Tipo de documento</label>
                    <select name="tipo_doc" id="tipo_doc" class="form-select">
                        <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                        <option value="Cédula de extranjería">Cédula de extranjería</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="form-label">Nombres:</label>
                    <input type="text" id="nombre" name="nombres" placeholder="Ingrese el primer nombre" required class="form-input">
                </div>
                <div>
                    <label for="Apellido" class="form-label">Apellidos:</label>
                    <input type="text" id="Apellido" name="apellidos" placeholder="Ingrese el primer Apellido" required class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                    <label for="email" class="form-label">Correo:</label>
                    <input type="email" name="email" id="email" required placeholder="Ingrese su correo" class="form-input">
                </div>
                <div>
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" name="telefono" id="telefono" placeholder="Ingrese un número de teléfono" required class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <div class="password-container">
                        <input type="password" name="contraseña" id="contraseña" required placeholder="Ingrese la contraseña" class="form-input password-input">
                        <i class="far fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>
                <div>
                    <label for="contraseña_confirmation" class="form-label">Confirmar Contraseña</label>
                    <div class="password-container">
                        <input type="password" name="contraseña_confirmation" id="contraseña_confirmation" required placeholder="Confirme la contraseña" class="form-input password-input">
                        <i class="far fa-eye password-toggle" id="toggleConfirmPassword"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="rol" class="form-label">Rol:</label>
                    <select id="rol" name="id_rol" required class="form-select">
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Gestor</option>
                        <option value="3">Planeación</option>
                        <option value="4">SENNOVA</option>
                        <option value="5">Tecnoparque</option>
                        <option value="6">Tecnoacademia</option>
                    </select>
                </div>
            </div>

            <input type="submit" value="Crear Usuario" name="Registrar" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline self-center w-full md:w-1/2">
        </form>
    </div>
    <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php';
    ?>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script> <script>
        const passwordInput = document.getElementById('contraseña');
        const togglePasswordButton = document.getElementById('togglePassword');
        const confirmPasswordInput = document.getElementById('contraseña_confirmation');
        const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');

        togglePasswordButton.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordButton.classList.remove('fa-eye');
                togglePasswordButton.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                togglePasswordButton.classList.remove('fa-eye-slash');
                togglePasswordButton.classList.add('fa-eye');
            }
        });

        toggleConfirmPasswordButton.addEventListener('click', () => {
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                toggleConfirmPasswordButton.classList.remove('fa-eye');
                toggleConfirmPasswordButton.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                toggleConfirmPasswordButton.classList.remove('fa-eye-slash');
                toggleConfirmPasswordButton.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
