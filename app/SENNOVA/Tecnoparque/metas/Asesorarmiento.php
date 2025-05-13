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
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/metas.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<div class="dashboard-container">
    <h2 class="text-xl font-bold mb-4">Dashboard de Asesoramiento</h2>
    <a href="javascript:void(0);" id="toggleFormButtonAso" class="actualizar-tabla-link inline-block">
        <button type="button" class="actualizar-tabla-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span id="toggleFormButtonTextAso">Agregar Asesoramiento</span>
        </button>
    </a>
    <form id="formAso" method="POST" class="formulario" style="display: none;">
        <input type="hidden" name="action" id="actionAso" value="create">
        <input type="hidden" name="id" id="idAso">
        <div class="form-group">
            <label for="tipoAso">Tipo:</label>
            <select id="tipoAso" name="tipo" required>
                <option value="Asociaciones">Asociaciones</option>
                <option value="Cooperativa">Cooperativa</option>
            </select>
        </div>
        <div class="form-group">
            <label for="encargadoAso">Encargado:</label>
            <input type="text" id="encargadoAso" name="encargado" required>
        </div>
        <div class="form-group">
            <label for="entidadAso">Entidad Impactada:</label>
            <input type="text" id="entidadAso" name="entidad" required>
        </div>
        <div class="form-group">
            <label for="fechaAso">Fecha de Asesoramiento:</label>
            <input type="datetime-local" id="fechaAso" name="fecha" required>
        </div>
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="reset" class="btn btn-secondary" onclick="resetFormAso()">Cancelar</button>
        </div>
    </form>
    <div class="tabla-outer" style="margin-top: 20px;">
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
            <tbody id="tbodyAsesoramientos">
                <!-- AJAX -->
            </tbody>
        </table>
    </div>
    <div class="indicadores">
        <div class="indicador">
            <h3>Total Asesoramientos</h3>
            <p id="indicadorTotalAso">0</p>
        </div>
        <div class="indicador">
            <h3>Por Tipo</h3>
            <p id="indicadorPorTipoAso"></p>
        </div>
        <div class="indicador">
            <h3>Por Encargado</h3>
            <p id="indicadorPorEncargadoAso"></p>
        </div>
    </div>
    <div class="chart-container" style="height: 350px;">
        <h2>Asesoramientos por Tipo</h2>
        <canvas id="graficaAsoTipo"></canvas>
    </div>
    <div class="chart-container" style="height: 350px;">
        <h2>Asesoramientos por Encargado</h2>
        <canvas id="graficaAsoEncargado"></canvas>
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

function cargarAsesoramientos() {
    $.ajax({
        url: 'obtener_asesoramientos.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({}),
        success: function(resp) {
            if (resp.success) {
                // Tabla
                let html = '';
                resp.data.forEach(a => {
                    html += `<tr>
                        <td>${a.id_asesoramiendo}</td>
                        <td>${a.tipo}</td>
                        <td>${a.encargadoAsesoramiento}</td>
                        <td>${a.nombreEntidadImpacto}</td>
                        <td>${formatearFechaAso(a.fechaAsesoramiento)}</td>
                        <td>
                            <div class='action-buttons'>
                                <button class='btn-icon edit' onclick='editAso(${JSON.stringify(a)})' title='Editar'><i class='fas fa-edit'></i></button>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='${a.id_asesoramiendo}'>
                                    <input type='hidden' name='action' value='delete'>
                                    <button type='submit' class='btn-icon delete' title='Eliminar'><i class='fas fa-trash-alt'></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#tbodyAsesoramientos').html(html);
                // Indicadores
                $('#indicadorTotalAso').text(resp.indicadores.total);
                let porTipo = '';
                Object.entries(resp.indicadores.por_tipo).forEach(([tipo, cant]) => {
                    porTipo += `${tipo}: ${cant}<br>`;
                });
                $('#indicadorPorTipoAso').html(porTipo);
                let porEnc = '';
                Object.entries(resp.indicadores.por_encargado).forEach(([enc, cant]) => {
                    porEnc += `${enc}: ${cant}<br>`;
                });
                $('#indicadorPorEncargadoAso').html(porEnc);
                // Gráficas
                renderGraficasAso(resp.indicadores);
            }
        }
    });
}

let graficaAsoTipo = null;
let graficaAsoEncargado = null;
function renderGraficasAso(indicadores) {
    // Por Tipo
    if (graficaAsoTipo) graficaAsoTipo.destroy();
    const ctxTipo = document.getElementById('graficaAsoTipo').getContext('2d');
    graficaAsoTipo = new Chart(ctxTipo, {
        type: 'pie',
        data: {
            labels: Object.keys(indicadores.por_tipo),
            datasets: [{
                data: Object.values(indicadores.por_tipo),
                backgroundColor: ['#60a5fa', '#fbbf24'],
                borderColor: ['#2563eb', '#b45309'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    // Por Encargado
    if (graficaAsoEncargado) graficaAsoEncargado.destroy();
    const ctxEnc = document.getElementById('graficaAsoEncargado').getContext('2d');
    graficaAsoEncargado = new Chart(ctxEnc, {
        type: 'bar',
        data: {
            labels: Object.keys(indicadores.por_encargado),
            datasets: [{
                label: 'Cantidad',
                data: Object.values(indicadores.por_encargado),
                backgroundColor: '#34d399',
                borderColor: '#059669',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

document.getElementById('toggleFormButtonAso').addEventListener('click', function() {
    const form = document.getElementById('formAso');
    const buttonText = document.getElementById('toggleFormButtonTextAso');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        buttonText.textContent = 'Agregar Asesoramiento';
    } else {
        resetFormAso();
    }
});
function editAso(a) {
    document.getElementById('idAso').value = a.id_asesoramiendo;
    document.getElementById('tipoAso').value = a.tipo;
    document.getElementById('encargadoAso').value = a.encargadoAsesoramiento;
    document.getElementById('entidadAso').value = a.nombreEntidadImpacto;
    document.getElementById('fechaAso').value = a.fechaAsesoramiento.replace(' ', 'T');
    document.getElementById('actionAso').value = 'update';
    document.getElementById('formAso').style.display = 'block';
    document.getElementById('toggleFormButtonTextAso').textContent = 'Editar Asesoramiento';
}
function resetFormAso() {
    document.getElementById('formAso').reset();
    document.getElementById('idAso').value = '';
    document.getElementById('actionAso').value = 'create';
    document.getElementById('formAso').style.display = 'none';
    document.getElementById('toggleFormButtonTextAso').textContent = 'Agregar Asesoramiento';
}

// Inicializar tabla e indicadores al cargar
$(document).ready(function() {
    cargarAsesoramientos();
});
</script>
