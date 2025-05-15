<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/general_sennova/metodosGestor.php';

requireRole(['4', '5', '6']);
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
      body {
        font-family: 'Inter', sans-serif;
      }
      .thead-sticky {
        position: sticky;
        top: 0;
        z-index: 1;
      }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <div class="flex-grow">
            <div class="container mx-auto px-4 py-8">
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Filtros de Búsqueda</h2>
                        <form id="filtroForm" method="GET" action="historialOP.php" onsubmit="return false;" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="numeroDocumento" class="block text-gray-700 text-sm font-bold mb-2">N° Orden de Pago</label>
                                    <input type="text" id="numeroDocumento" name="numeroDocumento" value="<?php echo htmlspecialchars($numeroDocumento); ?>" placeholder="Número OP" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label for="beneficiario" class="block text-gray-700 text-sm font-bold mb-2">Beneficiario</label>
                                    <input type="text" id="beneficiario" name="beneficiario" value="<?php echo htmlspecialchars($beneficiario); ?>" placeholder="Nombre beneficiario" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label for="estado" class="block text-gray-700 text-sm font-bold mb-2">Estado</label>
                                    <select id="estado" name="estado" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="Todos" <?php echo ($estado=='Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Pagada" <?php echo ($estado=='Pagada') ? 'selected' : ''; ?>>Pagada</option>
                                        <option value="Pendiente" <?php echo ($estado=='Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="mes" class="block text-gray-700 text-sm font-bold mb-2">Mes</label>
                                    <select id="mes" name="mes" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="">Todos los meses</option>
                                        <option value="1" <?php echo ($mes=='1') ? 'selected' : ''; ?>>Enero</option>
                                        <option value="2" <?php echo ($mes=='2') ? 'selected' : ''; ?>>Febrero</option>
                                        <option value="3" <?php echo ($mes=='3') ? 'selected' : ''; ?>>Marzo</option>
                                        <option value="4" <?php echo ($mes=='4') ? 'selected' : ''; ?>>Abril</option>
                                        <option value="5" <?php echo ($mes=='5') ? 'selected' : ''; ?>>Mayo</option>
                                        <option value="6" <?php echo ($mes=='6') ? 'selected' : ''; ?>>Junio</option>
                                        <option value="7" <?php echo ($mes=='7') ? 'selected' : ''; ?>>Julio</option>
                                        <option value="8" <?php echo ($mes=='8') ? 'selected' : ''; ?>>Agosto</option>
                                        <option value="9" <?php echo ($mes=='9') ? 'selected' : ''; ?>>Septiembre</option>
                                        <option value="10" <?php echo ($mes=='10') ? 'selected' : ''; ?>>Octubre</option>
                                        <option value="11" <?php echo ($mes=='11') ? 'selected' : ''; ?>>Noviembre</option>
                                        <option value="12" <?php echo ($mes=='12') ? 'selected' : ''; ?>>Diciembre</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="fechaInicio" class="block text-gray-700 text-sm font-bold mb-2">Fecha Inicio</label>
                                    <input type="date" id="fechaInicio" name="fechaInicio" value="<?php echo htmlspecialchars($fechaInicio); ?>" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label for="fechaFin" class="block text-gray-700 text-sm font-bold mb-2">Fecha Fin</label>
                                    <input type="date" id="fechaFin" name="fechaFin" value="<?php echo htmlspecialchars($fechaFin); ?>" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                 <div>
                                    <label for="registrosPorPagina" class="block text-gray-700 text-sm font-bold mb-2">N° Registros</label>
                                    <select id="registrosPorPagina" name="registrosPorPagina" class="filtro-dinamico shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="10" <?php echo ($registrosPorPagina=='10') ? 'selected' : ''; ?>>10</option>
                                        <option value="20" <?php echo ($registrosPorPagina=='20') ? 'selected' : ''; ?>>20</option>
                                        <option value="40" <?php echo ($registrosPorPagina=='40') ? 'selected' : ''; ?>>40</option>
                                        <option value="60" <?php echo ($registrosPorPagina=='60') ? 'selected' : ''; ?>>60</option>
                                        <option value="todos" <?php echo ($registrosPorPagina=='todos') ? 'selected' : ''; ?>>Todos</option>
                                    </select>
                                </div>
                                <div class="flex items-end justify-end space-x-2 mt-2">
                                    <button type="button" id="limpiarFiltros" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        <i class="fas fa-times mr-2"></i> Limpiar
                                    </button>
                                    <button id="cargarMas" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">+ Registros</button>
                                </div>
                            </div>
                        </form>
                        <div id="filtros-activos" class="mt-4 text-sm text-gray-600"></div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-x-auto" style="padding: 20px;">
                    <div class="py-3">
                        <table id="tablaOP" class="min-w-full table-auto rounded-lg">
                            <thead class="thead-sticky bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">N° OP</th>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">Fechas</th>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">Estado</th>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">Beneficiario</th>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">Valor Neto</th>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">Información Bancaria</th>
                                    <th class="px-4 py-2 text-left text-gray-600 font-semibold uppercase">Códigos Asociados</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php foreach ($initialData as $row): ?>
                                    <tr data-documento="<?php echo htmlspecialchars($row['Numero_Documento']); ?>">
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['Numero_Documento']); ?></td>
                                        <td class="border px-4 py-2">
                                            <span class="block"><?php echo htmlspecialchars($row['Fecha_de_Registro']); ?></span>
                                            <span class="block"><?php echo htmlspecialchars($row['Fecha_de_Pago']); ?></span>
                                        </td>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['Estado']); ?></td>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['Nombre_Razon_Social']); ?></td>
                                        <td class="border px-4 py-2">
                                            <span class="block"><?php echo '$ ' . number_format($row['Valor_Neto'], 2, ',', '.'); ?></span>
                                        </td>
                                        <td class="border px-4 py-2">
                                            <span class="block">Estado: <?php echo htmlspecialchars($row['Estado_Cuenta']); ?></span>
                                            <span class="block">Medio: <?php echo htmlspecialchars($row['Medio_de_Pago']); ?></span>
                                        </td>
                                        <td class="border px-4 py-2">
                                            <span class="block">CDP: <?php echo htmlspecialchars($row['CDP']); ?></span>
                                            <span class="block">CRP: <?php echo htmlspecialchars($row['CODIGO_CRP']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
             <div class="container mx-auto px-4 py-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/SENNOVA/General/ViaticosGraficas.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
                        filtrosHTML += `<span class="inline-block bg-indigo-200 text-indigo-700 px-2 py-1 rounded-full text-sm font-semibold mr-2">${key}: ${value}</span>`;
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
                            $("#tablaOP tbody").append(`<tr><td colspan='7' class="px-4 py-2 border text-center text-gray-500">${mensajeNoResultados}</td></tr>`);
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
                            $("#tablaOP tbody").append(`<tr><td colspan='7' class="px-4 py-2 border text-center text-gray-500">${mensajeNoResultados}</td></tr>`);
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
                        <td class="border px-4 py-2">${row.Numero_Documento}</td>
                        <td class="border px-4 py-2">
                            <span class="block">${row.Fecha_de_Registro}</span>
                            <span class="block">${row.Fecha_de_Pago}</span>
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
        });
    </script>
</body>
</html>
