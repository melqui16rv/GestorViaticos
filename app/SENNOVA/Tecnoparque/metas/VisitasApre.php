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
    <a href="javascript:void(0);" id="toggleFormButtonVisitas" class="actualizar-tabla-link inline-block">
        <button type="button" class="actualizar-tabla-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span id="toggleFormButtonText">Agregar Visita</span>
        </button>
    </a>

    <form id="formVisitasApre" method="POST" class="formulario" style="display: none;">
        <input type="hidden" name="action" id="actionVisitas" value="create">
        <input type="hidden" name="id_visita" id="id_visitaVisitas">
        <div class="form-group">
            <label for="encargadoVisitas">Encargado:</label>
            <input type="text" id="encargadoVisitas" name="encargado" required>
        </div>
        <div class="form-group">
            <label for="numAsistentesVisitas">Número de Asistentes:</label>
            <input type="number" id="numAsistentesVisitas" name="numAsistentes" required>
        </div>
        <div class="form-group">
            <label for="fechaCharlaVisitas">Fecha de la Charla:</label>
            <input type="datetime-local" id="fechaCharlaVisitas" name="fechaCharla" required>
        </div>
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="reset" class="btn btn-secondary" onclick="resetFormVisitas()">Cancelar</button>
        </div>
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
        <canvas id="impactoChartVisitas"></canvas>
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
    // Mostrar/ocultar formulario y cambiar texto del botón
    document.getElementById('toggleFormButtonVisitas').addEventListener('click', function() {
        const form = document.getElementById('formVisitasApre');
        const buttonText = document.getElementById('toggleFormButtonText');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            buttonText.textContent = 'Agregar Visita';
        } else {
            resetFormVisitas();
        }
    });

    // Editar visita
    function editVisita(visita) {
        document.getElementById('id_visitaVisitas').value = visita.id_visita;
        document.getElementById('encargadoVisitas').value = visita.encargado;
        document.getElementById('numAsistentesVisitas').value = visita.numAsistentes;
        document.getElementById('fechaCharlaVisitas').value = visita.fechaCharla.replace(' ', 'T');
        document.getElementById('actionVisitas').value = 'update';
        document.getElementById('formVisitasApre').style.display = 'block';
        document.getElementById('toggleFormButtonText').textContent = 'Editar Visita';
    }

    function resetFormVisitas() {
        document.getElementById('formVisitasApre').reset();
        document.getElementById('id_visitaVisitas').value = '';
        document.getElementById('actionVisitas').value = 'create';
        document.getElementById('formVisitasApre').style.display = 'none';
        document.getElementById('toggleFormButtonText').textContent = 'Agregar Visita';
    }

    // Gráfico de impacto
    const ctxVisitas = document.getElementById('impactoChartVisitas').getContext('2d');
    new Chart(ctxVisitas, {
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
#formVisitasApre {
    display: none;
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
#formVisitasApre .form-group {
    margin-bottom: 15px;
}
#formVisitasApre .form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #374151;
}
#formVisitasApre .form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}
#formVisitasApre .form-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
#formVisitasApre .btn {
    padding: 10px 15px;
    font-size: 1rem;
    border-radius: 4px;
    cursor: pointer;
}
#formVisitasApre .btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}
#formVisitasApre .btn-primary:hover {
    background-color: #0056b3;
}
#formVisitasApre .btn-secondary {
    background-color: #e9ecef;
    color: #212529;
    border: none;
}
#formVisitasApre .btn-secondary:hover {
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
/* Estilos adaptados para el botón "Agregar Visita" */
.actualizar-tabla-link {
    text-decoration: none;
    display: inline-block; /* Asegura que el enlace solo ocupe el tamaño del contenido */
}
.actualizar-tabla-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
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
}
.actualizar-tabla-btn:hover, .actualizar-tabla-btn:focus {
    background: linear-gradient(90deg, #60a5fa 0%, #34d399 100%);
    box-shadow: 0 4px 16px rgba(52,211,153,0.13), 0 3px 12px rgba(96,165,250,0.13);
    transform: translateY(-2px) scale(1.03);
}
.icon-refresh {
    width: 1.3em;
    height: 1.3em;
    stroke-width: 2.2;
}
</style>

