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
$indicadores = $metas->obtenerIndicadoresVisitas();
?>

<h1 class="titulo">Gestión de Visitas de Aprendices</h1>

<div class="dashboard-container">
    <!-- Botón para mostrar/ocultar formulario -->
    <button id="toggleFormButton" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Visita
    </button>

    <!-- Formulario para agregar/editar visitas -->
    <form id="formVisitas" method="POST" class="formulario" style="display: none;">
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

    <!-- Indicadores -->
    <div class="indicadores">
        <div class="indicador">
            <h3>Total de Asistentes</h3>
            <p><?php echo $indicadores['total_asistentes']; ?></p>
        </div>
        <div class="indicador">
            <h3>Total de Charlas</h3>
            <p><?php echo $indicadores['total_charlas']; ?></p>
        </div>
        <div class="indicador">
            <h3>Promedio de Asistentes por Charla</h3>
            <p><?php echo $indicadores['promedio_asistentes']; ?></p>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="chart-container">
        <h2>Impacto de las Charlas</h2>
        <canvas id="impactoChart"></canvas>
    </div>

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

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Mostrar/ocultar formulario
    document.getElementById('toggleFormButton').addEventListener('click', function() {
        const form = document.getElementById('formVisitas');
        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    });

    // Editar visita
    function editVisita(visita) {
        document.getElementById('id_visita').value = visita.id_visita;
        document.getElementById('encargado').value = visita.encargado;
        document.getElementById('numAsistentes').value = visita.numAsistentes;
        document.getElementById('fechaCharla').value = visita.fechaCharla.replace(' ', 'T');
        document.getElementById('action').value = 'update';
        document.getElementById('formVisitas').style.display = 'block';
    }

    function resetForm() {
        document.getElementById('formVisitas').reset();
        document.getElementById('id_visita').value = '';
        document.getElementById('action').value = 'create';
    }

    // Gráfico de impacto
    const ctx = document.getElementById('impactoChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($indicadores['encargados']); ?>,
            datasets: [{
                label: 'Número de Asistentes',
                data: <?php echo json_encode($indicadores['asistentes_por_encargado']); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<style>
.indicadores {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
}
.indicador {
    text-align: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}
.chart-container {
    margin: 20px 0;
    height: 400px;
}
</style>