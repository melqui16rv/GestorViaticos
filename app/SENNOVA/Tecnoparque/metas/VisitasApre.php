<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();

// Agregar esta función auxiliar después de los requires
function formatearFecha($fecha) {
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    
    $timestamp = strtotime($fecha);
    $dia = date('j', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);
    $hora = strtolower(date('g:i a', $timestamp));
    
    return $dia . ' de ' . $mes . ' ' . $anio . '<br>' . $hora;
}

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
<head>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/visApreStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
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
    
    <div class="filtros-container">
        <div class="filtro-grupo">
            <label for="ordenRegistros">Orden:</label>
            <select id="ordenRegistros" class="filtro-select">
                <option value="DESC">Más recientes primero</option>
                <option value="ASC">Más antiguos primero</option>
            </select>
        </div>
        
        <div class="filtro-grupo">
            <label for="limiteRegistros">Mostrar:</label>
            <select id="limiteRegistros" class="filtro-select">
                <option value="30">30 registros</option>
                <option value="50">50 registros</option>
                <option value="70">70 registros</option>
                <option value="">Todos</option>
            </select>
        </div>
        
        <div class="filtro-grupo">
            <label for="filtroEncargado">Encargado:</label>
            <select id="filtroEncargado" class="filtro-select">
                <option value="">Todos</option>
                <?php 
                $encargados = $metas->obtenerEncargadosUnicos();
                foreach($encargados as $encargado) {
                    echo "<option value='" . htmlspecialchars($encargado) . "'>" . htmlspecialchars($encargado) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="filtro-grupo">
            <label for="filtroMes">Mes:</label>
            <select id="filtroMes" class="filtro-select">
                <option value="">Todos</option>
                <?php 
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                $mesesUnicos = $metas->obtenerMesesUnicos();
                $anioActual = null;
                
                foreach($mesesUnicos as $fecha) {
                    if($anioActual !== $fecha['anio']) {
                        if($anioActual !== null) {
                            echo "</optgroup>";
                        }
                        echo "<optgroup label='" . $fecha['anio'] . "'>";
                        $anioActual = $fecha['anio'];
                    }
                    echo "<option value='" . $fecha['mes'] . "' data-anio='" . $fecha['anio'] . "'>" 
                         . $meses[$fecha['mes']] 
                         . "</option>";
                }
                if($anioActual !== null) {
                    echo "</optgroup>";
                }
                ?>
            </select>
        </div>
        
        <div class="filtro-grupo" style="display: none;">
            <label for="filtroAnio">Año:</label>
            <select id="filtroAnio" class="filtro-select">
                <?php foreach(array_unique(array_column($mesesUnicos, 'anio')) as $anio): ?>
                    <option value="<?php echo $anio; ?>"><?php echo $anio; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="button" id="limpiarFiltros" class="btn-limpiar">
            <i class="fas fa-undo"></i> Limpiar filtros
        </button>
    </div>

    <style>
.filtros-container {
    display: flex;
    gap: 20px;
    margin: 20px 0;
    flex-wrap: wrap;
    background: #f5f5f5;
    padding: 15px;
    border-radius: 8px;
}

.filtro-grupo {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filtro-select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 150px;
}

.btn-limpiar {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-left: auto;
}

.btn-limpiar:hover {
    background-color: #5a6268;
}
</style>

    <!-- Tabla con encabezados fijos y scroll solo en el cuerpo -->
    <div class="tabla-outer">
        <table class="tabla">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 22%;">Encargado</th>
                    <th style="width: 18%;">Número de Asistentes</th>
                    <th style="width: 32%;">Fecha de la Charla</th>
                    <th style="width: 20%;">Acciones</th>
                </tr>
            </thead>
        </table>
        <div class="tabla-scroll">
            <table class="tabla">
                <tbody>
                    <?php foreach ($visitas as $visita): ?>
                    <tr>
                        <td style="width: 8%;"><?php echo htmlspecialchars($visita['id_visita']); ?></td>
                        <td style="width: 22%;"><?php echo htmlspecialchars($visita['encargado']); ?></td>
                        <td style="width: 18%;"><?php echo htmlspecialchars($visita['numAsistentes']); ?></td>
                        <td style="width: 32%;"><?php echo formatearFecha($visita['fechaCharla']); ?></td>
                        <td style="width: 20%;">
                            <div class="action-buttons">
                                <button class="btn-icon edit" onclick="editVisita(<?php echo htmlspecialchars(json_encode($visita)); ?>)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id_visita" value="<?php echo $visita['id_visita']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn-icon delete" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
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
    
    <!-- Gráficas existentes -->
    <div class="chart-container" style="height: 400px;">
        <h2>Ranking de Encargados</h2>
        <canvas id="rankingChart"></canvas>
    </div>
    
    <div class="chart-container" style="height: 400px;">
        <h2>Visitas por Semana</h2>
        <canvas id="semanalChart"></canvas>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <a href="reporte_visitas.php" target="_blank" class="btn btn-primary">Descargar Reporte Completo en PDF</a>
    </div>
    
    
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

    // Declarar variables globales para evitar re-instanciación múltiple de gráficos
    let rankingChartInstance = null;
    let semanalChartInstance = null;

    function initCharts() {
        // Ranking de Encargados
        const ctxRanking = document.getElementById('rankingChart').getContext('2d');
        if(rankingChartInstance){ rankingChartInstance.destroy(); }
        rankingChartInstance = new Chart(ctxRanking, {
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
                scales: { y: { beginAtZero: true } }
            }
        });

        // Visitas por Semana con configuración mejorada
        const ctxSemanal = document.getElementById('semanalChart').getContext('2d');
        if(semanalChartInstance){ semanalChartInstance.destroy(); }
        semanalChartInstance = new Chart(ctxSemanal, {
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
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Función para actualizar las gráficas cuando cambie el tamaño de la ventana
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            initCharts();
        }, 250);
    });

    // Inicializar los gráficos cuando la ventana esté completamente cargada
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initCharts, 100);
    });

    // Función para actualizar la tabla según los filtros
    async function actualizarTabla() {
        try {
            const filtros = {
                orden: document.getElementById('ordenRegistros').value,
                limite: document.getElementById('limiteRegistros').value,
                encargado: document.getElementById('filtroEncargado').value,
                mes: document.getElementById('filtroMes').value,
                anio: document.getElementById('filtroAnio').value
            };

            const response = await fetch('obtener_visitas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(filtros)
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();
            const tbody = document.querySelector('.tabla tbody');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="5" style="text-align: center;">No se encontraron registros</td>`;
                tbody.appendChild(tr);
                return;
            }

            data.forEach(visita => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="width: 8%;">${visita.id_visita}</td>
                    <td style="width: 22%;">${visita.encargado}</td>
                    <td style="width: 18%;">${visita.numAsistentes}</td>
                    <td style="width: 32%;">${formatearFecha(visita.fechaCharla)}</td>
                    <td style="width: 20%;">
                        <div class="action-buttons">
                            <button class="btn-icon edit" onclick='editVisita(${JSON.stringify(visita)})' title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_visita" value="${visita.id_visita}">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error:', error);
            alert('Ocurrió un error al actualizar la tabla');
        }
    }

    // Función para limpiar filtros
    function limpiarFiltros() {
        document.getElementById('ordenRegistros').value = 'DESC';
        document.getElementById('limiteRegistros').value = '30';
        document.getElementById('filtroEncargado').value = '';
        document.getElementById('filtroMes').value = '';
        document.getElementById('filtroAnio').value = new Date().getFullYear().toString();
        actualizarTabla();
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        actualizarTabla();
        
        // Agregar event listeners para los filtros
        document.querySelectorAll('.filtro-select').forEach(select => {
            select.addEventListener('change', actualizarTabla);
        });

        // Event listener para el botón de limpiar
        document.getElementById('limpiarFiltros').addEventListener('click', limpiarFiltros);
    });

    // Modificar el event listener del filtro de mes
    document.getElementById('filtroMes').addEventListener('change', function() {
        const mesSelect = document.getElementById('filtroMes');
        const anioSelect = document.getElementById('filtroAnio');
        
        if(mesSelect.value) {
            const selectedOption = mesSelect.options[mesSelect.selectedIndex];
            const anio = selectedOption.getAttribute('data-anio');
            anioSelect.value = anio;
        }
        
        actualizarTabla();
    });

    // Función auxiliar para formatear fecha
    function formatearFecha(fecha) {
        const meses = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];
        
        const date = new Date(fecha);
        const dia = date.getDate();
        const mes = meses[date.getMonth()];
        const anio = date.getFullYear();
        const hora = date.toLocaleTimeString('es-ES', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        }).toLowerCase();
        
        return `${dia} de ${mes} ${anio}<br>${hora}`;
    }
</script>
