<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/planeacion/metodosGestor.php';

requireRole(['3']);
$miClaseG = new planeacion();

// Obtener valores desde cookies o GET
$numeroDocumento = isset($_GET['numeroDocumento']) ? $_GET['numeroDocumento'] : 
                  (isset($_COOKIE['filtro_op_numeroDocumento']) ? $_COOKIE['filtro_op_numeroDocumento'] : '');
$estado = isset($_GET['estado']) ? $_GET['estado'] : 
         (isset($_COOKIE['filtro_op_estado']) ? $_COOKIE['filtro_op_estado'] : 'Todos');
$beneficiario = isset($_GET['beneficiario']) ? $_GET['beneficiario'] : 
               (isset($_COOKIE['filtro_op_beneficiario']) ? $_COOKIE['filtro_op_beneficiario'] : '');
$mes = isset($_GET['mes']) ? $_GET['mes'] : 
      (isset($_COOKIE['filtro_op_mes']) ? $_COOKIE['filtro_op_mes'] : '');
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : 
              (isset($_COOKIE['filtro_op_fechaInicio']) ? $_COOKIE['filtro_op_fechaInicio'] : '');
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : 
           (isset($_COOKIE['filtro_op_fechaFin']) ? $_COOKIE['filtro_op_fechaFin'] : '');
$registrosPorPagina = isset($_COOKIE['filtro_op_registrosPorPagina']) ? $_COOKIE['filtro_op_registrosPorPagina'] : '10';

// Asegurar que las cookies se establezcan incluso en la carga inicial
if (!empty($numeroDocumento)) setcookie('filtro_op_numeroDocumento', $numeroDocumento, time() + (86400 * 30), '/');
if (!empty($estado)) setcookie('filtro_op_estado', $estado, time() + (86400 * 30), '/');
if (!empty($beneficiario)) setcookie('filtro_op_beneficiario', $beneficiario, time() + (86400 * 30), '/');
if (!empty($mes)) setcookie('filtro_op_mes', $mes, time() + (86400 * 30), '/');
if (!empty($fechaInicio)) setcookie('filtro_op_fechaInicio', $fechaInicio, time() + (86400 * 30), '/');
if (!empty($fechaFin)) setcookie('filtro_op_fechaFin', $fechaFin, time() + (86400 * 30), '/');
setcookie('filtro_op_registrosPorPagina', $registrosPorPagina, time() + (86400 * 30), '/');

// Validación y sanitización de filtros
$numeroDocumento = htmlspecialchars(trim($numeroDocumento));
$estado = htmlspecialchars(trim($estado));
$beneficiario = htmlspecialchars(trim($beneficiario));
$mes = filter_var($mes, FILTER_VALIDATE_INT);
$fechaInicio = htmlspecialchars(trim($fechaInicio));
$fechaFin = htmlspecialchars(trim($fechaFin));

// Validar formato de fechas
if (!empty($fechaInicio)) {
    $fechaInicio = date('Y-m-d', strtotime($fechaInicio));
}
if (!empty($fechaFin)) {
    $fechaFin = date('Y-m-d', strtotime($fechaFin));
}

// Array de filtros validados
$filtrosIniciales = [
    'numeroDocumento' => $numeroDocumento,
    'estado' => $estado,
    'beneficiario' => $beneficiario,
    'mes' => $mes,
    'fechaInicio' => $fechaInicio,
    'fechaFin' => $fechaFin
];

// Determinar el límite inicial basado en registrosPorPagina
$limit = ($registrosPorPagina === 'todos') ? 999999 : intval($registrosPorPagina);
$initialData = $miClaseG->obtenerOP($filtrosIniciales, $limit, 0);

