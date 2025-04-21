<?php

use Dom\Text;

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

// Se obtienen los filtros iniciales desde cookies o GET
$numeroDocumento = isset($_GET['numeroDocumento']) ? $_GET['numeroDocumento'] : 
                  (isset($_COOKIE['filtro_numeroDocumento']) ? $_COOKIE['filtro_numeroDocumento'] : '');
$fuente = isset($_GET['fuente']) ? $_GET['fuente'] : 
          (isset($_COOKIE['filtro_fuente']) ? $_COOKIE['filtro_fuente'] : 'Todos');
$reintegros = isset($_GET['reintegros']) ? $_GET['reintegros'] : 
              (isset($_COOKIE['filtro_reintegros']) ? $_COOKIE['filtro_reintegros'] : 'Todos');
$registrosPorPagina = isset($_COOKIE['filtro_registrosPorPagina']) ? $_COOKIE['filtro_registrosPorPagina'] : '10';
$limit = ($registrosPorPagina === 'todos') ? 999999 : intval($registrosPorPagina);

// Se obtienen los primeros registros
$initialData = $miClaseG->obtenerCDP($numeroDocumento, $fuente, $reintegros, $limit, 0);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <div class="contenedor" style="min-height: 100vh; display: flex; flex-direction: column;">
        <div class="contenido" style="flex: 1;">
            <div class="contenedorStandar">
                <div class="filtrosContenedor">
                    <!-- Sección de Filtros -->
                    <div id="filtros">
                        <form id="filtroForm" method="GET" action="index.php" onsubmit="return false;">
                            <div class="filtro-grupo">
                                <label for="numeroDocumento">N° CDP</label>
                                <input type="text" id="numeroDocumento" name="numeroDocumento"
                                       value="<?php echo htmlspecialchars($numeroDocumento); ?>"
                                       placeholder="Ejemplo: 125" class="filtro-dinamico">
                            </div>
        
                            <div class="filtro-grupo">
                                <label for="fuente">Fuente</label>
                                <select id="fuente" name="fuente" class="filtro-dinamico">
                                    <option value="Todos" <?php echo ($fuente=='Todos') ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Nación" <?php echo ($fuente=='Nación') ? 'selected' : ''; ?>>Nación</option>
                                    <option value="Propios" <?php echo ($fuente=='Propios') ? 'selected' : ''; ?>>Propios</option>
                                </select>
                            </div>
        
                            <div class="filtro-grupo">
                                <label for="reintegros">Reintegros</label>
                                <select id="reintegros" name="reintegros" class="filtro-dinamico">
                                    <option value="Todos" <?php echo ($reintegros=='Todos') ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Con reintegro" <?php echo ($reintegros=='Con reintegro') ? 'selected' : ''; ?>>Con reintegro</option>
                                    <option value="Sin reintegro" <?php echo ($reintegros=='Sin reintegro') ? 'selected' : ''; ?>>Sin reintegro</option>
                                </select>
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
                        <table border="1" id="tablaCDP" class="tablaBusqueda">
                            <thead>
                                <tr>
                                    <th>CDP</th>
                                    <th>Fecha<br>Registro</th>
                                    <th>Fecha<br>Creación</th>
                                    <th>Estado/<br>Dependencia/<br>Fuente</th>
                                    <th>Valor<br>Actual</th>
                                    <th>Saldo<br>Comprometer</th>
                                    <th>Descripción<br>Detallada</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php foreach ($initialData as $row): ?>
                                    <tr data-documento="<?php echo htmlspecialchars($row['Numero_Documento']); ?>">
                                        <td><?php echo htmlspecialchars($row['Numero_Documento']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Fecha_de_Registro']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Fecha_de_Creacion']); ?></td>
                                        <td>
                                            <span class="multi-line"><?php echo htmlspecialchars($row['Estado']); ?></span>
                                            <span class="multi-line"><?php echo htmlspecialchars($row['Dependencia']); ?></span>
                                            <span class="multi-line"><?php echo htmlspecialchars($row['Fuente']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars('$ ' . number_format((float)$row['Valor_Actual'], 2, '.', ',')); ?></td>
                                        <td><?php echo htmlspecialchars('$ ' . number_format((float)$row['Saldo_por_Comprometer'], 2, '.', ',')); ?></td>
                                        <td style="text-align: center;">
                                            <a href="control/CRP_asociado.php?cod_CDP=<?php echo htmlspecialchars($row['Numero_Documento']); ?>" 
                                            class="btn-detalle" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/presupuesto/control/PresupuestoTotal.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>

    <script>
    $(document).ready(function(){
        // Eliminar todo el código relacionado con el evento click y el modal
        let offset = <?php echo ($registrosPorPagina === 'todos' ? 0 : 10); ?>;
        let limit = <?php echo ($registrosPorPagina === 'todos' ? 999999 : 10); ?>;

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
            const numeroDocumento = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();

            $.ajax({
                url: './control/ajaxGestor.php',
                method: 'GET',
                data: {
                    action: 'cargarMasCDP',
                    numeroDocumento: numeroDocumento,
                    fuente: fuente,
                    reintegros: reintegros,
                    offset: offset,
                    limit: limit
                },
                dataType: 'json',
                success: function(response) {
                    if(response.length > 0) {
                        updateTableWithData(response);
                        offset += limit;
                    } else {
                        alert("No hay más registros para mostrar.");
                        $("#cargarMas").hide();
                    }
                },
                error: function(){
                    alert("Error al cargar más registros.");
                }
            });
        });

        // Manejo del botón "Limpiar Filtros"
        $("#limpiarFiltros").on("click", function(){
            // Limpiar cookies
            setCookie('filtro_numeroDocumento', '');
            setCookie('filtro_fuente', 'Todos');
            setCookie('filtro_reintegros', 'Todos');
            setCookie('filtro_registrosPorPagina', '10');

            // Resetear los valores de los filtros
            $("#numeroDocumento").val('');
            $("#fuente").val('Todos');
            $("#reintegros").val('Todos');
            $("#registrosPorPagina").val('10');
            limit = 10;
            offset = 10;

            // Recargar la tabla con valores iniciales
            $.ajax({
                url: './control/ajaxGestor.php',
                method: 'GET',
                data: {
                    action: 'cargarMasCDP',
                    numeroDocumento: '',
                    fuente: 'Todos',
                    reintegros: 'Todos',
                    offset: 0,
                    limit: 10
                },
                dataType: 'json',
                success: function(response) {
                    // Limpiar la tabla actual
                    $("#tablaCDP tbody").empty();

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
            const numeroDoc = $("#numeroDocumento").val();
            const fuenteVal = $("#fuente").val();
            const reintegrosVal = $("#reintegros").val();
            const registrosVal = $("#registrosPorPagina").val();

            let filtrosHTML = '<strong>Filtros activos:</strong> ';
            let hayFiltros = false;

            if (numeroDoc) {
                filtrosHTML += `<span class="filtro-tag">Documento: ${numeroDoc}</span>`;
                hayFiltros = true;
            }
            if (fuenteVal !== 'Todos') {
                filtrosHTML += `<span class="filtro-tag">Fuente: ${fuenteVal}</span>`;
                hayFiltros = true;
            }
            if (reintegrosVal !== 'Todos') {
                filtrosHTML += `<span class="filtro-tag">Reintegros: ${reintegrosVal}</span>`;
                hayFiltros = true;
            }
            if (registrosVal !== '10') {
                filtrosHTML += `<span class="filtro-tag">Cantidad de Registros: ${registrosVal}</span>`;
                hayFiltros = true;
            }

            $("#filtros-activos").html(hayFiltros ? filtrosHTML : '');
        }

        // Función para establecer cookies
        function setCookie(name, value, days = 30) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function buscarDinamico() {
            const numeroDocumento = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();
            const registrosPorPagina = $("#registrosPorPagina").val();

            // Guardar filtros en cookies
            setCookie('filtro_numeroDocumento', numeroDocumento);
            setCookie('filtro_fuente', fuente);
            setCookie('filtro_reintegros', reintegros);
            setCookie('filtro_registrosPorPagina', registrosPorPagina);

            // Resetear offset para nueva búsqueda
            offset = 10;

            $.ajax({
                url: './control/ajaxGestor.php',
                method: 'GET',
                data: {
                    action: 'cargarMasCDP',
                    numeroDocumento: numeroDocumento,
                    fuente: fuente,
                    reintegros: reintegros,
                    offset: 0,
                    limit: limit
                },
                dataType: 'json',
                success: function(response) {
                    // Limpiar la tabla actual
                    $("#tablaCDP tbody").empty();

                    if(response.length > 0) {
                        // Cargar los nuevos datos
                        updateTableWithData(response);
                        $("#cargarMas").show();
                    } else {
                        let mensajeNoResultados = "No se encontraron resultados";
                        if (numeroDocumento || fuente !== 'Todos' || reintegros !== 'Todos') {
                            mensajeNoResultados += " con los filtros seleccionados";
                        }
                        let tr = `<tr><td colspan='9' style='text-align: center;'>${mensajeNoResultados}</td></tr>`;
                        $("#tablaCDP tbody").append(tr);
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
            return '$ ' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Modificar la función createTableRow para incluir el formato de moneda
        function createTableRow(row) {
            return `
                <tr data-documento="${row.Numero_Documento}">
                    <td>${row.Numero_Documento}</td>
                    <td>${row.Fecha_de_Registro}</td>
                    <td>${row.Fecha_de_Creacion}</td>
                    <td>
                        <span class="multi-line">${row.Estado}</span>
                        <span class="multi-line">${row.Dependencia}</span>
                        <span class="multi-line">${row.Fuente}</span>
                    </td>
                    <td>${formatCurrency(row.Valor_Actual)}</td>
                    <td>${formatCurrency(row.Saldo_por_Comprometer)}</td>

                    <td style="text-align: center;">
                        <a href="control/CRP_asociado.php?cod_CDP=${row.Numero_Documento}" 
                        class="btn-detalle" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>`;
        }

        // Asegurarse de que los datos cargados dinámicamente también estén formateados
        function updateTableWithData(response) {
            response.forEach(function(row) {
                $("#tablaCDP tbody").append(createTableRow(row));
            });
        }

        // Establecer valores iniciales desde las cookies
        $(document).ready(function(){
            const cookieNumeroDocumento = document.cookie.split('; ').find(row => row.startsWith('filtro_numeroDocumento='));
            const cookieFuente = document.cookie.split('; ').find(row => row.startsWith('filtro_fuente='));
            const cookieReintegros = document.cookie.split('; ').find(row => row.startsWith('filtro_reintegros='));
            const cookieRegistros = document.cookie.split('; ').find(row => row.startsWith('filtro_registrosPorPagina='));

            if(cookieRegistros) {
                const valorRegistros = cookieRegistros.split('=')[1];
                $("#registrosPorPagina").val(valorRegistros);
                
                // Actualizar límite y offset según el valor de registros
                if(valorRegistros === 'todos') {
                    limit = 999999;
                    offset = 0;
                    $("#cargarMas").hide();
                } else {
                    limit = parseInt(valorRegistros);
                    offset = parseInt(valorRegistros);
                }
            }
            
            // Si hay filtros guardados, realizar búsqueda inicial
            if(cookieNumeroDocumento || cookieFuente || cookieReintegros || cookieRegistros) {
                buscarDinamico();
                actualizarFiltrosActivos();
            }
        });

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