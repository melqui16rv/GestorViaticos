<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/metodosGestor.php';

requireRole(['2']);
$miClaseG = new gestor();

// Leer filtros desde cookies si existen, si no, usar GET, si no, valores por defecto
function getFiltroCookie($nombre, $default = '') {
    return isset($_COOKIE[$nombre]) ? $_COOKIE[$nombre] : $default;
}

$documento = isset($_GET['documento']) ? $_GET['documento'] : getFiltroCookie('filtroGestor_documento', '');
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : getFiltroCookie('filtroGestor_nombre', '');
$cdp = isset($_GET['cdp']) ? $_GET['cdp'] : getFiltroCookie('filtroGestor_cdp', '');
$crp = isset($_GET['crp']) ? $_GET['crp'] : getFiltroCookie('filtroGestor_crp', '');
$mes = isset($_GET['mes']) ? $_GET['mes'] : getFiltroCookie('filtroGestor_mes', '');
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : getFiltroCookie('filtroGestor_fechaInicio', '');
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : getFiltroCookie('filtroGestor_fechaFin', '');
$registrosPorPagina = getFiltroCookie('filtroGestor_registrosPorPagina', '10');
$offset = 0;
$limit = ($registrosPorPagina === 'todos') ? 999999 : intval($registrosPorPagina);

$mesesDisponibles = $miClaseG->obtenerMesesDisponibles();

$datosSaldos = $miClaseG->obtenerSaldosAsignadosConFechas(
    $documento,
    $nombre,
    $cdp,
    $crp,
    $mes,
    $fechaInicio,
    $fechaFin,
    $limit,
    $offset
);

