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

// Leer filtros desde cookies si existen, si no, usar GET, si no, valores por defecto
function getFiltroCookie($nombre, $default = '') {
    return isset($_COOKIE[$nombre]) ? $_COOKIE[$nombre] : $default;
}

$numeroDocumento = isset($_GET['numeroDocumento']) ? htmlspecialchars(trim($_GET['numeroDocumento'])) : getFiltroCookie('filtroOP_numeroDocumento', '');
$estado = isset($_GET['estado']) ? htmlspecialchars(trim($_GET['estado'])) : getFiltroCookie('filtroOP_estado', 'Todos');
$beneficiario = isset($_GET['beneficiario']) ? htmlspecialchars(trim($_GET['beneficiario'])) : getFiltroCookie('filtroOP_beneficiario', '');
$mes = isset($_GET['mes']) ? filter_var($_GET['mes'], FILTER_VALIDATE_INT) : getFiltroCookie('filtroOP_mes', '');
$fechaInicio = isset($_GET['fechaInicio']) ? htmlspecialchars(trim($_GET['fechaInicio'])) : getFiltroCookie('filtroOP_fechaInicio', '');
$fechaFin = isset($_GET['fechaFin']) ? htmlspecialchars(trim($_GET['fechaFin'])) : getFiltroCookie('filtroOP_fechaFin', '');
$registrosPorPagina = getFiltroCookie('filtroOP_registrosPorPagina', '10');
$limit = ($registrosPorPagina === 'todos') ? 999999 : intval($registrosPorPagina);

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

// Se obtienen los primeros registros según los filtros de cookies o GET
$initialData = $miClaseG->obtenerOP($filtrosIniciales, $limit, 0);
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
        // --- Variables de paginación ---
        let limit = <?php echo json_encode($limit); ?>;
        let offset = limit;

        // --- Función para establecer cookies ---
        function setCookie(name, value, days = 30) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // --- Guardar filtros en cookies cada vez que cambian ---
        function guardarFiltrosEnCookies() {
            setCookie('filtroOP_numeroDocumento', $("#numeroDocumento").val());
            setCookie('filtroOP_estado', $("#estado").val());
            setCookie('filtroOP_beneficiario', $("#beneficiario").val());
            setCookie('filtroOP_mes', $("#mes").val());
            setCookie('filtroOP_fechaInicio', $("#fechaInicio").val());
            setCookie('filtroOP_fechaFin', $("#fechaFin").val());
            setCookie('filtroOP_registrosPorPagina', $("#registrosPorPagina").val());
        }

        // --- Limpiar cookies de filtros ---
        function limpiarCookiesFiltros() {
            setCookie('filtroOP_numeroDocumento', '', -1);
            setCookie('filtroOP_estado', '', -1);
            setCookie('filtroOP_beneficiario', '', -1);
            setCookie('filtroOP_mes', '', -1);
            setCookie('filtroOP_fechaInicio', '', -1);
            setCookie('filtroOP_fechaFin', '', -1);
            setCookie('filtroOP_registrosPorPagina', '', -1);
        }

        // --- Al cambiar cualquier filtro, guardar en cookies y buscar ---
        let typingTimer;
        const doneTypingInterval = 500;

        $(".filtro-dinamico").on('change keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                guardarFiltrosEnCookies();
                buscarDinamico();
                actualizarFiltrosActivos();
            }, doneTypingInterval);
        });

        // --- Al cambiar registros por página ---
        $("#registrosPorPagina").on('change', function() {
            const valorSeleccionado = $(this).val();
            limit = valorSeleccionado === 'todos' ? 999999 : parseInt(valorSeleccionado);
            offset = limit;
            guardarFiltrosEnCookies();
            if(valorSeleccionado === 'todos') {
                $("#cargarMas").hide();
            } else {
                $("#cargarMas").show();
            }
            buscarDinamico();
        });

        // --- Limpiar filtros y cookies ---
        $("#limpiarFiltros").on("click", function(){
            limpiarCookiesFiltros();
            $("#numeroDocumento").val('');
            $("#beneficiario").val('');
            $("#estado").val('Todos');
            $("#mes").val('');
            $("#fechaInicio").val('');
            $("#fechaFin").val('');
            $("#registrosPorPagina").val('10');
            limit = 10;
            offset = 10;
            buscarDinamico();
            actualizarFiltrosActivos();
        });

        // --- Al cargar la página, leer cookies y setear valores iniciales ---
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

        function setFiltrosDesdeCookies() {
            let filtros = [
                {id: 'numeroDocumento', cookie: 'filtroOP_numeroDocumento'},
                {id: 'estado', cookie: 'filtroOP_estado'},
                {id: 'beneficiario', cookie: 'filtroOP_beneficiario'},
                {id: 'mes', cookie: 'filtroOP_mes'},
                {id: 'fechaInicio', cookie: 'filtroOP_fechaInicio'},
                {id: 'fechaFin', cookie: 'filtroOP_fechaFin'},
                {id: 'registrosPorPagina', cookie: 'filtroOP_registrosPorPagina'}
            ];
            filtros.forEach(function(f) {
                let val = leerCookie(f.cookie);
                if(val !== null && typeof val !== 'undefined' && val !== '') {
                    $("#" + f.id).val(val);
                }
            });
            // Ajustar limit y offset según cookie de registrosPorPagina
            let valReg = leerCookie('filtroOP_registrosPorPagina');
            if(valReg) {
                limit = valReg === 'todos' ? 999999 : parseInt(valReg);
                offset = limit;
                if(valReg === 'todos') $("#cargarMas").hide();
                else $("#cargarMas").show();
            }
        }

        setFiltrosDesdeCookies();
        actualizarFiltrosActivos();

        // --- Si hay algún filtro en cookies, hacer búsqueda inicial ---
        let hayFiltrosCookies = false;
        ['filtroOP_numeroDocumento','filtroOP_estado','filtroOP_beneficiario','filtroOP_mes','filtroOP_fechaInicio','filtroOP_fechaFin','filtroOP_registrosPorPagina'].forEach(function(c){
            if(leerCookie(c)) hayFiltrosCookies = true;
        });
        if(hayFiltrosCookies) {
            buscarDinamico();
        }

        let offset = 10;

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

            $.ajax({
                url: './control/ajaxGestor.php',
                method: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(response) {
                    $("#tablaOP tbody").empty();
                    if(Array.isArray(response) && response.length > 0) {
                        updateTableWithData(response);
                        $("#cargarMas").show();
                    } else {
                        let mensajeNoResultados = "No se encontraron resultados con los filtros seleccionados";
                        $("#tablaOP tbody").append(`<tr><td colspan='7' style='text-align: center;'>${mensajeNoResultados}</td></tr>`);
                        $("#cargarMas").hide();
                    }
                },
                error: function(xhr, status, error){
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
                url: './control/ajaxGestor.php',
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
            $("#numeroDocumento").val('');
            $("#beneficiario").val('');
            $("#estado").val('Todos');
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
                url: './control/ajaxGestor.php',
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
                url: './control/ajaxGestor.php',
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
                url: './control/ajaxGestor.php',
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
    });
    </script>
</body>
</html>