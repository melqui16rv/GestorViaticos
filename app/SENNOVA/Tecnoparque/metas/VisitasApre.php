<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();

// Calcular indicadores adicionales a partir de $visitas
$visitas = $metas->obtenerVisitasApre();
$indicadores = $metas->obtenerIndicadoresVisitas();

// Nuevos cálculos: agrupación por nodo y series temporales
$visitasPorNodo = [];
$visitasTemporal = [];
foreach($visitas as $v) {
    $nodo = $v['nodo'] ?? 'Desconocido';
    if (!isset($visitasPorNodo[$nodo])) {
        $visitasPorNodo[$nodo] = 0;
    }
    $visitasPorNodo[$nodo]++;
    
    // Agrupar por año-mes
    $mes = date('Y-m', strtotime($v['fechaCharla']));
    if (!isset($visitasTemporal[$mes])) {
        $visitasTemporal[$mes] = 0;
    }
    $visitasTemporal[$mes]++;
}
ksort($visitasTemporal);

// Nuevo cálculo: visitas por semana (últimas 5 semanas)
$monday = new DateTime("monday this week");
$semanas = [];
for ($i = 4; $i >= 0; $i--) {
    $weekStart = clone $monday;
    $weekStart->modify("-$i week");
    $weekEnd = clone $weekStart;
    $weekEnd->modify("+6 days");
    $label = $weekStart->format('d M') . " - " . $weekEnd->format('d M');
    $semanas[$label] = ['start' => $weekStart, 'end' => $weekEnd, 'count' => 0];
}
foreach($visitas as $v) {
    $fecha = new DateTime($v['fechaCharla']);
    foreach ($semanas as $label => &$datos) {
        if ($fecha >= $datos['start'] && $fecha <= $datos['end']) {
            $datos['count']++;
        }
    }
}
$labelsSemanales = array_keys($semanas);
$dataSemanales = array_map(fn($d) => $d['count'], $semanas);

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
        <div class="indicador">
            <h3>Visitas por Nodo</h3>
            <p>
                <?php 
                    foreach($visitasPorNodo as $nodo => $cant){
                        echo htmlspecialchars($nodo) . ": " . $cant . "<br>";
                    }
                ?>
            </p>
        </div>
    </div>

    <div class="chart-container" style="height: 400px;">
        <h2>Distribución de Visitas por Nodo</h2>
        <canvas id="nodoChart"></canvas>
    </div>

    <div class="chart-container" style="height: 400px;">
        <h2>Visitas por Semana</h2>
        <canvas id="semanalChart"></canvas>
    </div>

    <div style="text-align: center; margin: 20px 0;">
        <a href="reporte_visitas.php" target="_blank" class="btn btn-primary">Descargar Reporte Completo en PDF</a>
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

    // Gráfico: Distribución de Visitas por Nodo (Pie Chart)
    const ctxNodo = document.getElementById('nodoChart').getContext('2d');
    new Chart(ctxNodo, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($visitasPorNodo)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($visitasPorNodo)); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Nueva Gráfica: Visitas por Semana (Line Chart)
    const ctxSemanal = document.getElementById('semanalChart').getContext('2d');
    new Chart(ctxSemanal, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labelsSemanales); ?>,
            datasets: [{
                label: 'Visitas',
                data: <?php echo json_encode($dataSemanales); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
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
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}
#formVisitasApre .form-group {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}
#formVisitasApre .form-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #374151;
}
#formVisitasApre .form-group input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
    width: 100%;
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
/* Adaptabilidad para pantallas pequeñas */
@media (max-width: 768px) {
    #formVisitasApre {
        padding: 15px;
        width: 90%;
    }

    #formVisitasApre .form-group {
        margin-bottom: 10px;
    }

    #formVisitasApre .form-buttons {
        flex-direction: column;
        gap: 5px;
    }

    #formVisitasApre .btn {
        width: 100%;
    }
}
/* Formulario */
#formVisitasApre {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
}
#formVisitasApre .form-group label {
    font-weight: bold;
    color: #1e293b;
}
#formVisitasApre .form-group input {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 1rem;
    width: 100%;
    margin-top: 0.5rem;
}
#formVisitasApre .form-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}
#formVisitasApre .btn-primary {
    background: #2563eb;
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}
#formVisitasApre .btn-primary:hover {
    background: #1e40af;
}
#formVisitasApre .btn-secondary {
    background: #e2e8f0;
    color: #1e293b;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}
#formVisitasApre .btn-secondary:hover {
    background: #cbd5e1;
}

/* Tabla */
.tabla {
    width: 100%;
    border-collapse: collapse;
    margin-top: 2rem;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.tabla th,
.tabla td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}
.tabla th {
    background: #2563eb;
    color: #fff;
    font-weight: bold;
}
.tabla tr:hover {
    background: #f1f5f9;
}
</style>

