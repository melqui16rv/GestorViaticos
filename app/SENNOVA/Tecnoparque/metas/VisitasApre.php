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
    <button id="toggleFormButton" class="btn btn-primary">
        <i class="fas fa-plus"></i> Agregar Visita
    </button>

    <form id="formVisitas" method="POST" class="formulario" style="display: none;">
        <input type="hidden" name="action" id="action" value="create">
        <input type="hidden" name="id_visita" id="id_visita"> <div class="form-group">
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

    <div class="chart-container">
        <h2>Impacto de las Charlas</h2>
        <canvas id="impactoChart"></canvas>
    </div>

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
        document.getElementById('id_visita').value = ''; // Asegurar que el ID se limpia
        document.getElementById('action').value = 'create'; // Restablecer la acción a 'create'
        document.getElementById('formVisitas').style.display = 'none'; // Ocultar el formulario después de resetear
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
#formVisitas {
    display: none;
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f5f5f5;
}
.form-group {
    margin-bottom: 10px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
}
.form-group input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}
.btn-primary {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-primary:hover {
    background-color: #0056b3;
}
.btn-secondary {
    background-color: #e9ecef;
    color: #212529;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 10px;
}
.btn-secondary:hover {
    background-color: #d0d3d6;
}
.btn-danger {
    background-color: #dc3545;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-danger:hover {
    background-color: #c82333;
}
.btn-edit {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 5px;
}
.btn-edit:hover {
    background-color: #218838;
}
.tabla {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}
.tabla thead th {
    background-color: #f0f0f0;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #ddd;
}
.tabla tbody td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}
.tabla tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
.tabla tbody tr:hover {
    background-color: #f0f0f0;
}
</style>

