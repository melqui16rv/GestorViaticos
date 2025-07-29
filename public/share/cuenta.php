<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/metodos_perfil.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['numero_documento'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}
requireRole(['9']);
$numero_documento_sesion = $_SESSION['numero_documento'];
$metodosPerfilUsuario = new metodosPerfilUsuario();

// Obtener datos actuales del usuario
$datosUsuario = $metodosPerfilUsuario->obtenerDatosPerfil($numero_documento_sesion);

if (!$datosUsuario['success']) {
    $_SESSION['error'] = 'Error al cargar los datos del perfil';
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$usuario = $datosUsuario['data'];
$tiposDocumento = $metodosPerfilUsuario->obtenerTiposDocumento();

// Obtener nombre del rol para mostrar
$roles = [
    1 => 'Administrador',
    2 => 'Gestor',
    3 => 'Instructor',
    4 => 'Aprendiz',
    5 => 'Usuario',
    6 => 'Presupuesto'
];
$nombreRol = isset($roles[$usuario['id_rol']]) ? $roles[$usuario['id_rol']] : 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Sistema de Viáticos</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/nav.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: var(--nav-height, 70px);
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            backdrop-filter: blur(10px);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .profile-role {
            font-size: 16px;
            opacity: 0.9;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
        }

        .profile-content {
            padding: 40px 30px;
        }

        .section-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #667eea;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .readonly-field {
            background: #f1f3f4 !important;
            color: #666;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: none;
            align-items: center;
            gap: 10px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .profile-header {
                padding: 30px 20px;
            }

            .profile-content {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .profile-name {
                font-size: 24px;
            }

            .section-title {
                font-size: 20px;
            }
        }

        .back-btn {
            position: fixed;
            top: 90px;
            left: 20px;
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            border: none;
            padding: 12px 15px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: white;
            transform: translateX(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Incluir navegación -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <!-- Botón de regreso -->
    <button class="back-btn" onclick="history.back()" title="Volver">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="container">
        <div class="profile-card">
            <!-- Header del perfil -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="profile-name"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></h1>
                <span class="profile-role"><?php echo $nombreRol; ?></span>
            </div>

            <!-- Contenido del perfil -->
            <div class="profile-content">
                <h2 class="section-title">
                    <i class="fas fa-edit"></i>
                    Editar Información de Perfil
                </h2>

                <!-- Mostrar mensajes -->
                <div id="message-container"></div>

                <!-- Formulario de edición -->
                <form id="profileForm">
                    <!-- Información que NO se puede cambiar -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre_completo">Nombre Completo</label>
                            <input type="text" 
                                   class="form-control readonly-field" 
                                   id="nombre_completo" 
                                   value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" 
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" 
                                   class="form-control readonly-field" 
                                   id="email" 
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Información que SÍ se puede cambiar -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero_documento">Número de Documento *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="numero_documento" 
                                   name="numero_documento"
                                   value="<?php echo htmlspecialchars($usuario['numero_documento']); ?>" 
                                   pattern="[0-9]{6,12}"
                                   title="Solo números, entre 6 y 12 dígitos"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="tipo_doc">Tipo de Documento *</label>
                            <select class="form-control" id="tipo_doc" name="tipo_doc" required>
                                <?php foreach ($tiposDocumento as $codigo => $nombre): ?>
                                    <option value="<?php echo $codigo; ?>" 
                                            <?php echo ($usuario['tipo_doc'] == $codigo) ? 'selected' : ''; ?>>
                                        <?php echo $nombre; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" 
                               class="form-control" 
                               id="telefono" 
                               name="telefono"
                               value="<?php echo htmlspecialchars($usuario['telefono']); ?>" 
                               pattern="[0-9]{7,15}"
                               title="Solo números, entre 7 y 15 dígitos"
                               required>
                    </div>

                    <button type="submit" class="btn-primary">
                        <span class="btn-text">Actualizar Perfil</span>
                        <div class="loading">
                            <div class="spinner"></div>
                            Actualizando...
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('.btn-primary');
            const btnText = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.loading');
            const messageContainer = document.getElementById('message-container');
            
            // Mostrar loading
            btnText.style.display = 'none';
            loading.style.display = 'flex';
            btn.disabled = true;
            
            // Limpiar mensajes anteriores
            messageContainer.innerHTML = '';
            
            const formData = new FormData(this);
            formData.append('action', 'actualizar_perfil');
            
            fetch('<?php echo BASE_URL; ?>public/share/procesar_perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageContainer.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            ${data.message}
                        </div>
                    `;
                    
                    // Si cambió el número de documento, recargar la página después de 2 segundos
                    if (data.numero_documento_cambio) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } else {
                    messageContainer.innerHTML = `
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                messageContainer.innerHTML = `
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error de conexión. Por favor, intente nuevamente.
                    </div>
                `;
            })
            .finally(() => {
                // Ocultar loading
                btnText.style.display = 'inline';
                loading.style.display = 'none';
                btn.disabled = false;
                
                // Scroll al mensaje
                messageContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        // Validación en tiempo real para número de documento
        document.getElementById('numero_documento').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
        });

        // Validación en tiempo real para teléfono
        document.getElementById('telefono').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 15) {
                this.value = this.value.slice(0, 15);
            }
        });
    </script>
</body>
</html>