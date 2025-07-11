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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Presupuesto - Gestión CDP</title>
    
    <!-- Preload crítico -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" as="style">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'mono': ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        'primary': {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/compatibility.css">
      <!-- PWA Meta -->
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Sistema moderno de gestión de presupuesto CDP - SENA">
      <!-- Early Chart Functions Definition -->
    <script>
        // Global chart variables and functions need to be available early for the PresupuestoTotal component
        let globalChart = null;
        let globalChartData = null;

        // Early definition of expandChartGlobal function
        window.expandChartGlobal = function(data) {
            console.log('expandChartGlobal called with data:', data);
            globalChartData = data;
            
            // Wait for DOM to be ready, then show modal
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    showChartModal(data);
                });
            } else {
                showChartModal(data);
            }
        };

        function showChartModal(data) {
            const modal = document.getElementById('chartModalGlobal');
            if (!modal) {
                console.error('Modal chartModalGlobal no encontrado');
                return;
            }
            
            modal.style.display = 'flex';
            
            // Actualizar estadísticas en el modal
            const elements = {
                valorTotalGlobal: '$' + data.valor_actual,
                saldoDisponibleGlobal: '$' + data.saldo_disponible,
                porcentajeDisponibleGlobal: data.porcentaje_disponible + '%',
                consumoCdpGlobal: '$' + data.consumo_cdp,
                porcentajeConsumidoGlobal: data.porcentaje_consumido + '%'
            };
            
            Object.keys(elements).forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = elements[id];
                }
            });
            
            // Crear la gráfica cuando se abre el modal
            if (!globalChart) {
                createGlobalChart();
            } else {
                updateGlobalChart();
            }
            
            // Añadir event listener para cerrar con backdrop click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeChartGlobal();
                }
            });
        }

        // Crear gráfica global
        function createGlobalChart() {
            const ctx = document.getElementById('presupuestoChartGlobal');
            if (!ctx) {
                console.error('Canvas presupuestoChartGlobal no encontrado');
                return;
            }
            
            globalChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Presupuesto Consumido', 'Presupuesto Disponible'],
                    datasets: [{
                        data: [
                            parseFloat(globalChartData.porcentaje_consumido),
                            parseFloat(globalChartData.porcentaje_disponible)
                        ],
                        backgroundColor: [
                            '#0ea5e9',   // Azul para consumido
                            '#22c55e'    // Verde para disponible
                        ],
                        borderColor: [
                            '#0284c7', 
                            '#16a34a'
                        ],
                        borderWidth: 4,
                        cutout: '65%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 15,
                                    weight: '600'
                                },
                                padding: 24,
                                color: '#475569',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#1e293b',
                            bodyColor: '#475569',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            cornerRadius: 12,
                            displayColors: true,
                            titleFont: {
                                size: 15,
                                weight: '600'
                            },
                            bodyFont: {
                                size: 14
                            },
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const amounts = [
                                        '$' + globalChartData.consumo_cdp,
                                        '$' + globalChartData.saldo_disponible
                                    ];
                                    return `${label}: ${value.toFixed(1)}% (${amounts[context.dataIndex]})`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: 20
                    },
                    elements: {
                        arc: {
                            borderRadius: 10
                        }
                    }
                }
            });
        }

        // Actualizar gráfica global
        function updateGlobalChart() {
            if (globalChart && globalChartData) {
                globalChart.data.datasets[0].data = [
                    parseFloat(globalChartData.porcentaje_consumido),
                    parseFloat(globalChartData.porcentaje_disponible)
                ];
                globalChart.update();
            }
        }

        // Early definition of closeChartGlobal function
        window.closeChartGlobal = function() {
            const modal = document.getElementById('chartModalGlobal');
            if (modal) {
                modal.style.display = 'none';
            }
        };

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeChartGlobal();
            }
        });
    </script>
