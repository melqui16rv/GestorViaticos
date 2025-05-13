<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';

$metas = new metas_tecnoparque();

// CRUD exclusivo para asesoramiento (solo para crear/editar/eliminar, la tabla se llena por AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $metas->insertarAsesoramiento($_POST['tipo'], $_POST['encargado'], $_POST['entidad'], $_POST['fecha']);
    } elseif ($action === 'update') {
        $metas->actualizarAsesoramiento($_POST['id'], $_POST['tipo'], $_POST['encargado'], $_POST['entidad'], $_POST['fecha']);
    } elseif ($action === 'delete') {
        $metas->eliminarAsesoramiento($_POST['id']);
    }
    // Setear cookie para que index.php muestre la vista de asesoramiento tras recargar
    setcookie('tecnoparque_metas_vista', 'asesoramiento', time() + 60*60*24*30, '/');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Función para formatear fecha en JS (se usará en el frontend)
function formatearFechaAso($fecha) {
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
?>
<head>
<link rel="stylesheet" href="/assets/css/sennova/tecnoparque/metas.css">
<link rel="stylesheet" href="/assets/css/sennova/tecnoparque/asesoramientoStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<div class="dashboard-container" id="dashboardContentAso">
    <div class="indicadores">
        <div class="indicador" style="background-color:#fde2e2;">
            <h3>Meta Asociaciones</h3>
            <p id="aso-metaAsoAsociaciones">0 / 5</p>
        </div>
        <div class="indicador" style="background-color:#fce1a8;">
            <h3>Meta Cooperativa</h3>
            <p id="aso-metaAsoCooperativa">0 / 1</p>
        </div>
    </div>
    <div class="tabla-card mb-8" id="aso-tablaCardAso">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Tabla de Asesoramientos</h2>
            <a href="javascript:void(0);" id="aso-toggleFormButtonAso" class="actualizar-tabla-link inline-block">
                <button type="button" class="actualizar-tabla-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="aso-toggleFormButtonTextAso">Agregar Asesoramiento</span>
                </button>
            </a>
        </div>
        <form id="aso-formAso" method="POST" class="formulario" style="display: none;">
            <input type="hidden" name="action" id="aso-actionAso" value="create">
            <input type="hidden" name="id" id="aso-idAso">
            <div class="form-group">
                <label for="aso-tipoAso">Tipo:</label>
                <select id="aso-tipoAso" name="tipo" required>
                    <option value="Asociaciones">Asociaciones</option>
                    <option value="Cooperativa">Cooperativa</option>
                </select>
            </div>
            <div class="form-group">
                <label for="aso-encargadoAso">Encargado:</label>
                <input type="text" id="aso-encargadoAso" name="encargado" required>
            </div>
            <div class="form-group">
                <label for="aso-entidadAso">Entidad Impactada:</label>
                <input type="text" id="aso-entidadAso" name="entidad" required>
            </div>
            <div class="form-group">
                <label for="aso-fechaAso">Fecha de Asesoramiento:</label>
                <input type="datetime-local" id="aso-fechaAso" name="fecha" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="reset" class="btn btn-secondary" onclick="resetFormAso()">Cancelar</button>
            </div>
        </form>
        <div class="tabla-outer">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Encargado</th>
                        <th>Entidad Impactada</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
            <div class="tabla-scroll">
                <table class="tabla">
                    <tbody id="aso-tbodyAsesoramientos">
                        <!-- AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="flex gap-6 mb-6">
        <div class="stat-item">
            <div class="stat-label font-semibold">Por Tipo</div>
            <div id="aso-indicadorPorTipoAso"></div>
        </div>
        <div class="stat-item">
            <div class="stat-label font-semibold">Por Encargado</div>
            <div id="aso-indicadorPorEncargadoAso"></div>
        </div>
    </div>
    <div class="chart-container mb-6">
        <h2 class="text-xl font-semibold mb-2">Asesoramientos por Tipo</h2>
        <canvas id="aso-graficaAsoTipo"></canvas>
    </div>
    <div class="chart-container mb-6">
        <h2 class="text-xl font-semibold mb-2">Asesoramientos por Encargado</h2>
        <canvas id="aso-graficaAsoEncargado"></canvas>
    </div>
</div>
<script>
function formatearFechaAso(fecha) {
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const d = new Date(fecha.replace(' ', 'T'));
    const dia = d.getDate();
    const mes = meses[d.getMonth()];
    const anio = d.getFullYear();
    let hora = d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
    return `${dia} de ${mes} ${anio}<br>${hora}`;
}

function cargarAsesoramientosAso() {
    $.ajax({
        url: 'obtener_asesoramientos.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({}),
        success: function(resp) {
            if (resp.success) {
                // Solo manipular el tbody de asesoramiento
                let html = '';
                let tipo1 = 0, tipo2 = 0;
                resp.data.forEach(a => {
                    if(a.tipo === 'Asociaciones') tipo1++;
                    if(a.tipo === 'Cooperativa') tipo2++;
                    html += `<tr>
                        <td>${a.id_asesoramiendo}</td>
                        <td>${a.tipo}</td>
                        <td>${a.encargadoAsesoramiento}</td>
                        <td>${a.nombreEntidadImpacto}</td>
                        <td>${formatearFechaAso(a.fechaAsesoramiento)}</td>
                        <td>
                            <div class='aso-action-buttons'>
                                <button class='aso-btn-icon edit' onclick='editAso(${JSON.stringify(a)})' title='Editar'><i class='fas fa-edit'></i></button>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='${a.id_asesoramiendo}'>
                                    <input type='hidden' name='action' value='delete'>
                                    <button type='submit' class='aso-btn-icon delete' title='Eliminar'><i class='fas fa-trash-alt'></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
                });
                // Refuerzo: solo este tbody
                $('#aso-tbodyAsesoramientos').html(html);
                // Indicadores
                $('#aso-indicadorTotalAso').text(resp.indicadores.total);
                $('#aso-indicadorTipoAso1').text(tipo1);
                $('#aso-indicadorTipoAso2').text(tipo2);
                // Indicadores de meta
                $('#aso-metaAsoAsociaciones').text(tipo1 + ' / 5');
                $('#aso-metaAsoCooperativa').text(tipo2 + ' / 1');
                let porTipo = '';
                Object.entries(resp.indicadores.por_tipo).forEach(([tipo, cant]) => {
                    porTipo += `${tipo}: ${cant}<br>`;
                });
                $('#aso-indicadorPorTipoAso').html(porTipo);
                let porEnc = '';
                Object.entries(resp.indicadores.por_encargado).forEach(([enc, cant]) => {
                    porEnc += `${enc}: ${cant}<br>`;
                });
                $('#aso-indicadorPorEncargadoAso').html(porEnc);
                // Gráficas
                renderGraficasAso(resp.indicadores);
            }
        }
    });
}

let graficaAsoTipo = null;
let graficaAsoEncargado = null;
function renderGraficasAso(indicadores) {
    // Colores igual que ProyectosExt
    const azulSuave = 'rgba(59,130,246,0.60)';
    const azulBorde = 'rgba(59,130,246,1)';
    const amarilloSuave = 'rgba(253,224,71,0.65)';
    const amarilloBorde = 'rgba(253,224,71,1)';
    const verdeSuave = 'rgba(34,197,94,0.75)';
    const verdeBorde = 'rgba(34,197,94,1)';
    // Por Tipo
    if (graficaAsoTipo) graficaAsoTipo.destroy();
    const ctxTipo = document.getElementById('aso-graficaAsoTipo').getContext('2d');
    graficaAsoTipo = new Chart(ctxTipo, {
        type: 'pie',
        data: {
            labels: Object.keys(indicadores.por_tipo),
            datasets: [{
                data: Object.values(indicadores.por_tipo),
                backgroundColor: [azulSuave, amarilloSuave],
                borderColor: [azulBorde, amarilloBorde],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    // Por Encargado
    if (graficaAsoEncargado) graficaAsoEncargado.destroy();
    const ctxEnc = document.getElementById('aso-graficaAsoEncargado').getContext('2d');
    graficaAsoEncargado = new Chart(ctxEnc, {
        type: 'bar',
        data: {
            labels: Object.keys(indicadores.por_encargado),
            datasets: [{
                label: 'Cantidad',
                data: Object.values(indicadores.por_encargado),
                backgroundColor: verdeSuave,
                borderColor: verdeBorde,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

document.getElementById('aso-toggleFormButtonAso').addEventListener('click', function() {
    const form = document.getElementById('aso-formAso');
    const buttonText = document.getElementById('aso-toggleFormButtonTextAso');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        buttonText.textContent = 'Agregar Asesoramiento';
    } else {
        resetFormAso();
    }
});
function editAso(a) {
    document.getElementById('aso-idAso').value = a.id_asesoramiendo;
    document.getElementById('aso-tipoAso').value = a.tipo;
    document.getElementById('aso-encargadoAso').value = a.encargadoAsesoramiento;
    document.getElementById('aso-entidadAso').value = a.nombreEntidadImpacto;
    document.getElementById('aso-fechaAso').value = a.fechaAsesoramiento.replace(' ', 'T');
    document.getElementById('aso-actionAso').value = 'update';
    document.getElementById('aso-formAso').style.display = 'block';
    document.getElementById('aso-toggleFormButtonTextAso').textContent = 'Editar Asesoramiento';
}
function resetFormAso() {
    document.getElementById('aso-formAso').reset();
    document.getElementById('aso-idAso').value = '';
    document.getElementById('aso-actionAso').value = 'create';
    document.getElementById('aso-formAso').style.display = 'none';
    document.getElementById('aso-toggleFormButtonTextAso').textContent = 'Agregar Asesoramiento';
}

// Inicializar tabla e indicadores al cargar
$(document).ready(function() {
    cargarAsesoramientosAso();
    // Refuerzo: forzar cookie al enviar el formulario
    $('#aso-formAso').on('submit', function(e) {
        document.cookie = 'tecnoparque_metas_vista=asesoramiento; path=/; max-age=' + (60*60*24*30);
    });
});
</script>
