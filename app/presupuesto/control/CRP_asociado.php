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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/modern_styles.css">
</head>
<body class="app-layout">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    
    <div class="contenedor app-container">
        <div class="contenedorStandar app-content">
            <div class="page-header">
                <h1 class="page-title">RPs Asociados al CDP: <span class="text-primary"><?php echo htmlspecialchars($cod_CDP); ?></span></h1>
                <h1 class="page-title">Total RPs: <span class="text-primary"><?php echo $totalesCRP['total']; ?></span></h1>
            </div>
            
            <div class="flex-container">
                <!-- Columna de la tabla (60%) -->
                <div class="table-column">
                    <div class="contenderDeTabla data-table-container">
                        <div class="contendor_tabla">
                            <div class="table-responsive">
                                <table border="1" id="tablaCDP" class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>Código RP</th>
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
                                                    <td><?php echo htmlspecialchars($crp['Numero_Documento']); ?></td>
                                                    <td><?php echo htmlspecialchars($crp['Fecha_de_Registro']); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $crp['Estado'])); ?>">
                                                            <?php echo htmlspecialchars($crp['Estado']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($crp['Nombre_Razon_Social']); ?></td>
                                                    <td class="text-right">$<?php echo number_format($crp['Valor_Inicial'] ?? 0, 2); ?></td>
                                                    <td class="text-right">$<?php echo number_format($crp['Valor_Actual'] ?? 0, 2); ?></td>
                                                    <td class="text-right">$<?php echo number_format($crp['Saldo_por_Utilizar'] ?? 0, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="no-data">
                                                    <div class="empty-state">
                                                        <i class="fas fa-info-circle empty-icon"></i>
                                                        <p>No hay CRPs asociados a este CDP</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna de información (40%) -->
                <div class="info-column">
                    <!-- Resumen de totales -->
                    <div class="card resumen-totales">
                        <div class="card-content">
                            <div class="stat-item">
                                <div class="stat-label">Valor Aprobado CDP</div>
                                <div class="stat-value">$<?php echo number_format($totalesCRP['valor_cdp_aprobado'], 2); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Saldo disponible del CDP</div>
                                <div class="stat-value">$<?php echo number_format($totalesCRP['saldo_cdp'], 2); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Valor Comprometido de CDP</div>
                                <div class="stat-value">
                                    $<?php echo number_format($totalesCRP['valor_cdp_aprobado'] - $totalesCRP['saldo_cdp'], 2); ?>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Valor Comprometido en RP de (<?php echo $totalesCRP['total']; ?>) Registros</div>
                                <div class="stat-value">$<?php echo number_format($totalesCRP['total_valor_crp'], 2); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Saldo Total Disponible de los RP</div>
                                <div class="stat-value">$<?php echo number_format($totalesCRP['saldo_crp'], 2); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Saldo Total Facturado</div>
                                <div class="stat-value">
                                    $<?php echo number_format($totalesCRP['total_valor_crp'] - $totalesCRP['saldo_crp'], 2); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="actions-container">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
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
                        showNotification("No hay más registros para mostrar", "info");
                        $("#cargarMas").hide();
                    }
                },
                error: function(){
                    showNotification("Error al cargar más registros", "error");
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
                    
                    showNotification("Filtros limpiados correctamente", "success");
                },
                error: function(){
                    showNotification("Error al recargar los registros", "error");
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

            let filtrosHTML = '<div class="active-filters-title"><i class="fas fa-filter"></i> <strong>Filtros activos:</strong></div>';
            let hayFiltros = false;

            if (numeroDoc) {
                filtrosHTML += `<span class="filtro-tag"><span class="tag-label">Documento:</span> ${numeroDoc}</span>`;
                hayFiltros = true;
            }
            if (fuenteVal !== 'Todos') {
                filtrosHTML += `<span class="filtro-tag"><span class="tag-label">Fuente:</span> ${fuenteVal}</span>`;
                hayFiltros = true;
            }
            if (reintegrosVal !== 'Todos') {
                filtrosHTML += `<span class="filtro-tag"><span class="tag-label">Reintegros:</span> ${reintegrosVal}</span>`;
                hayFiltros = true;
            }
            if (registrosVal !== '10') {
                filtrosHTML += `<span class="filtro-tag"><span class="tag-label">Registros:</span> ${registrosVal}</span>`;
                hayFiltros = true;
            }

            $("#filtros-activos").html(hayFiltros ? filtrosHTML : '');
        }

        function buscarDinamico() {
            const numeroDocumento = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();

            // Mostrar indicador de carga
            showLoadingIndicator();

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
                    // Ocultar indicador de carga
                    hideLoadingIndicator();
                    
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
                        let tr = `
                            <tr>
                                <td colspan='9' class="no-data">
                                    <div class="empty-state">
                                        <i class="fas fa-search empty-icon"></i>
                                        <p>${mensajeNoResultados}</p>
                                    </div>
                                </td>
                            </tr>`;
                        $("#tablaCDP tbody").append(tr);
                        $("#cargarMas").hide();
                    }
                },
                error: function(){
                    hideLoadingIndicator();
                    showNotification("Error al realizar la búsqueda", "error");
                }
            });
        }

        // Función para crear una nueva fila (modificada para incluir los data attributes)
        function createTableRow(row) {
            return `
                <tr data-documento="${row.Numero_Documento}" class="data-row">
                    <td>${row.Numero_Documento}</td>
                    <td>${row.Fecha_de_Registro}</td>
                    <td>${row.Fecha_de_Creacion}</td>
                    <td>
                        <span class="badge badge-${getStatusClass(row.Estado)}">${row.Estado}</span>
                        <div class="details-row">
                            <div class="detail-item"><i class="fas fa-building"></i> ${row.Dependencia}</div>
                            <div class="detail-item"><i class="fas fa-money-bill-wave"></i> ${row.Fuente}</div>
                        </div>
                    </td>
                    <td class="text-right">${row.Valor_Actual}</td>
                    <td class="text-right">${row.Saldo_por_Comprometer}</td>
                    <td class="text-center">                       
                        <a href="control/CRP_asociado.php?cod_CDP=${row.Numero_Documento}" 
                           class="ingresarConsumo btn-action btn-add">
                            <i class="fas fa-plus-circle"></i>
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

        // Función para determinar la clase de estado
        function getStatusClass(estado) {
            switch (estado.toLowerCase()) {
                case 'activo':
                case 'aprobado':
                    return 'success';
                case 'pendiente':
                    return 'warning';
                case 'cancelado':
                case 'rechazado':
                    return 'danger';
                default:
                    return 'info';
            }
        }

        // Funciones para notificaciones
        function showNotification(message, type) {
            const notificationContainer = $('.notification-container');
            
            if (notificationContainer.length === 0) {
                $('body').append('<div class="notification-container"></div>');
            }
            
            const notification = $(`
                <div class="notification notification-${type}">
                    <div class="notification-icon">
                        <i class="fas fa-${getNotificationIcon(type)}"></i>
                    </div>
                    <div class="notification-content">${message}</div>
                    <button class="notification-close"><i class="fas fa-times"></i></button>
                </div>
            `);
            
            $('.notification-container').append(notification);
            
            setTimeout(() => {
                notification.addClass('show');
            }, 10);
            
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
            
            notification.find('.notification-close').on('click', function() {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            });
        }
        
        function getNotificationIcon(type) {
            switch(type) {
                case 'success': return 'check-circle';
                case 'error': return 'exclamation-circle';
                case 'warning': return 'exclamation-triangle';
                case 'info': 
                default: return 'info-circle';
            }
        }
        
        // Funciones para indicadores de carga
        function showLoadingIndicator() {
            const loadingHTML = `
                <div class="loading-overlay">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                </div>
            `;
            
            if ($('.loading-overlay').length === 0) {
                $('.data-table-container').append(loadingHTML);
            }
        }
        
        function hideLoadingIndicator() {
            $('.loading-overlay').fadeOut(300, function() {
                $(this).remove();
            });
        }
    });
    </script>
</body>
</html>