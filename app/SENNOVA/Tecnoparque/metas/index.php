<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';

requireRole(['4', '5', '6']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - Metas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Heroicons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard_content.css">



    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
/* Contenedor principal */
.dashboard-container {
    padding: 2rem;
    max-width: 1000px;
    margin: 0 auto;
    background: #f8fafc;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

/* Tarjetas de estadísticas */
.stats-card {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.stat-item {
    flex: 1;
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f1f5f9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.stat-item .stat-value {
    font-size: 1.8rem;
    font-weight: bold;
}
.stat-item .stat-label {
    font-size: 1rem;
    color: #64748b;
}

/* Tabla */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.styled-table th,
.styled-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}
.styled-table th {
    background: #2563eb;
    color: #fff;
    font-weight: bold;
}
.styled-table tr:hover {
    background: #f1f5f9;
}

/* Gráficas */
.chart-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .stats-card {
        flex-direction: column;
    }
    .dashboard-container {
        padding: 1rem;
    }
}
</style>
</head>
<body class="bg-gray-50 min-h-screen relative">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <!-- Botón para mostrar/ocultar sidebar: SIEMPRE visible, fuera del sidebar -->
    <button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Mostrar/Ocultar menú" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
    <!-- Overlay para móvil -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar tipo Filament -->
        <aside id="sidebarFilament" class="sidebar-filament bg-white border-r border-gray-200 flex flex-col h-screen fixed lg:static left-0 top-0">
            <div class="flex items-center border-b border-gray-200 relative h-16">
                <span class="text-xl font-bold text-blue-700 mx-auto" style="color: #2b3b4f;">Panel de Metas</span>
            </div>
            <nav class="flex-1 py-4">
                <ul>
                    <li>
                        <a href="#" id="navProyectosTecnologicos" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-device-laptop mr-3"></i> 100 Proyectos de Base Tecnológica
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navAsesorarAsociaciones" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-users-group mr-3"></i> Asesorar a 20 Asociaciones
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navAsesorarAprendices" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-school mr-3"></i> Asesorar a 1 Cooperativa de Aprendices
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navExtensionismo" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-rocket mr-3"></i> 5 Proyectos de Extensionismo Tecnológico
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navVisitasAprendices" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-route mr-3"></i> Visitas de Aprendices
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-200 text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> Gestor Tecnoparque
            </div>
        </aside>

        <!-- Contenido principal -->
        <main id="mainContentFilament" class="main-content-filament flex-1 min-h-screen transition-all duration-200" style="overflow: scroll;height: 100vh;display: flex;justify-content: center;align-items: flex-start;margin: 0;">
            <div id="dashboardProyectosTecnologicos">
                <?php require_once './ProyectosTec.php'; ?>
            </div>
            <div id="dashboardAsesorarAsociaciones" style="display:none;">
                <?php require_once './AsesorarAso.php'; ?>
            </div>
            <div id="dashboardAsesorarAprendices" style="display:none;">
                <?php require_once './AsesorarApre.php'; ?>
            </div>
            <div id="dashboardExtensionismo" style="display:none;">
                <?php require_once './ProyectosExt.php'; ?>
            </div>
            <div id="dashboardVisitasAprendices" style="display:none;">
                <?php require_once './VisitasApre.php'; ?>
            </div>
        </main>
    </div>
    <script>
    // Utilidad para cookies
    function setCookie(name, value, days = 30) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
        return null;
    }

    // Hacer utilidades globales para otros scripts
    window.setCookie = setCookie;
    window.getCookie = getCookie;

    document.addEventListener('DOMContentLoaded', function() {
        // Dashboards y navegación
        const dashboards = {
            proyectosTecnologicos: document.getElementById('dashboardProyectosTecnologicos'),
            asesorarAsociaciones: document.getElementById('dashboardAsesorarAsociaciones'),
            asesorarAprendices: document.getElementById('dashboardAsesorarAprendices'),
            extensionismo: document.getElementById('dashboardExtensionismo'),
            visitasAprendices: document.getElementById('dashboardVisitasAprendices')
        };
        const navLinks = {
            'navProyectosTecnologicos': 'proyectosTecnologicos',
            'navAsesorarAsociaciones': 'asesorarAsociaciones',
            'navAsesorarAprendices': 'asesorarAprendices',
            'navExtensionismo': 'extensionismo',
            'navVisitasAprendices': 'visitasAprendices'
        };

        function hideAllDashboards() {
            Object.values(dashboards).forEach(dashboard => {
                if (dashboard) dashboard.style.display = 'none';
            });
        }

        function showDashboard(id) {
            hideAllDashboards();
            const dashboard = dashboards[id];
            if (dashboard) dashboard.style.display = 'block';

            // Marcar activo en el lateral
            document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
            const navId = Object.entries(navLinks).find(([k, v]) => v === id)?.[0];
            if (navId) {
                const navElement = document.getElementById(navId);
                if (navElement) navElement.classList.add('active');
                setCookie('tecnoparque_metas_vista', id, 30);
            }
        }

        // Si no hay cookie, inicializa con la vista de proyectos tecnológicos
        let vista = getCookie('tecnoparque_metas_vista');
        if (!vista || !dashboards[vista]) {
            vista = 'proyectosTecnologicos';
            setCookie('tecnoparque_metas_vista', vista, 30);
        }
        showDashboard(vista);

        // Event listeners para la navegación
        Object.entries(navLinks).forEach(([navId, dashboardId]) => {
            const navElement = document.getElementById(navId);
            if (navElement) {
                navElement.addEventListener('click', function(e) {
                    e.preventDefault();
                    showDashboard(dashboardId);
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            }
        });

        // Sidebar toggle y responsive
        const sidebar = document.getElementById('sidebarFilament');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContentFilament');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const body = document.body;
        let sidebarOpen = window.innerWidth >= 1024;

        function openSidebar() {
            sidebar.classList.remove('closed');
            mainContent.classList.add('sidebar-open');
            body.classList.remove('sidebar-closed');
            if (window.innerWidth < 1024) {
                sidebarOverlay.classList.add('active');
            }
            sidebarOpen = true;
            sidebarToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            `;
        }
        function closeSidebar() {
            sidebar.classList.add('closed');
            mainContent.classList.remove('sidebar-open');
            body.classList.add('sidebar-closed');
            sidebarOverlay.classList.remove('active');
            sidebarOpen = false;
            sidebarToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="toggle-icon text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            `;
        }
        sidebarToggle.addEventListener('click', function() {
            if (sidebarOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
        function handleResize() {
            if (window.innerWidth < 1024) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
        window.addEventListener('resize', handleResize);

        // --- SISTEMA DE COOKIES PARA FILTROS DE VISITAS APRENDICES ---
        // Espera a que el dashboard de visitas esté visible
        function applyVisitasApreFiltersFromCookies() {
            const dashboard = document.getElementById('dashboardVisitasAprendices');
            if (!dashboard) return;
            // Espera a que existan los inputs de filtro (ajusta los IDs/names según tu HTML)
            const filtroIds = ['filtro-orden', 'filtro-limite', 'filtro-encargado', 'filtro-mes', 'filtro-anio'];
            filtroIds.forEach(id => {
                const input = dashboard.querySelector(`#${id}`);
                if (input) {
                    const cookieVal = getCookie('tecnoparque_visitasapre_' + id);
                    if (cookieVal !== null && cookieVal !== undefined && cookieVal !== '') {
                        input.value = cookieVal;
                    }
                }
            });
            // Si tienes función para recargar datos, llámala aquí (ejemplo):
            if (typeof window.recargarVisitasApre === 'function') {
                window.recargarVisitasApre();
            }
        }

        // Guardar filtros en cookies al cambiar
        function setupVisitasApreFilterListeners() {
            const dashboard = document.getElementById('dashboardVisitasAprendices');
            if (!dashboard) return;
            const filtroIds = ['filtro-orden', 'filtro-limite', 'filtro-encargado', 'filtro-mes', 'filtro-anio'];
            filtroIds.forEach(id => {
                const input = dashboard.querySelector(`#${id}`);
                if (input) {
                    input.addEventListener('change', function() {
                        setCookie('tecnoparque_visitasapre_' + id, input.value, 30);
                    });
                }
            });
        }

        // Cuando se muestra el dashboard de visitas, aplica filtros desde cookies y configura listeners
        function onShowVisitasApreDashboard() {
            applyVisitasApreFiltersFromCookies();
            setupVisitasApreFilterListeners();
        }

        // Modifica showDashboard para ejecutar lógica de filtros cuando corresponda
        const originalShowDashboard = showDashboard;
        showDashboard = function(id) {
            originalShowDashboard(id);
            if (id === 'visitasAprendices') {
                setTimeout(onShowVisitasApreDashboard, 100); // Espera a que se renderice el contenido
            }
        };

        // Si la vista inicial es visitas, aplica filtros
        if (vista === 'visitasAprendices') {
            setTimeout(onShowVisitasApreDashboard, 100);
        }
    });
    </script>
    <style>
.sidebar-link.active {
    background-color: #f0f6ff;
    color: #2563eb;
    border-right: 3px solid #2563eb;
}
.dashboard-container {
    padding: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}
.stats-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
}
    </style>
</body>
</html>