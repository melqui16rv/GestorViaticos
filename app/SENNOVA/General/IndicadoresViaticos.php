<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/metodosGestor.php';

requireRole(['4']);
$miClaseG = new sennova_general_presuspuestal();

// Función para leer filtros de cookies o usar valores por defecto
function getFiltroCookie($nombre, $default = '') {
    return isset($_COOKIE[$nombre]) ? $_COOKIE[$nombre] : $default;
}

// Obtener valores de los filtros, ya sea de GET o de cookies
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

// Obtener los primeros registros según los filtros
$initialData = $miClaseG->obtenerOP($filtrosIniciales, $limit, 0);

// Filtrar dependencias permitidas
$dependenciasPermitidas = ['62', '66', '69', '70'];
$initialData = array_values(array_filter($initialData, function($row) use ($dependenciasPermitidas) {
    if (!isset($row['Dependencia'])) return false;
    if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($row['Dependencia']), $matches)) {
        return in_array($matches[1], $dependenciasPermitidas);
    }
    return false;
}));

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Órdenes de Pago</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos personalizados (puedes agregarlos aquí o en un archivo CSS aparte) */
        .contenedor {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .contenido {
            flex: 1;
        }
        .contenedorStandar {
            padding: 20px;
            margin-bottom: 20px;
        }
        .filtrosContenedor {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
        }
        .filtro-grupo {
            margin-bottom: 10px;
        }
        .filtro-botones {
            margin-top: 10px;
        }
        .contenderDeTabla {
            overflow-x: auto; /* Para hacer la tabla horizontalmente desplazable en pantallas pequeñas */
        }
        .tablaBusqueda {
            width: 100%;
            border-collapse: collapse;
        }
        .tablaBusqueda th, .tablaBusqueda td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }
        .tablaBusqueda th {
            background-color: #007bff;
            color: white;
        }
        .multi-line {
            display: block;
            margin-bottom: 5px;
        }
         .contenedorStandar2 {
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        #filtros-activos {
            margin-top: 10px;
        }
        .filtro-tag {
            background: #e9ecef;
            padding: 0.3em 0.6em;
            border-radius: 0.25em;
            margin-right: 0.5em;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="contenido">
            <div class="contenedorStandar">
                <div class="filtrosContenedor">
                    <div id="filtros">
                        <form id="filtroForm" method="GET" action="historialOP.php" onsubmit="return false;">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="numeroDocumento">N° Orden de Pago</label>
                                    <input type="text" class="form-control filtro-dinamico" id="numeroDocumento" name="numeroDocumento"
                                           value="<?php echo htmlspecialchars($numeroDocumento); ?>"
                                           placeholder="Número OP">
                                </div
                                <div class="form-group col-md-3">
                                    <label for="beneficiario">Beneficiario</label>
                                    <input type="text" class="form-control filtro-dinamico" id="beneficiario" name="beneficiario"
                                           value="<?php echo htmlspecialchars($beneficiario); ?>"
                                           placeholder="Nombre beneficiario">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="estado">Estado</label>
                                    <select id="estado" name="estado" class="form-control filtro-dinamico">
                                        <option value="Todos" <?php echo ($estado=='Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Pagada" <?php echo ($estado=='Pagada') ? 'selected' : ''; ?>>Pagada</option>
                                        <option value="Pendiente" <?php echo ($estado=='Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    </select>
                                </div>
                                 <div class="form-group col-md-3">
                                    <label for="mes">Mes</label>
                                    <select id="mes" name="mes" class="form-control filtro-dinamico">
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
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                     <label for="fechaInicio">Fecha Inicio</label>
                                    <input type="date" id="fechaInicio" name="fechaInicio" class="form-control filtro-dinamico">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="fechaFin">Fecha Fin</label>
                                    <input type="date" id="fechaFin" name="fechaFin" class="form-control filtro-dinamico">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="registrosPorPagina">N° Registros</label>
                                    <select id="registrosPorPagina" name="registrosPorPagina" class="form-control filtro-dinamico">
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="40">40</option>
                                        <option value="60">60</option>
                                        <option value="todos">Todos</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 d-flex align-items-end">
                                    <button type="button" id="limpiarFiltros" class="btn btn-outline-danger mr-2">
                                        <i class="fas fa-times"></i> Limpiar
                                    </button>
                                    <button id="cargarMas" class="btn btn-primary">+ Registros</button>
                                </div>
                            </div>
                        </form>
                        <div id="filtros-activos"></div>
                    </div>
                </div>
                <div class="contenderDeTabla">
                    <div class="contendor_tabla">
                        <table id="tablaOP" class="table table-striped table-bordered table-hover">
                            <thead class="thead-dark">
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
                                            <span class="multi-line"><?php echo '$ ' . number_format($row['Valor_Neto'], 2, ',', '.'); ?></span>
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
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/General/ViaticosGraficas.php'; ?>
                </div>
            </div>
        </div>
    </div>

     <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function(){
            // --- Variables de paginación ---
            let limit = <?php echo json_encode($limit); ?>;
            let offset = 0;

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

            // --- Función para guardar los filtros en cookies ---
            function guardarFiltrosEnCookies() {
                setCookie('filtroOP_numeroDocumento', $("#numeroDocumento").val());
                setCookie('filtroOP_estado', $("#estado").val());
                setCookie('filtroOP_beneficiario', $("#beneficiario").val());
                setCookie('filtroOP_mes', $("#mes").val());
                setCookie('filtroOP_fechaInicio', $("#fechaInicio").val());
                setCookie('filtroOP_fechaFin', $("#fechaFin").val());
                setCookie('filtroOP_registrosPorPagina', $("#registrosPorPagina").val());
            }

            // --- Función para limpiar las cookies de los filtros ---
            function limpiarCookiesFiltros() {
                setCookie('filtroOP_numeroDocumento', '', -1);
                setCookie('filtroOP_estado', '', -1);
                setCookie('filtroOP_beneficiario', '', -1);
                setCookie('filtroOP_mes', '', -1);
                setCookie('filtroOP_fechaInicio', '', -1);
                setCookie('filtroOP_fechaFin', '', -1);
                setCookie('filtroOP_registrosPorPagina', '', -1);
            }

            // --- Función para leer el valor de una cookie ---
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

            // --- Función para establecer los filtros desde las cookies ---
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

                // Establecer el valor del select de meses
                let mesCookie = leerCookie('filtroOP_mes');
                if (mesCookie) {
                    $("#mes").val(mesCookie);
                }
                // Ajustar el limit y offset
                let valReg = leerCookie('filtroOP_registrosPorPagina');
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

             // --- Función para actualizar la visualización de los filtros activos ---
            function actualizarFiltrosActivos() {
                const filtros = {
                    'N° OP': $("#numeroDocumento").val(),
                    'Beneficiario': $("#beneficiario").val(),
                    'Estado': $("#estado").val() !== 'Todos' ? $("#estado").val() : '',
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

            // --- Función para realizar la búsqueda y actualizar la tabla ---
            function buscarDinamico(resetOffset = true) {
                guardarFiltrosEnCookies();
                if(resetOffset) offset = 0;

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
                        $("#tablaOP tbody").empty();
                         // --- Manejo de la respuesta ---
                        if (response && typeof response === 'object' && response.error) {
                            let mensajeNoResultados = "No se encontraron resultados con los filtros seleccionados";
                            $("#tablaOP tbody").append(`<tr><td colspan='7' style='text-align: center;'>${mensajeNoResultados}</td></tr>`);
                            $("#cargarMas").hide();
                            return;
                        }
                        if (Array.isArray(response) && response.length > 0) {
                            updateTableWithData(response);
                             if(limit === 999999) {
                                $("#cargarMas").hide();
                            } else {
                                $("#cargarMas").show();
                            }
                        } else {
                             let mensajeNoResultados = "No se encontraron resultados con los filtros seleccionados";
                            $("#tablaOP tbody").append(`<tr><td colspan='7' style='text-align: center;'>${mensajeNoResultados}</td></tr>`);
                            $("#cargarMas").hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        console.log("Respuesta del servidor:", xhr.responseText);
                        alert("Error al realizar la búsqueda.");
                    }
                });
                actualizarFiltrosActivos();
            }

            // --- Evento para cargar más registros ---
            $("#cargarMas").on("click", function(e){
                e.preventDefault();
                offset += limit;

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
                        if(Array.isArray(response) && response.length > 0) {
                            updateTableWithData(response);
                        } else {
                            $("#cargarMas").hide();
                            alert("No hay más registros para mostrar.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        alert("Error al cargar más registros.");
                    }
                });
            });

            // --- Evento para el botón de limpiar filtros ---
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
                offset = 0;
                buscarDinamico();
            });

             // --- Evento para el cambio en el número de registros por página ---
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

            // --- Evento para los filtros dinámicos (cambio en input o select) ---
            $(".filtro-dinamico").on('change keyup', function() {
                buscarDinamico();
            });

            // --- Función para formatear los números de moneda ---
            function formatCurrency(value) {
                return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            // --- Función para crear filas de la tabla ---
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
                    </tr>
                `;
            }

            // --- Función para actualizar la tabla con los datos recibidos ---
            function updateTableWithData(response) {
                response.forEach(function(row) {
                    $("#tablaOP tbody").append(createTableRow(row));
                });
            }

// --- Inicialización: establecer filtros y realizar la primera búsqueda ---
            setFiltrosDesdeCookies();
            actualizarFiltrosActivos();
            buscarDinamico();
        });
    </script>
</body>
</html>
