<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
requireRole(['1']);
$dato = new user();

// Inicializar variables para mantener los valores del formulario
$num_doc = '';
$tipo_doc = '';
$nombres = '';
$apellidos = '';
$email = '';
$telefono = '';
$id_rol = '';

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
    $contraseña_confirmation = $_POST['contraseña_confirmation'];

    // Validar la confirmación de la contraseña
    if ($contraseña !== $contraseña_confirmation) {
        $error_message = "Las contraseñas no coinciden.";
    } else {
        $dato->crearUsuario($num_doc, $tipo_doc, $nombre_completo, $contraseña, $email, $telefono, $id_rol);
        //  potencialmente redirigir o mostrar éxito
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .form-label {
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-input, .form-select {
            border-radius: 0.375rem;
            border-width: 1px;
            border-color: #d1d5db;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5rem;
            width: 100%;
            transition: border-color 0.15s ease-in-out, shadow-sm 0.15s ease-in-out;
            outline: none;
            background-color: white;
        }

        .form-input:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-input.error {
            border-color: #dc2626;
        }

        .form-input.success {
            border-color: #16a34a;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
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
            color: #6b7280;
            z-index: 10;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .form-submit {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 2rem;
            width: 100%;
            max-width: 320px;
            align-self: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-submit:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .form-submit:active {
            background-color: #388E3C;
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 800px;
        }

        .form-section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .password-strength {
            margin-top: 0.5rem;
            height: 0.5rem;
            border-radius: 0.375rem;
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }

        .password-strength-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            border-radius: 0.375rem;
            transition: width 0.3s ease;
        }

        .password-strength-text {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.25rem;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #e5e7eb;
            color: #374151;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-button:hover {
            background-color: #d1d5db;
        }

        .back-button i {
            font-size: 1.25rem;
        }
        .password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.5rem;
    z-index: 10;
    transition: transform 0.2s ease;
}

.password-toggle:hover {
    transform: translateY(-50%) scale(1.2);
}

.password-toggle:active {
    animation: bounce 0.3s;
}

@keyframes bounce {
    0%   { transform: translateY(-50%) scale(1); }
    50%  { transform: translateY(-50%) scale(1.3); }
    100% { transform: translateY(-50%) scale(1); }
}

    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen py-8">
    <button class="back-button" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
    <div class="card">
        <form action="" method="POST" class="space-y-6">
            <h2 class="form-section-title">Agregar Usuario</h2>
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="documento" class="form-label">Número de documento:</label>
                    <input type="text" id="documento" name="num_doc" placeholder="Ingrese el número de documento" required value="<?php echo $num_doc; ?>" class="form-input">
                </div>
                <div>
                    <label for="tipo_doc" class="form-label">Tipo de documento</label>
                    <select name="tipo_doc" id="tipo_doc" class="form-select" value="<?php echo $tipo_doc; ?>">
                        <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                        <option value="Cédula de extranjería">Cédula de extranjería</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="form-label">Nombres:</label>
                    <input type="text" id="nombre" name="nombres" placeholder="Ingrese el primer nombre" required value="<?php echo $nombres; ?>" class="form-input">
                </div>
                <div>
                    <label for="Apellido" class="form-label">Apellidos:</label>
                    <input type="text" id="Apellido" name="apellidos" placeholder="Ingrese el primer Apellido" required value="<?php echo $apellidos; ?>" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                    <label for="email" class="form-label">Correo:</label>
                    <input type="email" name="email" id="email" required placeholder="Ingrese su correo" value="<?php echo $email; ?>" class="form-input">
                </div>
                <div>
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" name="telefono" id="telefono" placeholder="Ingrese un número de teléfono" required value="<?php echo $telefono; ?>" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <div class="password-container">
                        <input type="password" name="contraseña" id="contraseña" placeholder="Ingrese la contraseña" required class="form-input password-input">
                        <span class="password-toggle" id="togglePassword">👁️‍🗨️</span>
                    </div>
                    <div class="password-container">
                        <input type="password" name="contraseña_confirmation" id="contraseña_confirmation" required placeholder="Confirme la contraseña" class="form-input password-input">
                        <span class="password-toggle" id="toggleConfirmPassword">👁️‍🗨️</span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="rol" class="form-label">Rol:</label>
                    <select name="id_rol" id="rol" class="form-select" value="<?php echo $id_rol; ?>">
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

            <input type="submit" value="Crear Usuario" name="Registrar" class="form-submit">
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Función para alternar la visibilidad de la contraseña
        function togglePasswordVisibility(inputField, toggleButton) {
            const type = inputField.type === 'password' ? 'text' : 'password';
            inputField.type = type;

            // Actualizar el ícono
            if (type === 'password') {
                toggleButton.innerHTML = '<i class="far fa-eye"></i>';
            } else {
                toggleButton.innerHTML = '<i class="far fa-eye-slash"></i>';
            }
        }

        // Elementos de contraseña y confirmación
        const passwordInput = document.getElementById('contraseña');
        const togglePasswordButton = document.getElementById('togglePassword');

        const confirmPasswordInput = document.getElementById('contraseña_confirmation');
        const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');

        const passwordStrengthBar = document.getElementById('passwordStrengthBar');
        const passwordStrengthText = document.getElementById('passwordStrengthText');

        // Mostrar/ocultar contraseña principal
        togglePasswordButton.addEventListener('click', () => {
            togglePasswordVisibility(passwordInput, togglePasswordButton);
        });

        // Mostrar/ocultar confirmación de contraseña
        toggleConfirmPasswordButton.addEventListener('click', () => {
            togglePasswordVisibility(confirmPasswordInput, toggleConfirmPasswordButton);
        });

        // Validación de fuerza de contraseña
        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            let strength = 0;
            let color = '';
            let text = '';

            if (password.length >= 8) {
                strength += 25;
            }
            if (password.match(/[a-z]+/)) {
                strength += 25;
            }
            if (password.match(/[A-Z]+/)) {
                strength += 25;
            }
            if (password.match(/[0-9]+/)) {
                strength += 25;
            }

            if (strength < 50) {
                color = '#dc2626'; // rojo
                text = 'Débil';
                passwordInput.classList.remove('success');
                passwordInput.classList.add('error');
            } else if (strength < 80) {
                color = '#f59e0b'; // naranja
                text = 'Moderada';
                passwordInput.classList.remove('success');
                passwordInput.classList.remove('error');
            } else {
                color = '#16a34a'; // verde
                text = 'Fuerte';
                passwordInput.classList.remove('error');
                passwordInput.classList.add('success');
            }

            passwordStrengthBar.style.width = `${strength}%`;
            passwordStrengthBar.style.backgroundColor = color;
            passwordStrengthText.textContent = text;
        });

        // Validación en tiempo real de coincidencia de contraseñas
        confirmPasswordInput.addEventListener('input', () => {
            if (confirmPasswordInput.value === passwordInput.value) {
                confirmPasswordInput.classList.remove('error');
                confirmPasswordInput.classList.add('success');
            } else {
                confirmPasswordInput.classList.remove('success');
                confirmPasswordInput.classList.add('error');
            }
        });
    </script>
</body>
</html>
