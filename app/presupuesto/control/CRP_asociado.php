<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/planeacion/metodosGestor.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/planeacion/crpAsociados.php';

requireRole(['3']);
$miClaseG = new planeacion();

// Se obtienen los filtros iniciales (si existen)
$numeroDocumento = isset($_GET['numeroDocumento']) ? $_GET['numeroDocumento'] : '';
$fuente = isset($_GET['fuente']) ? $_GET['fuente'] : 'Todos';
$reintegros = isset($_GET['reintegros']) ? $_GET['reintegros'] : 'Todos';

// Se obtienen los primeros 10 registros
$initialData = $miClaseG->obtenerCDP($numeroDocumento, $fuente, $reintegros, 10, 0);

// Obtener el código CDP de la URL
$cod_CDP = isset($_GET['cod_CDP']) ? $_GET['cod_CDP'] : null;

if (!$cod_CDP) {
    die('Código CDP no proporcionado');
}

$miClaseG = new planeacion();

// Instanciar el nuevo gestor de CRPs
$gestorCRP = new planeacion1();

// Obtener los CRPs asociados al CDP
$crpsAsociados = $gestorCRP->obtenerCRPsAsociados($cod_CDP);
$totalesCRP = $gestorCRP->obtenerTotalCRPs($cod_CDP);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRP Asociados</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/index_gestor.css">
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    
    <div class="contenedor">
        <div class="contenedorStandar">
            <h1>CRP Asociados al CDP: <?php echo htmlspecialchars($cod_CDP); ?></h1>
            
            <!-- Resumen de totales -->
            <div class="resumen-totales">
                <p>Total CRPs: <?php echo $totalesCRP['total']; ?></p>
                <p>Valor Total Aprobado: $<?php echo number_format($totalesCRP['valor_cdp_aprobado'], 2); ?></p>
                <p>Valor Total del CRP: $<?php echo number_format($totalesCRP['total_valor_crp'], 2); ?></p>
                <p>Valor Saldo sin utilizar del valor Total del CDP: $<?php echo number_format($totalesCRP['saldo_cdp'], 2); ?></p>
                <p>Valor Saldo sin utilizar del valor Total del CRP: $<?php echo number_format($totalesCRP['saldo_crp'], 2); ?></p>
            </div>

            <div class="contenderDeTabla">
                <div class="contendor_tabla">
                    <table border="1" id="tablaCDP">
                        <thead>
                            <tr>
                                <th>Código CRP</th>
                                <th>Código CDP</th>
                                <th>N° Documento</th>
                                <th>Fecha Registro</th>
                                <th>Estado</th>
                                <th>Beneficiario</th>
                                <th>Valor Inicial</th>
                                <th>Valor Actual</th>
                                <th>Saldo por Utilizar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($crpsAsociados && count($crpsAsociados) > 0): ?>
                                <?php foreach ($crpsAsociados as $crp): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($crp['CODIGO_CRP']); ?></td>
                                        <td><?php echo htmlspecialchars($crp['CODIGO_CDP']); ?></td>
                                        <td><?php echo htmlspecialchars($crp['Numero_Documento']); ?></td>
                                        <td><?php echo htmlspecialchars($crp['Fecha_de_Registro']); ?></td>
                                        <td><?php echo htmlspecialchars($crp['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($crp['Nombre_Razon_Social']); ?></td>
                                        <td>$<?php echo number_format($crp['Valor_Inicial'] ?? 0, 2); ?></td>
                                        <td>$<?php echo number_format($crp['Valor_Actual'] ?? 0, 2); ?></td>
                                        <td>$<?php echo number_format($crp['Saldo_por_Utilizar'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="no-data">No hay CRPs asociados a este CDP</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>

    <script>
    $(document).ready(function(){
        // Eliminar todo el código relacionado con el evento click y el modal
        let offset = 10;
        let limit = 10;

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
                    action: 'cargarMas',
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
                    action: 'cargarMas',
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

        function buscarDinamico() {
            const numeroDocumento = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();

            // Resetear offset para nueva búsqueda
            offset = 10;

            $.ajax({
                url: './control/ajaxGestor.php',
                method: 'GET',
                data: {
                    action: 'cargarMas',
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

        // Función para crear una nueva fila (modificada para incluir los data attributes)
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
                    <td>${row.Valor_Actual}</td>
                    <td>${row.Saldo_por_Comprometer}</td>
                    <td style="text-align: center;">                       
                        <a href="control/CRP_asociado.php?cod_CDP=${row.Numero_Documento}" 
                           class="ingresarConsumo">
                            +
                        </a>
                    </td>
                </tr>`;
        }

        // Modificar las funciones que agregan filas para usar createTableRow
        function updateTableWithData(response) {
            response.forEach(function(row) {
                $("#tablaCDP tbody").append(createTableRow(row));
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
    <style>
    .resumen-totales {
        background: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        display: flex;
        justify-content: space-around;
    }
    .resumen-totales p {
        margin: 0;
        font-weight: bold;
    }
    .no-data {
        text-align: center;
        padding: 20px;
        color: #666;
    }
    </style>
</body>
</html>