if (empty($datosSaldos)) {
    error_log("No se encontraron datos para los filtros aplicados.");
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor - Saldos Asignados</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/gestor/index_gestor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

<div class="contenedor" style="min-height: 100vh; display: flex; flex-direction: column;">
    <div class="contenido" style="flex: 1;">
        <div class="contenedorStandar">
            <div class="filtrosContenedor">
                <div id="filtros">
                    <form id="filtroForm" method="GET" action="" onsubmit="return false;">
                        <div class="filtro-grupo">
                            <label for="documento">Documento</label>
                            <input type="text" id="documento" name="documento" value="<?php echo htmlspecialchars($documento); ?>" placeholder="Ej: 1073672380" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Ej: Juan Felipe Prado" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="cdp">CDP</label>
                            <input type="text" id="cdp" name="cdp" value="<?php echo htmlspecialchars($cdp); ?>" placeholder="Ejemplo: 125" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="crp">CRP</label>
                            <input type="text" id="crp" name="crp" value="<?php echo htmlspecialchars($crp); ?>" placeholder="Código CRP" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="mes">Mes</label>
                            <select id="mes" name="mes" class="filtro-dinamico">
                                <option value="">Todos los meses</option>
                                <?php foreach ($mesesDisponibles as $mesOpt): ?>
                                    <option value="<?php echo htmlspecialchars($mesOpt['mes']); ?>" <?php echo ($mesOpt['mes'] == $mes) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mesOpt['nombre_mes']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filtro-grupo">
                            <label for="fechaInicio">Fecha Inicio</label>
                            <input type="date" id="fechaInicio" name="fechaInicio" value="<?php echo htmlspecialchars($fechaInicio); ?>" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="fechaFin">Fecha Fin</label>
                            <input type="date" id="fechaFin" name="fechaFin" value="<?php echo htmlspecialchars($fechaFin); ?>" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="registrosPorPagina">N° Registros</label>
                            <select id="registrosPorPagina" name="registrosPorPagina" class="filtro-dinamico">
                                <option value="10" <?php echo ($registrosPorPagina=='10') ? 'selected' : ''; ?>>10</option>
                                <option value="20" <?php echo ($registrosPorPagina=='20') ? 'selected' : ''; ?>>20</option>
                                <option value="40" <?php echo ($registrosPorPagina=='40') ? 'selected' : ''; ?>>40</option>
                                <option value="60" <?php echo ($registrosPorPagina=='60') ? 'selected' : ''; ?>>60</option>
                                <option value="todos" <?php echo ($registrosPorPagina=='todos') ? 'selected' : ''; ?>>Todos</option>
                            </select>
                        </div>
                        <div class="filtro-botones">
                            <button type="button" id="limpiarFiltros" style="margin-top: 10px; margin-left: 12px;">
                                <i class="fas fa-times"></i>
                            </button>
                            <button id="cargarMas" style="margin-top: 10px; margin-left: 12px;">+ Registros</button>
                        </div>
                    </form>
                    <div id="filtros-activos"></div>
                </div>
            </div>
            <div class="contenderDeTabla">
                <div class="contendor_tabla">
                    <table border="1" id="tablaSaldosAsignados" class="tablaBusqueda">
                        <thead>
                            <tr>
                                <th style="display: none;">ID Saldo</th>
                                <th>Persona Viaticada</th>
                                <th>Fechas Ejecución<br>Viatico</th>
                                <th>Fecha Sugeriada<br>de Pago</th>
                                <th>Saldo<br>Asignado</th>
                                <th>Códigos Asociados</th>
                                <th>Información<br>Detallada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($datosSaldos)): ?>
                                <?php foreach ($datosSaldos as $row): ?>
                                    <tr data-id-saldo="<?php echo htmlspecialchars($row['ID_SALDO']); ?>">
                                        <td style="display: none;"><?php echo htmlspecialchars($row['ID_SALDO']); ?></td>
                                        <td>
                                            <span class="multi-line"><?php echo htmlspecialchars($row['NOMBRE_PERSONA']); ?></span>
                                            <span class="multi-line"><?php echo htmlspecialchars($row['DOCUMENTO_PERSONA']); ?></span>
                                        </td>
                                        <td>
                                            <span class="multi-line">Inicio: <?php echo htmlspecialchars($row['FECHA_INICIO']); ?></span>
                                            <span class="multi-line">Fin: <?php echo htmlspecialchars($row['FECHA_FIN']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['FECHA_PAGO'] ?? 'Sin registro'); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($row['SALDO_ASIGNADO'], 2, ',', '.')); ?></td>
                                        <td>
                                            <span class="multi-line">CDP: <?php echo htmlspecialchars($row['Numero_Documento_CDP']); ?></span>
                                            <span class="multi-line">CRP: <?php echo htmlspecialchars($row['Numero_Documento_CRP']); ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="control/asignacion.php?id_saldo=<?php echo urlencode($row['ID_SALDO']); ?>" 
                                            class="btn-detalle" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="filament-action-card">
                <a href="control/insert_saldo_asiganado.php" class="filament-button filament-button-action">
                    Agregar Asiganación
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let offset = 0;
    let limit = <?php echo json_encode($limit); ?>;
    let cargando = false;

    // --- Función para establecer cookies ---
    function setCookie(name, value, days = 30) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value || "") + expires + "; path=/";
    }

    // --- Guardar filtros en cookies cada vez que cambian ---
    function guardarFiltrosEnCookies() {
        setCookie('filtroGestor_documento', $("#documento").val());
        setCookie('filtroGestor_nombre', $("#nombre").val());
        setCookie('filtroGestor_cdp', $("#cdp").val());
        setCookie('filtroGestor_crp', $("#crp").val());
        setCookie('filtroGestor_mes', $("#mes").val());
        setCookie('filtroGestor_fechaInicio', $("#fechaInicio").val());
        setCookie('filtroGestor_fechaFin', $("#fechaFin").val());
        setCookie('filtroGestor_registrosPorPagina', $("#registrosPorPagina").val());
    }

    // --- Limpiar cookies de filtros ---
    function limpiarCookiesFiltros() {
        setCookie('filtroGestor_documento', '', -1);
        setCookie('filtroGestor_nombre', '', -1);
        setCookie('filtroGestor_cdp', '', -1);
        setCookie('filtroGestor_crp', '', -1);
        setCookie('filtroGestor_mes', '', -1);
        setCookie('filtroGestor_fechaInicio', '', -1);
        setCookie('filtroGestor_fechaFin', '', -1);
        setCookie('filtroGestor_registrosPorPagina', '', -1);
    }

    // --- Leer cookies ---
    function leerCookie(nombre) {
        let nameEQ = nombre + "=";
        let ca = document.cookie.split(';');
        for(let i=0;i < ca.length;i++) {
            let c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length,c.length));
        }
        return null;
    }

    // --- Setear filtros desde cookies al cargar la página ---
    function setFiltrosDesdeCookies() {
        let filtros = [
            {id: 'documento', cookie: 'filtroGestor_documento'},
            {id: 'nombre', cookie: 'filtroGestor_nombre'},
            {id: 'cdp', cookie: 'filtroGestor_cdp'},
            {id: 'crp', cookie: 'filtroGestor_crp'},
            {id: 'mes', cookie: 'filtroGestor_mes'},
            {id: 'fechaInicio', cookie: 'filtroGestor_fechaInicio'},
            {id: 'fechaFin', cookie: 'filtroGestor_fechaFin'},
            {id: 'registrosPorPagina', cookie: 'filtroGestor_registrosPorPagina'}
        ];
        filtros.forEach(function(f) {
            let val = leerCookie(f.cookie);
            if(val !== null && typeof val !== 'undefined' && val !== '') {
                $("#" + f.id).val(val);
            }
        });
        // Ajustar limit y offset según cookie de registrosPorPagina
        let valReg = leerCookie('filtroGestor_registrosPorPagina');
        if(valReg) {
            limit = valReg === 'todos' ? 999999 : parseInt(valReg);
            offset = 0;
            if(valReg === 'todos') $("#cargarMas").hide();
            else $("#cargarMas").show();
        } else {
            limit = 10;
            offset = 0;
        }
    }

    // --- Actualizar filtros activos ---
    function actualizarFiltrosActivos() {
        const filtros = {
            'Documento': $("#documento").val(),
            'Nombre': $("#nombre").val(),
            'CDP': $("#cdp").val(),
            'CRP': $("#crp").val(),
            'Mes': $("#mes option:selected").text() !== 'Todos los meses' && $("#mes").val() !== '' ? $("#mes option:selected").text() : '',
            'Fecha Inicio': $("#fechaInicio").val(),
            'Fecha Fin': $("#fechaFin").val(),
            'N° Registros': $("#registrosPorPagina").val() !== '10' ? $("#registrosPorPagina").val() : ''
        };

        let filtrosHTML = '<strong>Filtros activos:</strong> ';
        let hayFiltros = false;

        for (const [key, value] of Object.entries(filtros)) {
            if (value) {
                filtrosHTML += `<span class="filtro-tag">${key}: ${value}</span>`;
                hayFiltros = true;
            }
        }

        $("#filtros-activos").html(hayFiltros ? filtrosHTML : '');
    }

    // --- Buscar y actualizar tabla ---
    function buscarDinamico(resetOffset = true) {
        guardarFiltrosEnCookies();
        if(resetOffset) offset = 0;
        const filtros = {
            documento: $("#documento").val(),
            nombre: $("#nombre").val(),
            cdp: $("#cdp").val(),
            crp: $("#crp").val(),
            mes: $("#mes").val(),
            fechaInicio: $("#fechaInicio").val(),
            fechaFin: $("#fechaFin").val(),
            registrosPorPagina: $("#registrosPorPagina").val(),
            offset: offset,
            limit: limit
        };

        $.ajax({
            url: '',
            method: 'GET',
            data: filtros,
            success: function (response) {
                const nuevaTabla = $(response).find('#tablaSaldosAsignados tbody').html();
                $('#tablaSaldosAsignados tbody').html(nuevaTabla);
                offset = limit;
                if(limit === 999999) {
                    $("#cargarMas").hide();
                } else {
                    $("#cargarMas").show();
                }
            },
            error: function () {
                alert('Error al realizar la búsqueda.');
            }
        });
        actualizarFiltrosActivos();
    }

    // --- Cargar más registros ---
    $("#cargarMas").on("click", function(e){
        e.preventDefault();
        if (cargando) return;
        cargando = true;
        offset += limit;
        const filtros = {
            documento: $("#documento").val(),
            nombre: $("#nombre").val(),
            cdp: $("#cdp").val(),
            crp: $("#crp").val(),
            mes: $("#mes").val(),
            fechaInicio: $("#fechaInicio").val(),
            fechaFin: $("#fechaFin").val(),
            registrosPorPagina: $("#registrosPorPagina").val(),
            offset: offset,
            limit: limit
        };

        $.ajax({
            url: '',
            method: 'GET',
            data: filtros,
            success: function (response) {
                const nuevaTabla = $(response).find('#tablaSaldosAsignados tbody').html();
                if (nuevaTabla.trim() === "") {
                    alert("No se encontraron más registros.");
                    $("#cargarMas").hide();
                } else {
                    const registrosNuevos = $(response).find('#tablaSaldosAsignados tbody tr');
                    registrosNuevos.each(function () {
                        const idSaldo = $(this).data('id-saldo');
                        if ($(`#tablaSaldosAsignados tbody tr[data-id-saldo="${idSaldo}"]`).length === 0) {
                            $('#tablaSaldosAsignados tbody').append($(this));
                        }
                    });
                }
            },
            error: function () {
                alert('Error al cargar más registros.');
            },
            complete: function () {
                cargando = false;
            }
        });
    });

    // --- Limpiar filtros y cookies ---
    $("#limpiarFiltros").on("click", function(){
        limpiarCookiesFiltros();
        $("#documento, #nombre, #cdp, #crp, #mes, #fechaInicio, #fechaFin").val('');
        $("#registrosPorPagina").val('10');
        limit = 10;
        offset = 0;
        buscarDinamico();
    });

    // --- Evento para cambio en registros por página ---
    $("#registrosPorPagina").on('change', function() {
        let valorSeleccionado = $(this).val();
        limit = valorSeleccionado === 'todos' ? 999999 : parseInt(valorSeleccionado);
        offset = 0;
        if(valorSeleccionado === 'todos') {
            $("#cargarMas").hide();
        } else {
            $("#cargarMas").show();
        }
        buscarDinamico();
    });

    // --- Evento para cualquier filtro dinámico ---
    $(".filtro-dinamico").on('change keyup', function() {
        buscarDinamico();
    });

    // --- Inicialización al cargar la página ---
    setFiltrosDesdeCookies();
    actualizarFiltrosActivos();
    buscarDinamico();

    // --- Estilos CSS inline para los filtros activos ---
    $("<style>")
        .prop("type", "text/css")
        .html(`
            #filtros-activos { margin-top: 10px; }
            .filtro-tag {
                background: #e9ecef;
                padding: 3px 8px;
                border-radius: 4px;
                margin: 0 5px;
                display: inline-block;
            }
        `)
        .appendTo("head");
});
</script>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>
