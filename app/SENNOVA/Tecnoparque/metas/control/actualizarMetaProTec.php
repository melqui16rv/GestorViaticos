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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/formActualizarProTec.css">
    <style>
        .modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.7rem;
}

.modal-message {
    color: #475569;
    font-size: 1.07rem;
    margin-bottom: 1.5rem;
}

.modal-btns {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 0.5rem;
}

.modal-btn {
    padding: 0.6rem 1.4rem;
    border-radius: 0.5rem;
    border: none;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
}

.modal-btn-confirm {
    background: linear-gradient(90deg, #34d399 0%, #60a5fa 100%);
    color: #fff;
}

.modal-btn-confirm:hover {
    background: linear-gradient(90deg, #60a5fa 0%, #34d399 100%);
}

.modal-btn-cancel {
    background: #e5e7eb;
    color: #374151;
}

.modal-btn-cancel:hover {
    background: #d1d5db;
}
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen py-8">
    <button class="back-button" id="btn-regresar" onclick="return false;">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
    <div class="form-card">
        <div class="form-title">Actualizar Proyectos Tecnoparque (Solo tipo "Tecnológico")</div>
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off" id="form-actualizar-proyectos">
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
                            <input type="hidden" name="original_terminados[]" value="<?php echo (int)$p['terminados']; ?>">
                            <input type="hidden" name="original_en_proceso[]" value="<?php echo (int)$p['en_proceso']; ?>">
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
    <!-- Modal de confirmación genérico -->
    <div id="modal-confirm" class="modal-bg" style="display:none;">
        <div class="modal-card">
            <div class="modal-title" id="modal-confirm-title">¿Estás seguro?</div>
            <div class="modal-message" id="modal-confirm-message">Tienes cambios sin guardar. ¿Deseas salir?</div>
            <div class="modal-btns">
                <button class="modal-btn modal-btn-confirm" id="modal-btn-si">Sí</button>
                <button class="modal-btn modal-btn-cancel" id="modal-btn-no">Cancelar</button>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
    let cambios = false;

    // Detectar cambios en los campos
    document.querySelectorAll('input[name="terminados[]"], input[name="en_proceso[]"]').forEach(input => {
        input.addEventListener('input', () => {
            cambios = true;
        });
    });

    // Control de calidad al enviar: solo enviar si hay cambios
    document.getElementById('form-actualizar-proyectos').addEventListener('submit', function(e) {
        let hayCambios = false;
        const terminados = document.querySelectorAll('input[name="terminados[]"]');
        const enProceso = document.querySelectorAll('input[name="en_proceso[]"]');
        const originalesTerminados = document.querySelectorAll('input[name="original_terminados[]"]');
        const originalesEnProceso = document.querySelectorAll('input[name="original_en_proceso[]"]');
        for (let i = 0; i < terminados.length; i++) {
            if (
                terminados[i].value !== originalesTerminados[i].value ||
                enProceso[i].value !== originalesEnProceso[i].value
            ) {
                hayCambios = true;
                break;
            }
        }
        if (!hayCambios) {
            alert('No hay cambios para actualizar.');
            e.preventDefault();
            return false;
        }
        cambios = false; // Se van a guardar los cambios
    });

    // Alerta al intentar salir si hay cambios sin guardar
    window.addEventListener('beforeunload', function (e) {
        if (cambios) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Al hacer click en el botón regresar
    document.getElementById('btn-regresar').addEventListener('click', function(e) {
        if (cambios) {
            if (confirm('Hay cambios sin guardar. ¿Seguro que deseas salir?')) {
                window.location.href = '<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/index.php';
            }
        } else {
            window.location.href = '<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/index.php';
        }
    });
    </script>
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
