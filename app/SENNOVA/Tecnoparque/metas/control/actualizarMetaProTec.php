<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();

// Procesar actualización si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_proyectos'])) {
    $ids = $_POST['id_PBT'] ?? [];
    $terminados = $_POST['terminados'] ?? [];
    $en_proceso = $_POST['en_proceso'] ?? [];

    foreach ($ids as $i => $id) {
        // Solo actualiza si hay cambios en los valores enviados
        $nuevo_terminados = (int)$terminados[$i];
        $nuevo_en_proceso = (int)$en_proceso[$i];

        // Obtener valores actuales para comparar
        $proyecto_actual = $metas->obtenerProyectoPorId($id);
        if (
            $proyecto_actual &&
            (
                $proyecto_actual['terminados'] != $nuevo_terminados ||
                $proyecto_actual['en_proceso'] != $nuevo_en_proceso
            )
        ) {
            // Actualizar con la fecha y hora actual
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $metas->actualizarProyectoTecCompleto($id, $nuevo_terminados, $nuevo_en_proceso, $fecha_actualizacion);
        }
    }
    // Recargar los datos después de actualizar
    $mensaje = "¡Actualización exitosa!";
}

// Obtener solo los proyectos tipo Tecnológico
$proyectos = $metas->obtenerProyectosTecPorTipo('Tecnológico');
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/metas.css">
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

        .form-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            margin-bottom: 2rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .proy-form-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            font-size: 1.05rem;
        }
        .proy-form-table th, .proy-form-table td {
            padding: 0.9rem 0.7rem;
            text-align: center;
        }
        .proy-form-table th {
            background: linear-gradient(90deg, #e0f7e9 0%, #fffbe6 100%);
            color: #222;
            font-weight: 700;
            border-bottom: 2px solid #e0e0e0;
        }
        .proy-form-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .proy-form-table tr:hover {
            background-color: #f0f6f3;
        }
        .proy-form-table input[type="number"] {
            width: 90px;
            padding: 0.4rem 0.6rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            font-size: 1rem;
            text-align: right;
            background: #f9fafb;
            transition: border-color 0.2s;
        }
        .proy-form-table input[type="number"]:focus {
            border-color: #3b82f6;
            background: #fff;
            outline: none;
        }
        .proy-form-table .readonly {
            background: #f3f4f6;
            color: #64748b;
        }
        .proy-form-table .fecha-actualizacion {
            font-size: 0.98em;
            color: #64748b;
        }
        .actualizar-proyectos-btn {
            margin-top: 1.5rem;
            background: linear-gradient(90deg, #34d399 0%, #60a5fa 100%);
            color: #fff;
            font-weight: 600;
            font-size: 1.08rem;
            padding: 0.65rem 1.4rem;
            border: none;
            border-radius: 0.7rem;
            box-shadow: 0 2px 8px rgba(52,211,153,0.08), 0 1.5px 6px rgba(96,165,250,0.08);
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            outline: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
            margin-right: auto;
        }
        .actualizar-proyectos-btn:hover, .actualizar-proyectos-btn:focus {
            background: linear-gradient(90deg, #60a5fa 0%, #34d399 100%);
            box-shadow: 0 4px 16px rgba(52,211,153,0.13), 0 3px 12px rgba(96,165,250,0.13);
            transform: translateY(-2px) scale(1.03);
        }
        .icon-refresh {
            width: 1.3em;
            height: 1.3em;
            stroke-width: 2.2;
        }
        .mensaje-exito {
            background: #e0f7e9;
            color: #15803d;
            border-radius: 0.5rem;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1.2rem;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen py-8">
    <button class="back-button" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
    <div class="form-card">
        <div class="form-title">Actualizar Proyectos Tecnoparque (Solo tipo "Tecnológico")</div>
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <table class="proy-form-table">
                <thead>
                    <tr>
                        <th>Línea</th>
                        <th>Terminados</th>
                        <th>En Proceso</th>
                        <th>Fecha Actualización</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proyectos as $i => $p): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($p['nombre_linea']); ?>
                            <input type="hidden" name="id_PBT[]" value="<?php echo $p['id_PBT']; ?>">
                        </td>
                        <td>
                            <input type="number" name="terminados[]" min="0" value="<?php echo (int)$p['terminados']; ?>" required>
                        </td>
                        <td>
                            <input type="number" name="en_proceso[]" min="0" value="<?php echo (int)$p['en_proceso']; ?>" required>
                        </td>
                        <td class="fecha-actualizacion">
                            <?php echo htmlspecialchars($p['fecha_actualizacion']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="actualizar_proyectos" class="actualizar-proyectos-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.582 9A7.003 7.003 0 0112 5c3.866 0 7 3.134 7 7 0 1.657-.573 3.182-1.535 4.382M18.418 15A7.003 7.003 0 0112 19c-3.866 0-7-3.134-7-7 0-1.657.573-3.182 1.535-4.382"/>
                </svg>
                Actualizar
            </button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
</body>
</html>
<?php
// Métodos auxiliares para obtener y actualizar un registro completo
if (!method_exists($metas, 'obtenerProyectoPorId')) {
    function obtenerProyectoPorId($id) {
        $sql = "SELECT * FROM proyectos_tecnoparque WHERE id_PBT = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $metas->obtenerProyectoPorId = 'obtenerProyectoPorId';
}
if (!method_exists($metas, 'actualizarProyectoTecCompleto')) {
    function actualizarProyectoTecCompleto($id_PBT, $terminados, $en_proceso, $fecha_actualizacion) {
        $sql = "UPDATE proyectos_tecnoparque 
                SET terminados = :terminados, 
                    en_proceso = :en_proceso, 
                    fecha_actualizacion = :fecha_actualizacion
                WHERE id_PBT = :id_PBT";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':terminados', $terminados, PDO::PARAM_INT);
        $stmt->bindParam(':en_proceso', $en_proceso, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_actualizacion', $fecha_actualizacion, PDO::PARAM_STR);
        $stmt->bindParam(':id_PBT', $id_PBT, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    $metas->actualizarProyectoTecCompleto = 'actualizarProyectoTecCompleto';
}
?>
