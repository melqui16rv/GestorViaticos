<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();

// Manejo de acciones (crear, actualizar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $metas->insertarVisitasApre($_POST['encargado'], $_POST['numAsistentes'], $_POST['fechaCharla']);
                break;
            case 'update':
                $metas->actualizarVisitasApre($_POST['id_visita'], $_POST['encargado'], $_POST['numAsistentes'], $_POST['fechaCharla']);
                break;
            case 'delete':
                $metas->eliminarVisitasApre($_POST['id_visita']);
                break;
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Obtener todas las visitas
$visitas = $metas->obtenerVisitasApre();
?>

<h1 class="titulo">Gestión de Visitas de Aprendices</h1>

<div class="dashboard-container">
    <!-- Formulario para agregar/editar visitas -->
    <form id="formVisitas" method="POST" class="formulario">
        <input type="hidden" name="action" id="action" value="create">
        <div class="form-group">
            <label for="encargado">Encargado:</label>
            <input type="text" id="encargado" name="encargado" required>
        </div>
        <div class="form-group">
            <label for="numAsistentes">Número de Asistentes:</label>
            <input type="number" id="numAsistentes" name="numAsistentes" required>
        </div>
        <div class="form-group">
            <label for="fechaCharla">Fecha de la Charla:</label>
            <input type="datetime-local" id="fechaCharla" name="fechaCharla" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="reset" class="btn btn-secondary" onclick="resetForm()">Cancelar</button>
    </form>

    <!-- Tabla para listar visitas -->
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Encargado</th>
                <th>Número de Asistentes</th>
                <th>Fecha de la Charla</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visitas as $visita): ?>
            <tr>
                <td><?php echo htmlspecialchars($visita['id_visita']); ?></td>
                <td><?php echo htmlspecialchars($visita['encargado']); ?></td>
                <td><?php echo htmlspecialchars($visita['numAsistentes']); ?></td>
                <td><?php echo htmlspecialchars($visita['fechaCharla']); ?></td>
                <td>
                    <button class="btn btn-edit" onclick="editVisita(<?php echo htmlspecialchars(json_encode($visita)); ?>)">Editar</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_visita" value="<?php echo $visita['id_visita']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function editVisita(visita) {
        document.getElementById('id_visita').value = visita.id_visita;
        document.getElementById('encargado').value = visita.encargado;
        document.getElementById('numAsistentes').value = visita.numAsistentes;
        document.getElementById('fechaCharla').value = visita.fechaCharla.replace(' ', 'T');
        document.getElementById('action').value = 'update';
    }

    function resetForm() {
        document.getElementById('formVisitas').reset();
        document.getElementById('id_visita').value = '';
        document.getElementById('action').value = 'create';
    }
</script>
<style>
/* ...existing styles... */
</style>