</head>
<body class="bg-gray-25 min-h-screen">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <!-- Loading Overlay -->
    <div id="loadingSpinner" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner-clean"></div>
            <p class="loading-text">Cargando datos...</p>
        </div>
    </div>

    <!-- Dashboard Container -->
    <div class="dashboard-layout">
        
        <!-- Dashboard Header -->
        <!-- <div class="dashboard-header">
            <div class="header-left">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-bar dashboard-icon"></i>
                    Dashboard Presupuestal
                </h1>
                <p class="dashboard-subtitle">Gestión de Certificados de Disponibilidad Presupuestal (CDP)</p>
            </div> -->
            
        <!--</div>         Mobile Filters Toggle Button -->
        <!-- <button class="mobile-filters-toggle" id="mobileFiltersToggle">
            <i class="fas fa-sliders-h"></i>
            Filtros
        </button> -->

        <!-- Main Dashboard Content -->
        <div class="dashboard-content">
            
            <!-- Left Panel - Filters and Controls -->
            <aside class="dashboard-sidebar">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-sliders-h"></i>
                        Filtros
                    </h3>
                    
                    <form id="filtroForm" class="filter-form">
                        <div class="filter-field">
                            <label class="field-label">Número CDP</label>
                            <input type="text" 
                                   id="numeroDocumento" 
                                   name="numeroDocumento"
                                   value="<?php echo htmlspecialchars($numeroDocumento); ?>"
                                   placeholder="Ej: 125, 300..." 
                                   class="field-input filtro-dinamico">
                        </div>

                        <div class="filter-field">
                            <label class="field-label">Fuente</label>
                            <select id="fuente" name="fuente" class="field-select filtro-dinamico">
                                <option value="Todos" <?php echo ($fuente=='Todos') ? 'selected' : ''; ?>>Todas</option>
                                <option value="Nación" <?php echo ($fuente=='Nación') ? 'selected' : ''; ?>>Nación</option>
                                <option value="Propios" <?php echo ($fuente=='Propios') ? 'selected' : ''; ?>>Propios</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="field-label">Reintegros</label>
                            <select id="reintegros" name="reintegros" class="field-select filtro-dinamico">
                                <option value="Todos" <?php echo ($reintegros=='Todos') ? 'selected' : ''; ?>>Todos</option>
                                <option value="Con reintegro" <?php echo ($reintegros=='Con reintegro') ? 'selected' : ''; ?>>Con reintegro</option>
                                <option value="Sin reintegro" <?php echo ($reintegros=='Sin reintegro') ? 'selected' : ''; ?>>Sin reintegro</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="field-label">Registros por página</label>
                            <select id="registrosPorPagina" name="registrosPorPagina" class="field-select filtro-dinamico">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="40">40</option>
                                <option value="60">60</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="button" id="limpiarFiltros" class="btn-secondary">
                                <i class="fas fa-eraser"></i>
                                Limpiar
                            </button>
                            <button type="button" id="cargarMas" class="btn-primary">
                                <i class="fas fa-plus"></i>
                                Más datos
                            </button>
                        </div>
                    </form>

                    <!-- Active Filters -->
                    <div id="filtros-activos" class="active-filters-sidebar"></div>
                </div>

                <!-- Quick Stats Card -->
                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-chart-pie"></i>
                        Resumen
                    </h3>
                    <div class="quick-stats">
                        <div class="dashboard-content-mini">
                            <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/presupuesto/control/PresupuestoTotal.php'; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Right Panel - Data Table -->
            <main class="dashboard-main">
                <div class="main-header">
                    <h2 class="main-title">
                        <i class="fas fa-table"></i>
                        Listado de CDP
                    </h2>
                    <div class="main-actions">
                         <div class="header-right">
                            <div class="stats-overview">
                                <div class="stat-item">
                                    <div class="stat-value" id="totalCDP">0</div>
                                    <div class="stat-label">Total CDP</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="totalMonto">$0</div>
                                    <div class="stat-label">Valor Total</div>
                                </div>
                            </div>
                        </div>
                        <button class="action-btn" id="refreshTable" title="Actualizar">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="action-btn" title="Exportar">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="action-btn" title="Configurar vista">
                            <i class="fas fa-cog"></i>
                        </button>
                        
                    </div>
                </div>

                <div class="table-container-clean">
                    <div class="table-scroll">
                        <table id="tablaCDP" class="clean-table">
                            <thead>
                                <tr>
                                    <th class="sortable-clean" data-sort="cdp">
                                        <div class="th-content">
                                            <span>CDP</span>
                                            <i class="fas fa-sort sort-icon-clean"></i>
                                        </div>
                                    </th>
                                    <th class="sortable-clean" data-sort="fecha_registro">
                                        <div class="th-content">
                                            <span>F. Registro</span>
                                            <i class="fas fa-sort sort-icon-clean"></i>
                                        </div>
                                    </th>
                                    <th class="sortable-clean" data-sort="fecha_creacion">
                                        <div class="th-content">
                                            <span>F. Creación</span>
                                            <i class="fas fa-sort sort-icon-clean"></i>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="th-content">
                                            <span>Estado / Fuente</span>
                                        </div>
                                    </th>
                                    <th class="sortable-clean text-right" data-sort="valor_actual">
                                        <div class="th-content">
                                            <span>Valor Actual</span>
                                            <i class="fas fa-sort sort-icon-clean"></i>
                                        </div>
                                    </th>
                                    <th class="sortable-clean text-right" data-sort="saldo">
                                        <div class="th-content">
                                            <span>Saldo Disponible</span>
                                            <i class="fas fa-sort sort-icon-clean"></i>
                                        </div>
                                    </th>
                                    <th class="text-center">
                                        <div class="th-content">
                                            <span>Acciones</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php foreach ($initialData as $row): ?>
                                    <tr class="table-row-clean" data-documento="<?php echo htmlspecialchars($row['Numero_Documento']); ?>">
                                        <td class="font-mono font-semibold text-accent-600">
                                            <span class="cdp-number"><?php echo htmlspecialchars($row['Numero_Documento']); ?></span>
                                        </td>
                                        <td class="text-gray-600 text-sm">
                                            <?php echo htmlspecialchars($row['Fecha_de_Registro']); ?>
                                        </td>
                                        <td class="text-gray-600 text-sm">
                                            <?php echo htmlspecialchars($row['Fecha_de_Creacion']); ?>
                                        </td>
                                        <td>
                                            <div class="status-stack">
                                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['Estado'])); ?>">
                                                    <?php echo htmlspecialchars($row['Estado']); ?>
                                                </span>
                                                <div class="dept-info">
                                                    <span class="dept-name"><?php echo htmlspecialchars($row['Dependencia']); ?></span>
                                                    <span class="source-tag"><?php echo htmlspecialchars($row['Fuente']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <span class="money-value money-positive">
                                                <?php echo htmlspecialchars('$ ' . number_format((float)$row['Valor_Actual'], 2, '.', ',')); ?>
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <span class="money-value money-available">
                                                <?php echo htmlspecialchars('$ ' . number_format((float)$row['Saldo_por_Comprometer'], 2, '.', ',')); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="control/CRP_asociado.php?cod_CDP=<?php echo htmlspecialchars($row['Numero_Documento']); ?>" 
                                               class="action-link" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="empty-state-clean" style="display: none;">
                        <div class="empty-icon-clean">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="empty-title-clean">No hay resultados</h3>
                        <p class="empty-text-clean">Intenta ajustar los filtros de búsqueda</p>
                    </div>

                    <!-- Table Footer -->
                    <div class="table-footer">
                        <div class="table-info">
                            <span id="tableInfo">Mostrando registros de CDP</span>
                        </div>
                        <div class="table-pagination">
                            <!-- Pagination controls could go here -->
                        </div>
                    </div>                </div>
            </main>            <!-- Mobile Chart Container (shown below table on small screens) -->
            <div class="mobile-chart-container" id="mobileChartContainer">
                <h3 class="mobile-chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Resumen de Presupuesto
                </h3>
                <div class="mobile-chart-content" id="mobileChartContent">
                    <!-- Chart content will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Mobile Filters Modal -->
        <div class="mobile-filters-modal" id="mobileFiltersModal">
            <div class="mobile-filters-modal-content">                <div class="mobile-filters-header">
                    <h3>
                        <i class="fas fa-sliders-h"></i>
                        Filtros
                    </h3>
                    <button class="close-mobile-filters" id="mobileFiltersClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mobile-filters-body">
                    <form id="mobileFilterForm" class="filter-form">
                        <div class="filter-field">
                            <label class="field-label">Número CDP</label>
                            <input type="text" 
                                   id="mobileNumeroDocumento" 
                                   name="numeroDocumento"
                                   placeholder="Ej: 125, 300..." 
                                   class="field-input">
                        </div>

                        <div class="filter-field">
                            <label class="field-label">Fuente</label>
                            <select id="mobileFuente" name="fuente" class="field-select">
                                <option value="Todos">Todas</option>
                                <option value="Nación">Nación</option>
                                <option value="Propios">Propios</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="field-label">Reintegros</label>
                            <select id="mobileReintegros" name="reintegros" class="field-select">
                                <option value="Todos">Todos</option>
                                <option value="Con reintegro">Con reintegro</option>
                                <option value="Sin reintegro">Sin reintegro</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="field-label">Registros por página</label>
                            <select id="mobileRegistrosPorPagina" name="registrosPorPagina" class="field-select">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="40">40</option>
                                <option value="60">60</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                    </form>

                    <!-- Active Filters in Mobile -->
                    <div id="mobile-filtros-activos" class="active-filters-mobile"></div>
                </div>
                  <div class="mobile-filters-actions">
                    <button type="button" id="mobileLimpiarFiltros" class="btn-secondary-mobile">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                    <button type="button" id="mobileAplicarFiltros" class="btn-primary-mobile">
                        <i class="fas fa-check"></i>
                        Aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>    $(document).ready(function(){
        // Variables globales
        let offset = 0; // ALWAYS start from 0
        let limit = <?php echo ($registrosPorPagina === 'todos' ? 999999 : intval($registrosPorPagina)); ?>;
        let isLoading = false;// Initialize UI components
        initializeUI();
        
        // Event handlers
        setupEventHandlers();
          // Initialize filters
        initializeFilters();
        
        // Setup mobile responsiveness
        setupMobileResponsiveness();
        
        // Debug: Verify elements exist
        console.log("Mobile toggle button exists:", $("#mobileFiltersToggle").length);
        console.log("Mobile modal exists:", $("#mobileFiltersModal").length);
        console.log("Mobile chart container exists:", $("#mobileChartContainer").length);
        console.log("Sidebar chart exists:", $(".dashboard-sidebar .quick-stats .dashboard-content-mini").length);        // Initialize functions
        function initializeUI() {
            // Hide loading spinner
            hideLoadingSpinner();
            
            // Initialize stats with initial data from PHP
            updateTotalCDP();
            
            // Update table info
            updateTableInfo();
            
            // Setup mobile responsiveness
            setupMobileResponsiveness();
            
            // Initialize tooltips
            initializeTooltips();
        }

        function setupEventHandlers() {
            // Filter toggle
            $("#toggleFilters").on("click", function() {
                const $content = $("#filtersContent");
                const $icon = $(this).find("i");
                
                $content.slideToggle(300);
                $icon.toggleClass("fa-chevron-up fa-chevron-down");
            });            // Refresh table button
            $("#refreshTable").on("click", function() {
                refreshTable();
            });

            // Export functionality
            $(".main-actions .action-btn").eq(1).on("click", function() {
                exportTableData();
            });

            // Settings functionality
            $(".main-actions .action-btn").eq(2).on("click", function() {
                showMessage("Configuración de vista disponible próximamente", "info");
            });

            // Clear filters
            $("#limpiarFiltros").on("click", function() {
                clearAllFilters();
            });            // Load more records
            $("#cargarMas").on("click", function() {
                loadMoreRecords();
            });

            // Mobile filters modal handlers
            setupMobileFiltersModal();

            // Dynamic search
            setupDynamicSearch();            // Sort functionality
            setupTableSorting();
            
            // Enhanced table scroll handling for sticky headers
            setupTableScrollHandling();
        }

        function setupDynamicSearch() {
            let typingTimer;
            const doneTypingInterval = 500;

            $("#numeroDocumento").on('keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(performSearch, doneTypingInterval);
            });

            $("#numeroDocumento").on('keydown', function() {
                clearTimeout(typingTimer);
            });

            $(".filtro-dinamico").on('change keyup', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(performSearch, doneTypingInterval);
                updateActiveFilters();
            });
        }        function setupTableSorting() {
            $(".sortable-clean").on("click", function() {
                const sortBy = $(this).data("sort");
                const $icon = $(this).find(".sort-icon-clean");
                
                // Verificar si hay demasiados registros y recomendar filtros
                const rowCount = $("#tablaCDP tbody tr").length;
                if (rowCount > 100) {
                    const hasActiveFilters = hasAnyActiveFilters();
                    if (!hasActiveFilters) {
                        showMessage("Para mejor rendimiento, se recomienda aplicar filtros antes de ordenar grandes cantidades de datos", "info");
                    }
                }
                
                // Determinar dirección del ordenamiento
                let sortDirection = 'asc';
                if ($icon.hasClass("fa-sort-up")) {
                    sortDirection = 'desc';
                } else if ($icon.hasClass("fa-sort-down")) {
                    sortDirection = 'asc';
                }
                  // Reset other sort icons and remove active classes
                $(".sort-icon-clean").removeClass("fa-sort-up fa-sort-down").addClass("fa-sort");
                $(".sortable-clean").removeClass("active-sort");
                
                // Set current sort icon and active class
                $(this).addClass("active-sort");
                if (sortDirection === 'asc') {
                    $icon.removeClass("fa-sort fa-sort-down").addClass("fa-sort-up");
                } else {
                    $icon.removeClass("fa-sort fa-sort-up").addClass("fa-sort-down");
                }
                
                // Ordenar la tabla con los datos actuales
                sortTableData(sortBy, sortDirection);
                
                console.log(`Ordenando tabla por: ${sortBy} (${sortDirection})`);
                showMessage(`Tabla ordenada por: ${getColumnDisplayName(sortBy)} (${sortDirection === 'asc' ? 'Ascendente' : 'Descendente'})`, 'info');
            });
        }

        function hasAnyActiveFilters() {
            const numeroDoc = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();
            const registros = $("#registrosPorPagina").val();
            
            return numeroDoc !== '' || fuente !== 'Todos' || reintegros !== 'Todos' || registros !== '10';
        }

        function sortTableData(sortBy, direction) {
            const $tbody = $("#tablaCDP tbody");
            const $rows = $tbody.find("tr").toArray();
            
            // Mostrar indicador de ordenamiento
            const $loadingIndicator = $('<div class="sorting-indicator">Ordenando...</div>');
            $tbody.before($loadingIndicator);
            
            // Ordenar las filas según el criterio seleccionado
            $rows.sort(function(a, b) {
                let valueA = getSortValue($(a), sortBy);
                let valueB = getSortValue($(b), sortBy);
                
                // Comparar valores según el tipo de datos
                let comparison = 0;
                
                if (sortBy === 'valor_actual' || sortBy === 'saldo') {
                    // Para valores monetarios, convertir a número
                    valueA = parseFloat(valueA.replace(/[\$,]/g, '')) || 0;
                    valueB = parseFloat(valueB.replace(/[\$,]/g, '')) || 0;
                    comparison = valueA - valueB;
                } else if (sortBy === 'fecha_registro' || sortBy === 'fecha_creacion') {
                    // Para fechas, convertir a objetos Date
                    valueA = new Date(valueA) || new Date(0);
                    valueB = new Date(valueB) || new Date(0);
                    comparison = valueA - valueB;
                } else if (sortBy === 'cdp') {
                    // Para CDP (números), convertir a entero
                    valueA = parseInt(valueA) || 0;
                    valueB = parseInt(valueB) || 0;
                    comparison = valueA - valueB;
                } else {
                    // Para texto, comparación alfabética
                    comparison = valueA.localeCompare(valueB, 'es', { numeric: true });
                }
                
                return direction === 'asc' ? comparison : -comparison;
            });
              // Limpiar tbody y agregar filas ordenadas con animación mejorada
            $tbody.empty();
            
            // Eliminar indicador de carga después de un breve delay
            setTimeout(() => {
                $loadingIndicator.remove();
                
                $rows.forEach(function(row, index) {
                    const $row = $(row);
                    $row.addClass('sorting-in').hide();
                    $tbody.append($row);
                    
                    // Animación escalonada mejorada para mejor UX
                    setTimeout(() => {
                        $row.fadeIn(200, function() {
                            $(this).removeClass('sorting-in');
                        });
                    }, index * 25);
                });
                
                // Actualizar estadísticas después del ordenamiento
                setTimeout(() => {
                    updateTotalCDP();
                }, 300);
            }, 200);
        }

        function getSortValue($row, sortBy) {
            switch(sortBy) {
                case 'cdp':
                    return $row.find('.cdp-number').text().trim();
                case 'fecha_registro':
                    return $row.find('td:nth-child(2)').text().trim();
                case 'fecha_creacion':
                    return $row.find('td:nth-child(3)').text().trim();
                case 'valor_actual':
                    return $row.find('.money-positive').text().trim();
                case 'saldo':
                    return $row.find('.money-available').text().trim();
                default:
                    return '';
            }
        }

        function getColumnDisplayName(sortBy) {
            const displayNames = {
                'cdp': 'CDP',
                'fecha_registro': 'Fecha Registro',
                'fecha_creacion': 'Fecha Creación',
                'valor_actual': 'Valor Actual',
                'saldo': 'Saldo Disponible'
            };
            return displayNames[sortBy] || sortBy;
        }function setupMobileResponsiveness() {
            // Handle responsive sidebar collapse and mobile chart initialization
            handleResponsiveChanges();

            $(window).resize(function() {
                handleResponsiveChanges();
            });

            // Setup mobile filters modal - CRITICAL FIX
            console.log("Setting up mobile filters modal from setupMobileResponsiveness...");
            setupMobileFiltersModal();
        }

        function handleResponsiveChanges() {
            const isMobile = window.innerWidth <= 1024;
            console.log("Handle responsive changes - isMobile:", isMobile);
            
            if (isMobile) {
                $('.dashboard-sidebar').addClass('mobile-sidebar');
                
                // Initialize mobile chart on mobile view
                initializeMobileChart();
            } else {
                $('.dashboard-sidebar').removeClass('mobile-sidebar');
                
                // Hide mobile chart container on desktop
                $(".mobile-chart-container").hide();
                $("#mobileChartContent").empty();
            }
        }

        function initializeMobileChart() {
            console.log("Initializing mobile chart...");
            
            const mobileChartContent = $("#mobileChartContent");
            const sidebarChartContent = $(".dashboard-sidebar .quick-stats .dashboard-content-mini");
            
            // Show mobile chart container
            $(".mobile-chart-container").show();
            
            // Only initialize if mobile chart is empty and sidebar chart exists
            if (mobileChartContent.children().length === 0 && sidebarChartContent.length > 0) {
                console.log("Cloning chart content to mobile container");
                
                // Clone the chart content to mobile container
                const chartContent = sidebarChartContent.clone(true);
                mobileChartContent.empty().append(chartContent);
                
                // Re-execute any scripts in the cloned content
                setTimeout(() => {
                    try {
                        const scripts = mobileChartContent.find('script');
                        scripts.each(function() {
                            if (this.innerHTML.trim()) {
                                eval(this.innerHTML);
                            }
                        });
                        console.log("Mobile chart initialized successfully");
                    } catch (e) {
                        console.log("Mobile chart initialization completed with notes:", e.message);
                    }
                }, 300);
            } else if (mobileChartContent.children().length > 0) {
                console.log("Mobile chart already initialized");
            } else {
                console.log("Sidebar chart not available yet, waiting...");
                // Try again after a short delay
                setTimeout(() => {
                    initializeMobileChart();
                }, 500);
            }
        }

        function initializeTooltips() {
            // Simple tooltip implementation
            $("[title]").each(function() {
                $(this).hover(
                    function() {
                        const title = $(this).attr("title");
                        $(this).attr("data-original-title", title).removeAttr("title");
                        
                        $("<div class='tooltip-custom'>" + title + "</div>")
                            .appendTo("body")
                            .fadeIn(200);
                    },
                    function() {
                        $(this).attr("title", $(this).attr("data-original-title"));
                        $(".tooltip-custom").remove();
                    }
                );
            });
        }        function performSearch() {
            if (isLoading) return;
            
            console.log("=== PERFORM SEARCH START ===");
            showLoadingSpinner();
            
            const numeroDocumento = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();
            const registrosPorPagina = $("#registrosPorPagina").val();

            console.log("performSearch() - Filter values:");
            console.log("- numeroDocumento:", numeroDocumento);
            console.log("- fuente:", fuente);
            console.log("- reintegros:", reintegros);
            console.log("- registrosPorPagina:", registrosPorPagina);

            // Update limit variable
            limit = (registrosPorPagina === 'todos') ? 999999 : parseInt(registrosPorPagina);

            // Save filters to cookies
            saveFiltersToStorage(numeroDocumento, fuente, reintegros, registrosPorPagina);

            // Reset offset for new search - ALWAYS start from 0 for new searches
            offset = 0;
            
            console.log("performSearch() - Variables after update:");
            console.log("- limit:", limit);
            console.log("- offset:", offset);

            const ajaxData = {
                action: 'cargarMasCDP',
                numeroDocumento: numeroDocumento,
                fuente: fuente,
                reintegros: reintegros,
                offset: 0,
                limit: limit
            };
            
            console.log("performSearch() - AJAX data to send:", ajaxData);            $.ajax({
                url: './control/ajaxGestor.php',
                method: 'GET',
                data: ajaxData,
                dataType: 'json',
                success: function(response) {
                    console.log("performSearch() - AJAX success, response:", response);
                    console.log("performSearch() - Response length:", response.length);
                    updateTableWithNewData(response);
                    updateTableInfo();
                    resetTableSorting(); // Resetear ordenamiento al aplicar filtros
                    hideLoadingSpinner();
                    console.log("=== PERFORM SEARCH SUCCESS ===");
                },
                error: function(xhr, status, error) {
                    console.error("performSearch() - AJAX error:", error);
                    console.error("performSearch() - XHR:", xhr);
                    console.error("performSearch() - Status:", status);
                    showErrorMessage("Error al realizar la búsqueda");
                    hideLoadingSpinner();
                    console.log("=== PERFORM SEARCH ERROR ===");
                }
            });
        }

        function loadMoreRecords() {
            if (isLoading) return;
            
            const numeroDocumento = $("#numeroDocumento").val();
            const fuente = $("#fuente").val();
            const reintegros = $("#reintegros").val();

            showLoadingSpinner();

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
                        appendTableData(response);
                        offset += limit;
                        updateTableInfo();
                    } else {
                        showMessage("No hay más registros para mostrar", "info");
                        $("#cargarMas").hide();
                    }
                    hideLoadingSpinner();
                },
                error: function() {
                    showErrorMessage("Error al cargar más registros");
                    hideLoadingSpinner();
                }
            });        }        function resetTableSorting() {
            // Resetear todos los iconos de ordenamiento a su estado por defecto
            $(".sort-icon-clean").removeClass("fa-sort-up fa-sort-down").addClass("fa-sort");
            $(".sortable-clean").removeClass("active-sort");
            console.log("Ordenamiento de tabla reseteado");
        }

        function clearAllFilters() {
            // Clear form
            $("#numeroDocumento").val('');
            $("#fuente").val('Todos');
            $("#reintegros").val('Todos');
            $("#registrosPorPagina").val('10');
            
            // Reset variables
            limit = 10;
            offset = 0; // Corregido: debe ser 0 para empezar desde el principio
            
            // Clear storage
            clearFiltersFromStorage();
            
            // Reset table sorting
            resetTableSorting();
            
            // Reload data
            performSearch();
            
            // Update UI
            updateActiveFilters();
            $("#cargarMas").show();
        }function updateActiveFilters() {
            const numeroDoc = $("#numeroDocumento").val();
            const fuenteVal = $("#fuente").val();
            const reintegrosVal = $("#reintegros").val();
            const registrosVal = $("#registrosPorPagina").val();

            let filtersHTML = '';
            let hasFilters = false;

            if (numeroDoc) {
                filtersHTML += `<span class="filter-tag-clean"><i class="fas fa-hashtag"></i> CDP: ${numeroDoc}</span>`;
                hasFilters = true;
            }
            if (fuenteVal !== 'Todos') {
                filtersHTML += `<span class="filter-tag-clean"><i class="fas fa-money-bill-wave"></i> Fuente: ${fuenteVal}</span>`;
                hasFilters = true;
            }
            if (reintegrosVal !== 'Todos') {
                filtersHTML += `<span class="filter-tag-clean"><i class="fas fa-undo"></i> Reintegros: ${reintegrosVal}</span>`;
                hasFilters = true;
            }
            if (registrosVal !== '10') {
                filtersHTML += `<span class="filter-tag-clean"><i class="fas fa-list-ol"></i> Registros: ${registrosVal}</span>`;
                hasFilters = true;
            }

            // Update desktop active filters
            const $activeFilters = $("#filtros-activos");
            if (hasFilters) {
                $activeFilters.html(`
                    <div class="active-filters-header">
                        <i class="fas fa-filter"></i>
                        <span>Filtros aplicados:</span>
                    </div>
                    <div class="active-filters-list">
                        ${filtersHTML}
                    </div>
                `).show();
            } else {
                $activeFilters.hide();
            }

            // Update mobile active filters - CRITICAL FIX
            const $mobileActiveFilters = $("#mobile-filtros-activos");
            if (hasFilters) {
                $mobileActiveFilters.html(`
                    <div class="active-filters-header">
                        <i class="fas fa-filter"></i>
                        <span>Filtros aplicados:</span>
                    </div>
                    <div class="active-filters-list">
                        ${filtersHTML}
                    </div>
                `).show();
            } else {
                $mobileActiveFilters.hide();
            }
        }

        function updateTableWithNewData(response) {
            const $tbody = $("#tablaCDP tbody");
            $tbody.empty();

            if(response.length > 0) {
                appendTableData(response);
                $("#cargarMas").show();
                hideEmptyState();
            } else {
                showEmptyState();
                $("#cargarMas").hide();
            }
        }

        function appendTableData(response) {
            const $tbody = $("#tablaCDP tbody");
            
            response.forEach(function(row) {
                const $row = createTableRow(row);
                $tbody.append($row);
                
                // Add animation
                $row.hide().fadeIn(300);
            });
            
            updateTotalCDP();
        }        function createTableRow(row) {
            return $(`
                <tr class="table-row-clean" data-documento="${row.Numero_Documento}">
                    <td class="font-mono font-semibold text-accent-600">
                        <span class="cdp-number">${row.Numero_Documento}</span>
                    </td>
                    <td class="text-gray-600 text-sm">
                        ${row.Fecha_de_Registro}
                    </td>
                    <td class="text-gray-600 text-sm">
                        ${row.Fecha_de_Creacion}
                    </td>
                    <td>
                        <div class="status-stack">
                            <span class="status-badge status-${row.Estado.toLowerCase().replace(' ', '-')}">
                                ${row.Estado}
                            </span>
                            <div class="dept-info">
                                <span class="dept-name">${row.Dependencia}</span>
                                <span class="source-tag">${row.Fuente}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <span class="money-value money-positive">
                            ${formatCurrency(row.Valor_Actual)}
                        </span>
                    </td>
                    <td class="text-right">
                        <span class="money-value money-available">
                            ${formatCurrency(row.Saldo_por_Comprometer)}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="control/CRP_asociado.php?cod_CDP=${row.Numero_Documento}" 
                           class="action-link" 
                           title="Ver detalles del CDP">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            `);
        }

        function formatCurrency(value) {
            const number = parseFloat(value) || 0;
            return '$ ' + number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }        function showEmptyState() {
            $("#emptyState").show();
            $("#tablaCDP").hide();
        }

        function hideEmptyState() {
            $("#emptyState").hide();
            $("#tablaCDP").show();
        }

        function updateTableInfo() {
            const rowCount = $("#tablaCDP tbody tr").length;
            $("#tableInfo").text(`Mostrando ${rowCount} registros de CDP`);
        }        function updateTotalCDP() {
            const rowCount = $("#tablaCDP tbody tr").length;
            $("#totalCDP").text(rowCount);
            
            // Calculate total money value from visible rows
            let totalMonto = 0;
            $("#tablaCDP tbody tr").each(function() {
                const valorText = $(this).find('.money-value.money-positive').text();
                const valor = parseFloat(valorText.replace(/[$,]/g, '')) || 0;
                totalMonto += valor;
            });
            
            $("#totalMonto").text(formatCurrency(totalMonto));
            
            // Add animation to stats
            $('.stat-value').addClass('stat-updated');
            setTimeout(() => {
                $('.stat-value').removeClass('stat-updated');
            }, 500);
        }

        function showLoadingSpinner() {
            isLoading = true;
            $("#loadingSpinner").show();
        }

        function hideLoadingSpinner() {
            isLoading = false;
            $("#loadingSpinner").hide();
        }        function showMessage(message, type = 'success') {
            // Professional toast notification for dashboard
            const iconClass = type === 'error' ? 'exclamation-circle' : type === 'info' ? 'info-circle' : 'check-circle';
            const bgClass = type === 'error' ? 'bg-red-500' : type === 'info' ? 'bg-blue-500' : 'bg-green-500';
            
            const $toast = $(`
                <div class="toast-message ${bgClass} text-white">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-${iconClass}"></i>
                        <span class="font-medium">${message}</span>
                        <button class="toast-close ml-auto">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);
            
            $("body").append($toast);
            
            // Close button functionality
            $toast.find('.toast-close').on('click', function() {
                $toast.fadeOut(300, function() {
                    $(this).remove();
                });
            });
            
            // Auto close
            setTimeout(() => {
                $toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 4000);
        }

        function showErrorMessage(message) {
            showMessage(message, 'error');
        }        function refreshTable() {
            showLoadingSpinner();
            showMessage("Actualizando datos...", "info");
            setTimeout(() => {
                performSearch();
            }, 500);
        }

        function exportTableData() {
            showMessage("Preparando exportación...", "info");
            
            // Get current table data
            const tableData = [];
            $("#tablaCDP tbody tr").each(function() {
                if ($(this).find('.cdp-number').length > 0) {
                    const row = {
                        CDP: $(this).find('.cdp-number').text(),
                        'Fecha Registro': $(this).find('td').eq(1).text().trim(),
                        'Fecha Creación': $(this).find('td').eq(2).text().trim(),
                        Estado: $(this).find('.status-badge').text().trim(),
                        Dependencia: $(this).find('.dept-name').text().trim(),
                        Fuente: $(this).find('.source-tag').text().trim(),
                        'Valor Actual': $(this).find('.money-positive').text().trim(),
                        'Saldo Disponible': $(this).find('.money-available').text().trim()
                    };
                    tableData.push(row);
                }
            });

            if (tableData.length === 0) {
                showMessage("No hay datos para exportar", "error");
                return;
            }

            // Create CSV content
            const headers = Object.keys(tableData[0]);
            let csvContent = headers.join(',') + '\n';
            
            tableData.forEach(row => {
                const values = headers.map(header => `"${row[header]}"`);
                csvContent += values.join(',') + '\n';
            });

            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `CDP_Export_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
            
            showMessage(`Exportados ${tableData.length} registros exitosamente`, "success");
        }

        // Storage functions
        function saveFiltersToStorage(numeroDocumento, fuente, reintegros, registrosPorPagina) {
            setCookie('filtro_numeroDocumento', numeroDocumento);
            setCookie('filtro_fuente', fuente);
            setCookie('filtro_reintegros', reintegros);
            setCookie('filtro_registrosPorPagina', registrosPorPagina);
        }

        function clearFiltersFromStorage() {
            setCookie('filtro_numeroDocumento', '');
            setCookie('filtro_fuente', 'Todos');
            setCookie('filtro_reintegros', 'Todos');
            setCookie('filtro_registrosPorPagina', '10');
        }

        function setCookie(name, value, days = 30) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }        function initializeFilters() {
            // Load saved filters
            const cookieRegistros = getCookie('filtro_registrosPorPagina');
            
            if(cookieRegistros) {
                $("#registrosPorPagina").val(cookieRegistros);
                
                if(cookieRegistros === 'todos') {
                    limit = 999999;
                    offset = 0;
                    $("#cargarMas").hide();
                } else {
                    limit = parseInt(cookieRegistros);
                    offset = 0; // ALWAYS start from 0, not from registrosPorPagina
                }
            }
            
            // Update UI
            updateActiveFilters();
            updateTotalCDP();
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }        // Handle registros por pagina change
        $("#registrosPorPagina").on('change', function() {
            const valorSeleccionado = $(this).val();
            
            limit = valorSeleccionado === 'todos' ? 999999 : parseInt(valorSeleccionado);
            offset = 0; // ALWAYS start from 0 when changing registrosPorPagina

            if(valorSeleccionado === 'todos') {
                $("#cargarMas").hide();
            } else {
                $("#cargarMas").show();
            }

            performSearch();        });

        // Setup Mobile Filters Modal
        function setupMobileFiltersModal() {
            console.log("Setting up mobile filters modal...");
            
            // Open mobile filters modal
            $("#mobileFiltersToggle").on("click", function() {
                console.log("Mobile filters toggle clicked");
                openMobileFiltersModal();
            });

            // Close mobile filters modal
            $("#mobileFiltersClose").on("click", function() {
                console.log("Mobile filters close clicked");
                closeMobileFiltersModal();
            });

            // Close modal when clicking backdrop
            $("#mobileFiltersModal").on("click", function(e) {
                if (e.target === this) {
                    closeMobileFiltersModal();
                }
            });

            // Apply filters from mobile modal
            $("#mobileAplicarFiltros").on("click", function() {
                console.log("Apply mobile filters clicked");
                applyMobileFilters();
                closeMobileFiltersModal();
            });

            // Clear filters from mobile modal
            $("#mobileLimpiarFiltros").on("click", function() {
                console.log("Clear mobile filters clicked");
                clearMobileFilters();
            });

            // Sync mobile filters with desktop on changes
            setupMobileFilterSync();
        }

        function openMobileFiltersModal() {
            console.log("Opening mobile filters modal");
            
            // Sync current desktop filters to mobile
            syncDesktopToMobile();
            
            // Show modal
            $("#mobileFiltersModal").addClass("active").css("display", "flex");
            $("body").addClass("modal-open");
            
            console.log("Mobile filters modal opened");
        }

        function closeMobileFiltersModal() {
            $("#mobileFiltersModal").removeClass("active");
            setTimeout(() => {
                $("#mobileFiltersModal").css("display", "none");
                $("body").removeClass("modal-open");
            }, 300);
        }

        function syncDesktopToMobile() {
            // Sync all filter values from desktop to mobile
            $("#mobileNumeroDocumento").val($("#numeroDocumento").val());
            $("#mobileFuente").val($("#fuente").val());
            $("#mobileReintegros").val($("#reintegros").val());
            $("#mobileRegistrosPorPagina").val($("#registrosPorPagina").val());
        }

        function syncMobileToDesktop() {
            // Sync all filter values from mobile to desktop
            $("#numeroDocumento").val($("#mobileNumeroDocumento").val());
            $("#fuente").val($("#mobileFuente").val());
            $("#reintegros").val($("#mobileReintegros").val());
            $("#registrosPorPagina").val($("#mobileRegistrosPorPagina").val());
        }        function applyMobileFilters() {
            console.log("=== APPLY MOBILE FILTERS START ===");
            
            // Log mobile values before sync
            console.log("Mobile values before sync:");
            console.log("- numeroDocumento:", $("#mobileNumeroDocumento").val());
            console.log("- fuente:", $("#mobileFuente").val());
            console.log("- reintegros:", $("#mobileReintegros").val());
            console.log("- registrosPorPagina:", $("#mobileRegistrosPorPagina").val());
            
            // Sync mobile filters to desktop
            syncMobileToDesktop();
            
            // Log desktop values after sync
            console.log("Desktop values after sync:");
            console.log("- numeroDocumento:", $("#numeroDocumento").val());
            console.log("- fuente:", $("#fuente").val());
            console.log("- reintegros:", $("#reintegros").val());
            console.log("- registrosPorPagina:", $("#registrosPorPagina").val());
            
            // Trigger desktop filter application
            console.log("Calling performSearch()...");
            performSearch();
            updateActiveFilters();
            
            // Update totals (chart will update automatically through sidebar updates)
            setTimeout(() => {
                updateTotalCDP();
            }, 500);
            
            // Show success message
            if (typeof showMessage === 'function') {
                showMessage("Filtros aplicados correctamente", "success");
            } else {
                console.log("Filtros aplicados correctamente");
            }
            
            console.log("=== APPLY MOBILE FILTERS END ===");
        }        function clearMobileFilters() {
            // Clear mobile form
            $("#mobileNumeroDocumento").val("");
            $("#mobileFuente").val("Todos");
            $("#mobileReintegros").val("Todos");
            $("#mobileRegistrosPorPagina").val("10");
            
            // Clear desktop filters
            clearAllFilters();
            
            // Update totals (chart will update automatically)
            setTimeout(() => {
                updateTotalCDP();
            }, 500);
            
            // Show info message
            if (typeof showMessage === 'function') {
                showMessage("Filtros limpiados", "info");
            } else {
                console.log("Filtros limpiados");
            }
        }        function setupMobileFilterSync() {
            // When desktop filters change, update mobile
            $("#numeroDocumento, #fuente, #reintegros, #registrosPorPagina").on("change input", function() {
                syncDesktopToMobile();
            });
            
            // Setup MutationObserver to watch for sidebar chart changes
            setupChartSyncObserver();
        }

        function setupChartSyncObserver() {
            const sidebarChart = document.querySelector('.dashboard-sidebar .quick-stats .dashboard-content-mini');
            
            if (sidebarChart && window.MutationObserver) {
                const observer = new MutationObserver(function(mutations) {
                    let shouldUpdate = false;
                    
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' || mutation.type === 'subtree') {
                            shouldUpdate = true;
                        }
                    });
                    
                    if (shouldUpdate && window.innerWidth < 1024) {
                        console.log("Sidebar chart changed, updating mobile chart");
                        updateMobileChart();
                    }
                });
                
                observer.observe(sidebarChart, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
                
                console.log("Chart sync observer set up successfully");
            }
        }        function updateMobileChart() {
            console.log("Updating mobile chart...");
            
            const isMobile = window.innerWidth < 1024;
            if (!isMobile) {
                console.log("Not mobile, skipping chart update");
                return;
            }
            
            const mobileChartContent = $("#mobileChartContent");
            const sidebarChartContent = $(".dashboard-sidebar .quick-stats .dashboard-content-mini");
            
            // Only update if sidebar chart exists
            if (sidebarChartContent.length > 0) {
                console.log("Updating mobile chart content");
                
                // Clear mobile chart content first to prevent duplicates
                mobileChartContent.empty();
                
                // Clone fresh content with a slight delay to ensure data is updated
                setTimeout(() => {
                    const chartContent = sidebarChartContent.clone(true, true);
                    mobileChartContent.append(chartContent);
                    
                    console.log("Mobile chart content updated successfully");
                }, 150);
            } else {
                console.log("Sidebar chart not available for mobile update");
            }        }

        // Enhanced table scroll handling for sticky headers
        function setupTableScrollHandling() {
            const $tableWrapper = $(".table-scroll");
            
            // Handle vertical scroll indicators
            function updateScrollIndicators($wrapper) {
                const scrollTop = $wrapper.scrollTop();
                const scrollHeight = $wrapper[0].scrollHeight;
                const clientHeight = $wrapper[0].clientHeight;
                const maxScrollTop = scrollHeight - clientHeight;
                
                // Add/remove classes for scroll indicators
                if (scrollTop > 5) {
                    $wrapper.addClass("scrolled-top");
                } else {
                    $wrapper.removeClass("scrolled-top");
                }
                
                if (scrollTop < maxScrollTop - 5) {
                    $wrapper.addClass("scrolled-bottom");
                } else {
                    $wrapper.removeClass("scrolled-bottom");
                }
            }
            
            // Setup scroll event handlers
            $tableWrapper.on("scroll", function() {
                const $this = $(this);
                
                // Handle horizontal scroll (existing functionality)
                const scrollLeft = $this.scrollLeft();
                const maxScroll = this.scrollWidth - this.clientWidth;
                
                if (scrollLeft > 0) {
                    $this.addClass("scrolled-left");
                } else {
                    $this.removeClass("scrolled-left");
                }
                
                if (scrollLeft < maxScroll - 5) {
                    $this.addClass("scrolled-right");
                } else {
                    $this.removeClass("scrolled-right");
                }
                
                // Handle vertical scroll indicators
                updateScrollIndicators($this);
            });
            
            // Initialize scroll indicators on load
            setTimeout(() => {
                updateScrollIndicators($tableWrapper);
            }, 100);
            
            console.log("Table scroll handling with sticky headers initialized");
        }

        // Initialize on page load
        $(window).on('load', function() {
            hideLoadingSpinner();
            
            // Initialize mobile chart after everything is loaded
            setTimeout(() => {
                if (window.innerWidth < 1024) {
                    initializeMobileChart();
                }
            }, 1000);
        });
    });
    </script>

    <!-- Modal Global para Gráfica de Presupuesto -->
    <div id="chartModalGlobal" class="chart-modal-global" style="display: none;">
        <div class="modal-content-global">
            <div class="modal-header-global">
                <h3>Distribución del Presupuesto</h3>
                <button class="close-modal-global" onclick="closeChartGlobal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-global">
                <div class="chart-container-global">
                    <canvas id="presupuestoChartGlobal"></canvas>
                </div>
                <div class="chart-stats-global">
                    <div class="stat-global">
                        <div class="stat-title-global">Valor Total</div>
                        <div class="stat-amount-global" id="valorTotalGlobal">$0.00</div>
                    </div>
                    <div class="stat-global">
                        <div class="stat-title-global">Saldo Disponible</div>
                        <div class="stat-amount-global available" id="saldoDisponibleGlobal">$0.00</div>
                        <div class="stat-percentage-global" id="porcentajeDisponibleGlobal">0%</div>
                    </div>
                    <div class="stat-global">
                        <div class="stat-title-global">Consumo CDP</div>
                        <div class="stat-amount-global consumed" id="consumoCdpGlobal">$0.00</div>
                        <div class="stat-percentage-global" id="porcentajeConsumidoGlobal">0%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos del Modal Global */
        .chart-modal-global {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
            box-sizing: border-box;
        }

        .modal-content-global {
            background: white;
            border-radius: 20px;
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            animation: modalAppearGlobal 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }

        @keyframes modalAppearGlobal {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-30px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header-global {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 28px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .modal-header-global h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: -0.025em;
        }

        .close-modal-global {
            background: #f1f5f9;
            border: none;
            border-radius: 10px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .close-modal-global:hover {
            background: #e2e8f0;
            color: #475569;
            transform: scale(1.05);
        }

        .modal-body-global {
            padding: 28px;
        }

        .chart-container-global {
            height: 350px;
            margin-bottom: 28px;
            background: #fafafa;
            border-radius: 16px;
            padding: 20px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .chart-stats-global {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .stat-global {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-global:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-title-global {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-amount-global {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            letter-spacing: -0.025em;
        }

        .stat-amount-global.available {
            color: #15803d;
        }

        .stat-amount-global.consumed {
            color: #0c4a6e;
        }

        .stat-percentage-global {
            font-size: 16px;
            font-weight: 600;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chart-modal-global {
                padding: 10px;
            }
            
            .modal-content-global {
                max-width: 100%;
                border-radius: 16px;
            }
            
            .chart-container-global {
                height: 280px;
                padding: 16px;
                margin-bottom: 20px;
            }
            
            .chart-stats-global {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .modal-body-global {
                padding: 20px;
            }
            
            .modal-header-global {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .chart-container-global {
                height: 250px;
            }
            
            .modal-body-global {
                padding: 16px;
            }
            
            .modal-header-global {
                padding: 16px;
            }        }
        
        /* Estilos adicionales para toast notifications */
        .toast-message {
            position: fixed;
            top: 90px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            opacity: 0;
            transform: translateX(400px);
            animation: slideInToast 0.3s ease forwards;
        }
        
        .toast-message .flex {
            display: flex;
        }
        
        .toast-message .items-center {
            align-items: center;
        }
        
        .toast-message .gap-3 {
            gap: 12px;
        }
        
        .toast-message .font-medium {
            font-weight: 500;
        }
        
        .toast-message .ml-auto {
            margin-left: auto;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            opacity: 0.8;
            transition: all 0.2s ease;
        }
        
        .toast-close:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.2);
        }
        
        @keyframes slideInToast {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Mejoras adicionales para el ordenamiento */
        .table-row-clean.sorting-out {
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }
        
        .table-row-clean.sorting-in {
            opacity: 0;
            transform: translateY(10px);
            animation: sortIn 0.3s ease forwards;
        }
        
        @keyframes sortIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Indicador visual de columna ordenada */
        .sortable-clean.active-sort {
            background-color: var(--accent-50) !important;
        }
        
        .sortable-clean.active-sort .th-content span {
            color: var(--accent-700);
        }
        
        /* Responsive improvements para sorting */
        @media (max-width: 768px) {
            .toast-message {
                top: 80px;
                right: 10px;
                left: 10px;
                min-width: auto;
                max-width: none;
                transform: translateY(-100px);
                animation: slideDownToast 0.3s ease forwards;
            }
            
            @keyframes slideDownToast {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }
    </style>
</body>
</html>