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

$documento = $_GET['documento'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$cdp = $_GET['cdp'] ?? '';
$crp = $_GET['crp'] ?? '';
$mes = $_GET['mes'] ?? '';
$fechaInicio = $_GET['fechaInicio'] ?? '';
$fechaFin = $_GET['fechaFin'] ?? '';
$registrosPorPagina = $_GET['registrosPorPagina'] ?? '10';
$offset = $_GET['offset'] ?? 0;

$mesesDisponibles = $miClaseG->obtenerMesesDisponibles();

$datosSaldos = $miClaseG->obtenerSaldosAsignadosConFechas(
    $documento,
    $nombre,
    $cdp,
    $crp,
    $mes,
    $fechaInicio,
    $fechaFin,
    $registrosPorPagina === 'todos' ? 999999 : (int)$registrosPorPagina,
    (int)$offset
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
                            <input type="text" id="cdp" name="cdp" value="<?php echo isset($_GET['cdp']) ? htmlspecialchars($cdp) : ''; ?>" placeholder="Ejemplo: 125" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="crp">CRP</label>
                            <input type="text" id="crp" name="crp" value="<?php echo htmlspecialchars($crp); ?>" placeholder="Código CRP" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="mes">Mes</label>
                            <select id="mes" name="mes" class="filtro-dinamico">
                                <option value="">Todos los meses</option>
                                <?php foreach ($mesesDisponibles as $mes): ?>
                                    <option value="<?php echo htmlspecialchars($mes['mes']); ?>">
                                        <?php echo htmlspecialchars($mes['nombre_mes']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filtro-grupo">
                            <label for="fechaInicio">Fecha Inicio</label>
                            <input type="date" id="fechaInicio" name="fechaInicio" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="fechaFin">Fecha Fin</label>
                            <input type="date" id="fechaFin" name="fechaFin" class="filtro-dinamico">
                        </div>
                        <div class="filtro-grupo">
                            <label for="registrosPorPagina">N° Registros</label>
                            <select id="registrosPorPagina" name="registrosPorPagina" class="filtro-dinamico">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="40">40</option>
                                <option value="60">60</option>
                                <option value="todos">Todos</option>
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
                                        <td><?php echo htmlspecialchars($row['FECHA_PAGO']); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($row['SALDO_ASIGNADO'], 2, ',', '.')); ?></td>
                                        <td>
                                            <span class="multi-line">CDP: <?php echo htmlspecialchars($row['Numero_Documento_CDP']); ?></span>
                                            <span class="multi-line">CRP: <?php echo htmlspecialchars($row['Numero_Documento_CRP']); ?></span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="control/asignacion.php?id_saldo=<?php echo urlencode($row['ID_SALDO']); ?>" class="ingresarConsumo">+</a>
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
        let limit = 10;
        let cargando = false;

        function buscarDinamico() {
            const filtros = {
                documento: $("#documento").val(),
                nombre: $("#nombre").val(),
                cdp: $("#cdp").val(),
                crp: $("#crp").val(),
                mes: $("#mes").val(),
                fechaInicio: $("#fechaInicio").val(),
                fechaFin: $("#fechaFin").val(),
                offset: 0,
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
                    $("#cargarMas").show();
                },
                error: function () {
                    alert('Error al realizar la búsqueda.');
                }
            });
        }

        function cargarMasRegistros() {
            if (cargando) return;
            cargando = true;

            const filtros = {
                documento: $("#documento").val(),
                nombre: $("#nombre").val(),
                cdp: $("#cdp").val(),
                crp: $("#crp").val(),
                mes: $("#mes").val(),
                fechaInicio: $("#fechaInicio").val(),
                fechaFin: $("#fechaFin").val(),
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
                        offset += limit;
                    }
                },
                error: function () {
                    alert('Error al cargar más registros.');
                },
                complete: function () {
                    cargando = false;
                }
            });
        }

        function limpiarFiltros() {
            $("#documento, #nombre, #cdp, #crp, #mes, #fechaInicio, #fechaFin").val('');
            $("#registrosPorPagina").val('10');
            limit = 10;
            offset = 0;
            buscarDinamico();
        }

        $("#cargarMas").on("click", cargarMasRegistros);
        $("#limpiarFiltros").on("click", limpiarFiltros);
        $("#registrosPorPagina").on("change", function () {
            const valorSeleccionado = $(this).val();
            limit = valorSeleccionado === 'todos' ? 999999 : parseInt(valorSeleccionado);
            offset = 0;
            buscarDinamico();
        });
        $(".filtro-dinamico").on("input change", function () {
            buscarDinamico();
        });

        $(document).on("click", ".ingresarConsumo", function (e) {
            e.preventDefault();
            const idSaldo = $(this).closest("tr").data("id-saldo");
            if (idSaldo) {
                window.location.href = `control/asignacion.php?id_saldo=${idSaldo}`;
            }
        });

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
