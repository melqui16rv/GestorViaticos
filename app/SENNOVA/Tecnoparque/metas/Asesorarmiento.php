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
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sennova/tecnoparque/asesoramientoStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<div class="dashboard-container" id="dashboardContentAso">
    <div class="stats-card indicadores-asesoramiento" id="statsCardAso">
        <div class="flex flex-wrap gap-6 mb-6">
            <div class="stat-item indicador-asesoramiento">
                <div class="stat-value text-blue-700" id="indicadorTotalAso">0</div>
                <div class="stat-label">Total Asesoramientos</div>
            </div>
            <div class="stat-item indicador-asesoramiento">
                <div class="stat-value text-green-700" id="indicadorTipoAso1">0</div>
                <div class="stat-label">Asociaciones</div>
                <div class="stat-meta" id="metaAsociaciones" style="font-size:0.95rem;color:#2563eb;"></div>
            </div>
            <div class="stat-item indicador-asesoramiento">
                <div class="stat-value text-yellow-700" id="indicadorTipoAso2">0</div>
                <div class="stat-label">Cooperativa</div>
                <div class="stat-meta" id="metaCooperativa" style="font-size:0.95rem;color:#b59f00;"></div>
            </div>
        </div>
    </div>
    <div class="tabla-card mb-8" id="tablaCardAso">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Tabla de Asesoramientos</h2>
            
            <a href="javascript:void(0);" id="toggleFormButtonAso" class="actualizar-tabla-link inline-block">
                <button type="button" class="actualizar-tabla-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="toggleFormButtonTextAso">Agregar Asesoramiento</span>
                </button>
            </a>

        </div>
        
        <form id="formAso" method="POST" class="formulario formulario-asesoramiento" style="display: none;">
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

        <div class="grafica-table-wrapper">
            <table class="styled-table" id="styledTableAso">
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
    </div>
    <!-- Sección de información relevante -->
    <div class="info-extra mb-6" id="infoExtraAso" style="display: flex; gap: 2rem;">
        <div class="stat-item" style="flex:1;">
            <div class="stat-label font-semibold">Última asesoría registrada</div>
            <div id="ultimaAsesoriaAso" style="font-size:1rem;"></div>
        </div>
        <div class="stat-item" style="flex:1;">
            <div class="stat-label font-semibold">Avance de la meta total</div>
            <div style="margin-top: 0.5rem;">
                <div class="barra-meta-asesoramiento">
                    <div id="barraProgresoAso" class="barra-progreso-aso"></div>
                </div>
                <div id="porcentajeMetaAso" style="margin-top: 0.5rem; font-size: 1rem; font-weight: 500;"></div>
            </div>
        </div>
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
                // Tabla
                let html = '';
                let tipo1 = 0, tipo2 = 0;
                let ultimaFecha = null;
                let encargadoCount = {};
                let encargadoTop = '';
                let maxCount = 0;
                resp.data.forEach(a => {
                    if(a.tipo === 'Asociaciones') tipo1++;
                    if(a.tipo === 'Cooperativa') tipo2++;
                    // Calcular última fecha
                    if (!ultimaFecha || new Date(a.fechaAsesoramiento) > new Date(ultimaFecha)) {
                        ultimaFecha = a.fechaAsesoramiento;
                    }
                    // Contar por encargado
                    encargadoCount[a.encargadoAsesoramiento] = (encargadoCount[a.encargadoAsesoramiento] || 0) + 1;
                    if (encargadoCount[a.encargadoAsesoramiento] > maxCount) {
                        maxCount = encargadoCount[a.encargadoAsesoramiento];
                        encargadoTop = a.encargadoAsesoramiento;
                    }
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
                $('#indicadorTipoAso1').text(tipo1);
                $('#indicadorTipoAso2').text(tipo2);

                // Indicadores de meta
                let metaAsocRestante = Math.max(0, 5 - tipo1);
                let metaCoopRestante = Math.max(0, 1 - tipo2);
                $('#metaAsociaciones').text(metaAsocRestante === 0 ? 'Meta alcanzada' : `Faltan ${metaAsocRestante} para la meta (5)`);
                $('#metaCooperativa').text(metaCoopRestante === 0 ? 'Meta alcanzada' : `Falta ${metaCoopRestante} para la meta (1)`);

                // Información relevante
                $('#ultimaAsesoriaAso').text(
                    ultimaFecha ? formatearFechaAso(ultimaFecha).replace('<br>', ' ') : 'Sin registros'
                );

                // Barra de progreso de la meta total (6 asesorías)
                let porcentaje = Math.min(100, Math.round((tipo1 + tipo2) / 6 * 100));
                $('#barraProgresoAso').css('width', porcentaje + '%');
                $('#porcentajeMetaAso').text(`${porcentaje}% (${tipo1 + tipo2} de 6 asesorías)`);
            }
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
    cargarAsesoramientosAso();
});
</script>