// Agregar manejo de Ajax en el mismo archivo
if(isset($_GET['action']) && ($_GET['action'] === 'buscarOP' || $_GET['action'] === 'cargarMas')) {
    header('Content-Type: application/json');
    
    $filtros = [
        'numeroDocumento' => $_GET['numeroDocumento'] ?? '',
        'estado' => $_GET['estado'] ?? 'Todos',
        'beneficiario' => $_GET['beneficiario'] ?? '',
        'mes' => $_GET['mes'] ?? '',
        'fechaInicio' => $_GET['fechaInicio'] ?? '',
        'fechaFin' => $_GET['fechaFin'] ?? ''
    ];
    
    $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
    
    $resultado = $miClaseG->obtenerOP($filtros, $limit, $offset);
    echo json_encode($resultado);
    exit;
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/index_presupuesto.css">
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <div class="contenedor" style="min-height: 100vh; display: flex; flex-direction: column;">
        <div class="contenido" style="flex: 1;">
            <div class="contenedorStandar">
                <div class="filtrosContenedor">
                    <!-- Sección de Filtros -->
                    <div id="filtros">
                        <form id="filtroForm" method="GET" action="historialOP.php" onsubmit="return false;">
                            <div class="filtro-grupo">
                                <label for="numeroDocumento">N° Orden de Pago</label>
                                <input type="text" id="numeroDocumento" name="numeroDocumento"
                                       value="<?php echo htmlspecialchars($numeroDocumento); ?>"
                                       placeholder="Número OP" class="filtro-dinamico">
                            </div>

                            <div class="filtro-grupo">
                                <label for="beneficiario">Beneficiario</label>
                                <input type="text" id="beneficiario" name="beneficiario"
                                       value="<?php echo htmlspecialchars($beneficiario); ?>"
                                       placeholder="Nombre beneficiario" class="filtro-dinamico">
                            </div>
        
                            <div class="filtro-grupo">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado" class="filtro-dinamico">
                                    <option value="Todos" <?php echo ($estado=='Todos') ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Pagada" <?php echo ($estado=='Pagada') ? 'selected' : ''; ?>>Pagada</option>
                                    <option value="Pendiente" <?php echo ($estado=='Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                </select>
                            </div>

                            <div class="filtro-grupo">
                                <label for="mes">Mes</label>
                                <select id="mes" name="mes" class="filtro-dinamico">
                                    <option value="">Todos los meses</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
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
                        <table border="1" id="tablaOP" class="tablaBusqueda">
                            <thead>
                                <tr>
                                    <th>N° OP</th>
                                    <th>Fechas</th>
                                    <th>Estado</th>
                                    <th>Beneficiario</th>
                                    <th>Valor Neto</th>
                                    <th>Información<br>Bancaria</th>
                                    <th>Códigos Asociados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($initialData as $row): ?>
                                    <tr data-documento="<?php echo htmlspecialchars($row['Numero_Documento']); ?>">
                                        <td><?php echo htmlspecialchars($row['Numero_Documento']); ?></td>
                                        <td>
                                            <span class="multi-line">Registro: <?php echo htmlspecialchars($row['Fecha_de_Registro']); ?></span>
                                            <span class="multi-line">Pago: <?php echo htmlspecialchars($row['Fecha_de_Pago']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Nombre_Razon_Social']); ?></td>
                                        <td>
                                            <span class="multi-line"><?php echo '$ ' . number_format($row['Valor_Neto'], 2, '.', ','); ?></span>
                                        </td>
                                        <td>
                                            <span class="multi-line">Estado: <?php echo htmlspecialchars($row['Estado_Cuenta']); ?></span>
                                            <span class="multi-line">Medio: <?php echo htmlspecialchars($row['Medio_de_Pago']); ?></span>
                                        </td>
                                        <td>
                                            <span class="multi-line">CDP: <?php echo htmlspecialchars($row['CDP']); ?></span>
                                            <span class="multi-line">CRP: <?php echo htmlspecialchars($row['CODIGO_CRP']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="contenedorStandar2">
                <div class="contenedorGrafiaca">
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/presupuesto/control/Presupuseto_viaticos_consumidos.php'; ?>
                    
                </div>
            </div>
        </div>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>

    <script>
    $(document).ready(function(){
        let offset = 10;
        let limit = 10;

        function setCookie(name, value, days = 30) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "; expires=" + date.toUTCString();
            document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Strict";
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) {
                const cookieValue = parts.pop().split(';').shift();
                return cookieValue === 'undefined' ? '' : cookieValue;
            }
            return '';
        }

        function buscarDinamico() {
            const filtros = {
                action: 'buscarOP',
                numeroDocumento: $("#numeroDocumento").val(),
                estado: $("#estado").val(),
                beneficiario: $("#beneficiario").val(),
                mes: $("#mes").val(),
                fechaInicio: $("#fechaInicio").val(),
                fechaFin: $("#fechaFin").val(),
                offset: 0,
                limit: limit
            };

            // Guardar en cookies antes de la búsqueda
            Object.entries(filtros).forEach(([key, value]) => {
                if (key !== 'action' && key !== 'offset' && key !== 'limit') {
                    if (value) {
                        setCookie(`filtro_op_${key}`, value);
                    } else {
                        setCookie(`filtro_op_${key}`, '');
                    }
                }
            });
            setCookie('filtro_op_registrosPorPagina', $("#registrosPorPagina").val());

            $.ajax({
                url: window.location.href, // Usar la misma página
                method: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(response) {
                    $("#tablaOP tbody").empty();
                    if(Array.isArray(response) && response.length > 0) {
                        updateTableWithData(response);
                        $("#cargarMas").show();
                    } else {
                        $("#tablaOP tbody").append('<tr><td colspan="7" style="text-align: center;">No se encontraron resultados</td></tr>');
                        $("#cargarMas").hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    alert("Error al realizar la búsqueda.");
                }
            });
        }

        // Actualizar el cargarMas
        $("#cargarMas").on("click", function(){
            const filtros = {
                action: 'cargarMas',
                numeroDocumento: $("#numeroDocumento").val(),
                estado: $("#estado").val(),
                beneficiario: $("#beneficiario").val(),
                mes: $("#mes").val(),
                fechaInicio: $("#fechaInicio").val(),
                fechaFin: $("#fechaFin").val(),
                offset: offset,
                limit: limit
            };

            $.ajax({
                url: window.location.href, // Usar la misma página
                method: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(response) {
                    if(Array.isArray(response) && response.length > 0) {
                        updateTableWithData(response);
                        offset += limit;
                    } else {
                        $("#cargarMas").hide();
                        alert("No hay más registros para mostrar.");
                    }
                },
                error: function(xhr, status, error){
                    console.error("Error:", error);
                    alert("Error al cargar más registros.");
                }
            });
        });

        // Actualizar el manejo de filtros dinámicos
        $(".filtro-dinamico").on('change keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(buscarDinamico, doneTypingInterval);
            actualizarFiltrosActivos();
        });

        // Actualizar limpiarFiltros
        $("#limpiarFiltros").on("click", function(){
            // Limpiar cookies
            setCookie('filtro_op_numeroDocumento', '');
            setCookie('filtro_op_estado', 'Todos');
            setCookie('filtro_op_beneficiario', '');
            setCookie('filtro_op_mes', '');
            setCookie('filtro_op_fechaInicio', '');
            setCookie('filtro_op_fechaFin', '');
            setCookie('filtro_op_registrosPorPagina', '10');

            // Resetear campos
            $("#numeroDocumento").val('');
            $("#estado").val('Todos');
            $("#beneficiario").val('');
            $("#mes").val('');
            $("#fechaInicio").val('');
            $("#fechaFin").val('');
            $("#registrosPorPagina").val('10');
            
            limit = 10;
            offset = 0;
            buscarDinamico();
        });

        // Variables para mantener la paginación y filtros
        // Evento para cambio en registros por página
        $("#registrosPorPagina").on('change', function() {
            const valorSeleccionado = $(this).val();

            // Actualizar el límite según la selección
            limit = valorSeleccionado === 'todos' ? 999999 : parseInt(valorSeleccionado);

            // Resetear offset y recargar datos
            offset = valorSeleccionado === 'todos' ? 0 : parseInt(valorSeleccionado);

            // Ocultar o mostrar botón "Cargar más" según selección
            if(valorSeleccionado === 'todos') {
                $("#cargarMas").hide();
            }

            buscarDinamico();
        });

        // Manejo del botón "Cargar más"
        $("#cargarMas").on("click", function(){
            const filtros = {
                action: 'buscarOP',
                numeroDocumento: $("#numeroDocumento").val(),
                estado: $("#estado").val(),
                beneficiario: $("#beneficiario").val(),
                mes: $("#mes").val(),
                fechaInicio: $("#fechaInicio").val(),
                fechaFin: $("#fechaFin").val(),
                offset: offset,
                limit: limit
            };

            $.ajax({
                url: window.location.href, // Usar la misma página
                method: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success' && response.data.length > 0) {
                        updateTableWithData(response.data);
                        offset += limit;
                    } else {
                        $("#cargarMas").hide();
                        alert("No hay más registros para mostrar.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("Error al cargar más registros.");
                }
            });
        });

        // Manejo del botón "Limpiar Filtros"
        $("#limpiarFiltros").on("click", function(){
            // Resetear los valores de los filtros
            $("#numeroDocumento").val('');
            $("#estado").val('Todos');
            $("#registrosPorPagina").val('10');
            limit = 10;
            offset = 10;

            // Recargar la tabla con valores iniciales
            $.ajax({
                url: window.location.href, // Usar la misma página
                method: 'GET',
                data: {
                    action: 'cargarMas',
                    numeroDocumento: '',
                    estado: 'Todos',
                    offset: 0,
                    limit: 10
                },
                dataType: 'json',
                success: function(response) {
                    // Limpiar la tabla actual
                    $("#tablaOP tbody").empty();

                    // Cargar los nuevos datos
                    updateTableWithData(response);

                    // Mostrar el botón de cargar más si estaba oculto
                    $("#cargarMas").show();
                },
                error: function(){
                    alert("Error al recargar los registros.");
                }
            });
            actualizarFiltrosActivos();
        });

        // Agregar búsqueda dinámica
        let typingTimer;
        const doneTypingInterval = 500; // Tiempo de espera después de escribir (500ms)

        $("#numeroDocumento").on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(buscarDinamico, doneTypingInterval);
        });

        $("#numeroDocumento").on('keydown', function() {
            clearTimeout(typingTimer);
        });

        // Manejar cambios en todos los filtros
        $(".filtro-dinamico").on('change keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(buscarDinamico, doneTypingInterval);
            actualizarFiltrosActivos();
        });

        function actualizarFiltrosActivos() {
            const filtros = {
                'N° OP': $("#numeroDocumento").val(),
                'Beneficiario': $("#beneficiario").val(),
                'Estado': $("#estado").val() !== 'Todos' ? $("#estado").val() : '',
                'Mes': $("#mes option:selected").text() !== 'Todos los meses' ? $("#mes option:selected").text() : '',
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

        function buscarDinamico() {
            const filtros = {
                action: 'buscarOP', // Cambiar el action para diferenciar de otros endpoints
                numeroDocumento: $("#numeroDocumento").val(),
                estado: $("#estado").val(),
                beneficiario: $("#beneficiario").val(),
                mes: $("#mes").val(),
                fechaInicio: $("#fechaInicio").val(),
                fechaFin: $("#fechaFin").val(),
                offset: 0,
                limit: limit
            };

            // Resetear offset para nueva búsqueda
            offset = 10;

            $.ajax({
                url: window.location.href, // Usar la misma página
                method: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(response) {
                    // Limpiar la tabla actual
                    $("#tablaOP tbody").empty();

                    if(response.length > 0) {
                        // Cargar los nuevos datos
                        updateTableWithData(response);
                        $("#cargarMas").show();
                    } else {
                        let mensajeNoResultados = "No se encontraron resultados con los filtros seleccionados";
                        $("#tablaOP tbody").append(`<tr><td colspan='7' style='text-align: center;'>${mensajeNoResultados}</td></tr>`);
                        $("#cargarMas").hide();
                    }
                },
                error: function(){
                    alert("Error al realizar la búsqueda.");
                }
            });
        }

        // Función para formatear números con signo de pesos
        function formatCurrency(value) {
            return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Modificar la función createTableRow para incluir el formato de moneda
        function createTableRow(row) {
            return `
                <tr data-documento="${row.Numero_Documento}">
                    <td>${row.Numero_Documento}</td>
                    <td>
                        <span class="multi-line">Registro: ${row.Fecha_de_Registro}</span>
                        <span class="multi-line">Pago: ${row.Fecha_de_Pago}</span>
                    </td>
                    <td>${row.Estado}</td>
                    <td>${row.Nombre_Razon_Social}</td>
                    <td>
                        <span class="multi-line">${formatCurrency(row.Valor_Neto)}</span>
                    </td>
                    <td>
                        <span class="multi-line">Estado: ${row.Estado_Cuenta}</span>
                        <span class="multi-line">Medio: ${row.Medio_de_Pago}</span>
                    </td>
                    <td>
                        <span class="multi-line">CDP: ${row.CDP}</span>
                        <span class="multi-line">CRP: ${row.CODIGO_CRP}</span>
                    </td>
                </tr>`;
        }

        // Asegurarse de que los datos cargados dinámicamente también estén formateados
        function updateTableWithData(response) {
            response.forEach(function(row) {
                $("#tablaOP tbody").append(createTableRow(row));
            });
        }

        // Estilos CSS inline para los filtros activos
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

        // Inicialización al cargar la página
        $(document).ready(function(){
            const hayCookies = document.cookie.split(';').some(c => c.trim().startsWith('filtro_op_'));
            
            if(hayCookies) {
                // Solo realizar búsqueda si hay cookies de filtros
                const cookieVals = {
                    numeroDocumento: getCookie('filtro_op_numeroDocumento'),
                    estado: getCookie('filtro_op_estado'),
                    beneficiario: getCookie('filtro_op_beneficiario'),
                    mes: getCookie('filtro_op_mes'),
                    fechaInicio: getCookie('filtro_op_fechaInicio'),
                    fechaFin: getCookie('filtro_op_fechaFin')
                };

                Object.entries(cookieVals).forEach(([key, value]) => {
                    if(value) {
                        $(`#${key}`).val(value);
                    }
                });

                buscarDinamico();
                actualizarFiltrosActivos();
            }
        });

        // Función helper para obtener valores de cookies
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return '';
        }
    });
    </script>
</body>
</html>