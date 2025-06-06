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
$visitas = $metas->obtenerVisitasApre(); // <-- ahora traerá el campo 'nodo' correctamente
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

// Calcular visitas por encargado para la nueva gráfica
$visitasPorEncargado = [];
foreach ($visitas as $v) {
    $encargado = $v['encargado'];
    if (!isset($visitasPorEncargado[$encargado])) {
        $visitasPorEncargado[$encargado] = 0;
    }
    $visitasPorEncargado[$encargado]++;
}
$labelsVisitasEncargado = array_keys($visitasPorEncargado);
$dataVisitasEncargado = array_values($visitasPorEncargado);

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
<!-- Agregar SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<div class="dashboard-container" id="dashboardVisitasApre">
    
    <div class="container mx-auto px-4" style="margin-top:20px;">
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Filtros de Búsqueda</h2>
                <form id="filtroFormVisitasApre" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="ordenRegistrosVisitasApre" class="block text-gray-700 text-sm font-bold mb-2">Orden</label>
                            <select id="ordenRegistrosVisitasApre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline filtro-select">
                                <option value="DESC">Más recientes primero</option>
                                <option value="ASC">Más antiguos primero</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="limiteRegistrosVisitasApre" class="block text-gray-700 text-sm font-bold mb-2">Mostrar</label>
                            <select id="limiteRegistrosVisitasApre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline filtro-select">
                                <option value="30">30 registros</option>
                                <option value="50">50 registros</option>
                                <option value="70">70 registros</option>
                                <option value="">Todos</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="filtroEncargadoVisitasApre" class="block text-gray-700 text-sm font-bold mb-2">Encargado</label>
                            <select id="filtroEncargadoVisitasApre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline filtro-select">
                                <option value="">Todos</option>
                                <?php 
                                $encargados = $metas->obtenerEncargadosUnicos();
                                foreach($encargados as $encargado) {
                                    echo "<option value='" . htmlspecialchars($encargado) . "'>" . htmlspecialchars($encargado) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="filtroMesVisitasApre" class="block text-gray-700 text-sm font-bold mb-2">Mes</label>
                            <select id="filtroMesVisitasApre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline filtro-select">
                                <option value="">Todos</option>
                                <?php 
                                // Definir los nombres de los meses
                                $meses = [
                                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                                ];
                                $mesesUnicos = $metas->obtenerMesesUnicos();
                                $anioActual = null;
                                foreach($mesesUnicos as $fecha) {
                                    if($anioActual !== $fecha['anio']) {
                                        if($anioActual !== null) echo "</optgroup>";
                                        echo "<optgroup label='" . $fecha['anio'] . "'>";
                                        $anioActual = $fecha['anio'];
                                    }
                                    // Solo mostrar si el mes existe en el array de meses
                                    if (isset($meses[$fecha['mes']])) {
                                        echo "<option value='" . $fecha['mes'] . "' data-anio='" . $fecha['anio'] . "'>" 
                                        . $meses[$fecha['mes']] 
                                        . "</option>";
                                    }
                                }
                                if($anioActual !== null) echo "</optgroup>";
                                ?>
                            </select>
                            <!-- Campo oculto para el año -->
                            <input type="hidden" id="filtroAnioVisitasApre" name="filtroAnio" value="">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" id="limpiarFiltrosVisitasApre" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            <i class="fas fa-undo mr-2"></i>Limpiar filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Tabla con encabezados fijos y scroll solo en el cuerpo -->
    <div class="tabla-card">
        <div class="flex justify-end mb-4">
            
            <a href="javascript:void(0);" id="toggleFormButtonVisitasApre" class="actualizar-tabla-link inline-block">
                <button type="button" class="actualizar-tabla-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="toggleFormButtonTextVisitasApre">Agregar Visita</span>
                </button>
            </a>
            
        </div>
        
        <form id="formVisitasApreUnique" method="POST" class="formulario formulario-visitasapre" style="display: none;">
            <input type="hidden" name="action" id="actionVisitasApre" value="create">
            <input type="hidden" name="id_visita" id="id_visitaVisitasApre">
            <div class="form-group">
                <label for="encargadoVisitasApre">Encargado:</label>
                <input type="text" id="encargadoVisitasApre" name="encargado" required>
            </div>
            <div class="form-group">
                <label for="numAsistentesVisitasApre">Número de Asistentes:</label>
                <input type="number" id="numAsistentesVisitasApre" name="numAsistentes" required>
            </div>
            <div class="form-group">
                <label for="fechaCharlaVisitasApre">Fecha de la Charla:</label>
                <input type="datetime-local" id="fechaCharlaVisitasApre" name="fechaCharla" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="reset" class="btn btn-secondary" onclick="resetFormVisitasApre()">Cancelar</button>
            </div>
        </form>
        
        <div class="p-6">
        </div>

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
                <tbody id="tbodyVisitasApre">
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
                                <form class="form-delete-visita" method="POST" style="display:inline;">
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
        <h2>Nivel Impacto X Encargado</h2>
        <canvas id="rankingChartVisitasApre"></canvas>
    </div>

    <!-- Nueva gráfica: Visitas por Encargado -->
    <div class="chart-container" style="height: 400px;">
        <h2>Visitas realizadas X Encargado</h2>
        <canvas id="visitasPorEncargadoChartVisitasApre"></canvas>
    </div>
    
    <div class="chart-container" style="height: 400px;">
        <h2>Visitas por Semana</h2>
        <canvas id="semanalChartVisitasApre"></canvas>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <button id="btnDescargarReportePDF" class="btn btn-primary">
            Descargar Reporte Completo en PDF
        </button>
    </div>
    
    
</div>
    

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- FUNCIONES DE COOKIES ---
    function setCookieFiltro(name, value, days = 30) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value || "") + expires + "; path=/";
    }
    function getCookieFiltro(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(";").shift());
        return null;
    }
    function deleteCookieFiltro(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    // --- IDs reales de los filtros ---
    const filtroIds = [
        { id: 'ordenRegistrosVisitasApre', cookie: 'tecnoparque_visitasapre_orden' },
        { id: 'limiteRegistrosVisitasApre', cookie: 'tecnoparque_visitasapre_limite' },
        { id: 'filtroEncargadoVisitasApre', cookie: 'tecnoparque_visitasapre_encargado' },
        { id: 'filtroMesVisitasApre', cookie: 'tecnoparque_visitasapre_mes' },
        { id: 'filtroAnioVisitasApre', cookie: 'tecnoparque_visitasapre_anio' }
    ];

    // --- Guardar cookies al cambiar filtros ---
    function setupFiltroCookies() {
        filtroIds.forEach(f => {
            const el = document.getElementById(f.id);
            if (el) {
                el.addEventListener('change', function() {
                    setCookieFiltro(f.cookie, el.value, 30);
                });
            }
        });
    }

    // --- Aplicar cookies a los filtros al cargar ---
    function aplicarCookiesAFiltros() {
        filtroIds.forEach(f => {
            const el = document.getElementById(f.id);
            if (el) {
                const val = getCookieFiltro(f.cookie);
                if (val !== null && val !== undefined && val !== "") {
                    el.value = val;
                    // Si es filtroMes, también actualiza filtroAnio si corresponde
                    if (f.id === 'filtroMesVisitasApre') {
                        const selectedOption = el.querySelector(`option[value="${val}"]`);
                        if (selectedOption && document.getElementById('filtroAnioVisitasApre')) {
                            document.getElementById('filtroAnioVisitasApre').value = selectedOption.getAttribute('data-anio') || '';
                        }
                    }
                }
            }
        });
    }

    // --- Limpiar cookies de filtros ---
    function limpiarCookiesFiltros() {
        filtroIds.forEach(f => deleteCookieFiltro(f.cookie));
    }

    // Mostrar/ocultar formulario y cambiar texto del botón
    document.getElementById('toggleFormButtonVisitasApre').addEventListener('click', function() {
        const form = document.getElementById('formVisitasApreUnique');
        const buttonText = document.getElementById('toggleFormButtonTextVisitasApre');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            buttonText.textContent = 'Agregar Visita';
        } else {
            resetFormVisitasApre();
        }
    });

    // Editar visita
    function editVisita(visita) {
        document.getElementById('id_visitaVisitasApre').value = visita.id_visita;
        document.getElementById('encargadoVisitasApre').value = visita.encargado;
        document.getElementById('numAsistentesVisitasApre').value = visita.numAsistentes;
        document.getElementById('fechaCharlaVisitasApre').value = visita.fechaCharla.replace(' ', 'T');
        document.getElementById('actionVisitasApre').value = 'update';
        document.getElementById('formVisitasApreUnique').style.display = 'block';
        document.getElementById('toggleFormButtonTextVisitasApre').textContent = 'Editar Visita';
    }

    function resetFormVisitasApre() {
        document.getElementById('formVisitasApreUnique').reset();
        document.getElementById('id_visitaVisitasApre').value = '';
        document.getElementById('actionVisitasApre').value = 'create';
        document.getElementById('formVisitasApreUnique').style.display = 'none';
        document.getElementById('toggleFormButtonTextVisitasApre').textContent = 'Agregar Visita';
    }

    // Declarar variables globales para evitar re-instanciación múltiple de gráficos
    let rankingChartInstance = null;
    let semanalChartInstance = null;
    let visitasPorEncargadoChartInstance = null; // Nueva variable para la gráfica

    function initCharts() {
        // Ranking de Encargados
        const ctxRanking = document.getElementById('rankingChartVisitasApre').getContext('2d');
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

        // Nueva gráfica: Visitas por Encargado
        const ctxVisitasEncargado = document.getElementById('visitasPorEncargadoChartVisitasApre').getContext('2d');
        if(visitasPorEncargadoChartInstance){ visitasPorEncargadoChartInstance.destroy(); }
        visitasPorEncargadoChartInstance = new Chart(ctxVisitasEncargado, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labelsVisitasEncargado); ?>,
                datasets: [{
                    label: 'Cantidad de Visitas',
                    data: <?php echo json_encode($dataVisitasEncargado); ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.5)',
                    borderColor: 'rgba(255, 159, 64, 1)',
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
        const ctxSemanal = document.getElementById('semanalChartVisitasApre').getContext('2d');
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
            const ordenSelect = document.getElementById('ordenRegistrosVisitasApre');
            const limiteSelect = document.getElementById('limiteRegistrosVisitasApre');
            const encargadoSelect = document.getElementById('filtroEncargadoVisitasApre');
            const mesSelect = document.getElementById('filtroMesVisitasApre');
            
            if (!ordenSelect || !limiteSelect || !encargadoSelect || !mesSelect) {
                console.error('Elementos de filtro no encontrados');
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Cargando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const filtros = {
                orden: ordenSelect.value,
                limite: limiteSelect.value,
                encargado: encargadoSelect.value,
                mes: mesSelect.value,
                anio: mesSelect.options[mesSelect.selectedIndex]?.dataset?.anio || ''
            };

            console.log('Enviando filtros:', filtros); // Debug

            const response = await fetch('obtener_visitas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(filtros)
            });

            const result = await response.json();
            console.log('Respuesta recibida:', result); // Debug

            // Cerrar loading
            Swal.close();

            if (!result.success) {
                throw new Error(result.message || 'Error desconocido');
            }

            actualizarTablaConDatos(result.data);
            actualizarIndicadores(result.indicadores);
            actualizarGraficos(result.indicadores);

        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al actualizar los datos'
            });
        }
    }

    function actualizarTablaConDatos(data) {
        const tbody = document.getElementById('tbodyVisitasApre');
        tbody.innerHTML = '';
        
        if (data.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="5" class="text-center py-4">No se encontraron registros</td>`;
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
                        <form class="form-delete-visita" method="POST" style="display:inline;">
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
    }

    function actualizarIndicadores(indicadores) {
        document.querySelector('.indicador:nth-child(1) p').textContent = indicadores.total_asistentes;
        document.querySelector('.indicador:nth-child(2) p').textContent = indicadores.total_charlas;
        document.querySelector('.indicador:nth-child(3) p').textContent = indicadores.promedio_asistentes;
    }

    function actualizarGraficos(indicadores) {
        // Actualizar gráfica de asistentes por encargado
        if(rankingChartInstance) {
            rankingChartInstance.data.labels = indicadores.encargados;
            rankingChartInstance.data.datasets[0].data = indicadores.asistentes_por_encargado;
            rankingChartInstance.update();
        }
        // Actualizar gráfica de visitas por encargado (sincronizada con los filtros)
        if(visitasPorEncargadoChartInstance && indicadores.visitas_por_encargado_labels && indicadores.visitas_por_encargado_data) {
            visitasPorEncargadoChartInstance.data.labels = indicadores.visitas_por_encargado_labels;
            visitasPorEncargadoChartInstance.data.datasets[0].data = indicadores.visitas_por_encargado_data;
            visitasPorEncargadoChartInstance.update();
        }
    }

    // Modificar el event listener del filtro de mes para actualizar el campo oculto de año
    document.getElementById('filtroMesVisitasApre').addEventListener('change', function() {
        const mesSelect = document.getElementById('filtroMesVisitasApre');
        const anioSelect = document.getElementById('filtroAnioVisitasApre');
        if(mesSelect.value) {
            const selectedOption = mesSelect.options[mesSelect.selectedIndex];
            const anio = selectedOption.getAttribute('data-anio');
            if(anioSelect) anioSelect.value = anio;
        } else {
            if(anioSelect) anioSelect.value = '';
        }
        actualizarTabla();
    });

    // Función para limpiar filtros
    function limpiarFiltros() {
        // Verificar que los elementos existan antes de modificarlos
        const elementos = {
            'ordenRegistrosVisitasApre': 'DESC',
            'limiteRegistrosVisitasApre': '30',
            'filtroEncargadoVisitasApre': '',
            'filtroMesVisitasApre': ''
        };

        for (const [id, valor] of Object.entries(elementos)) {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.value = valor;
            }
        }

        const anioSelect = document.getElementById('filtroAnioVisitasApre');
        if (anioSelect) {
            anioSelect.value = new Date().getFullYear().toString();
        }

        actualizarTabla();
        limpiarCookiesFiltros();
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // --- Aplicar cookies a los filtros antes de inicializar la tabla ---
        aplicarCookiesAFiltros();
        // Verificar que los elementos existan antes de agregar los event listeners
        const selects = document.querySelectorAll('.filtro-select');
        if (selects) {
            selects.forEach(select => {
                select.addEventListener('change', actualizarTabla);
            });
        }

        const btnLimpiar = document.getElementById('limpiarFiltrosVisitasApre');
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', limpiarFiltros);
        }

        const mesSelect = document.getElementById('filtroMesVisitasApre');
        if (mesSelect) {
            mesSelect.addEventListener('change', function() {
                const anioSelect = document.getElementById('filtroAnioVisitasApre');
                if (mesSelect.value && anioSelect) {
                    const selectedOption = mesSelect.options[mesSelect.selectedIndex];
                    const anio = selectedOption.getAttribute('data-anio');
                    if (anio) {
                        anioSelect.value = anio;
                    }
                }
                actualizarTabla();
            });
        }

        // Inicializar tabla
        actualizarTabla();
        setupFiltroCookies();
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

    // Interceptar submit del formulario para AJAX
    document.getElementById('formVisitasApreUnique').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        $.ajax({
            url: 'VisitasApre.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                resetFormVisitasApre();
                actualizarTabla();
            }
        });
    });

    // Interceptar submit de eliminar para AJAX
    $(document).on('submit', '.form-delete-visita', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        $.ajax({
            url: 'VisitasApre.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                actualizarTabla();
            }
        });
    });

    document.getElementById('btnDescargarReportePDF').addEventListener('click', function() {
        if (window.Swal) {
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espere unos segundos',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
        }
        fetch('control/reporte_visitas.php', { method: 'GET' })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text || 'No se pudo generar el PDF'); });
            }
            return response.blob();
        })
        .then(blob => {
            if (window.Swal) Swal.close();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'reporte_metas_tecnoparque.pdf';
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                window.URL.revokeObjectURL(url);
                a.remove();
            }, 1000);
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Descarga iniciada',
                    text: 'El PDF se está descargando.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            if (window.Swal) Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al generar el PDF'
            });
        });
    });
</script>

<!-- Agregar SweetAlert2 para mensajes más amigables -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
