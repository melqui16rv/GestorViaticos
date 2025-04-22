<?php
session_start();
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';

if (!isset($_SESSION['id_rol'])) {
    header("Location: includes/session/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Heroicons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/grafica.css">
    <style>
        /* Firefox */
        body {
        scrollbar-width: none; /* Oculta la barra en Firefox */
        -ms-overflow-style: none; /* Oculta la barra en IE y Edge antiguos */
        }

        /* WebKit (Chrome, Safari, Edge moderno, Opera) */
        body::-webkit-scrollbar {
        display: none; /* Oculta la barra en navegadores WebKit */
        }

        body { background: #f3f4f6; }
        .sidebar-link.active, .sidebar-link:hover {
            background: #e0e7ef;
            color: #2563eb;
        }
        .sidebar-link {
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-filament {
            transition: all 0.2s;
            min-width: 16rem;
            width: 16rem;
            max-width: 100vw;
        }
        .sidebar-filament.closed {
            margin-left: -16rem;
        }
        @media (max-width: 1023px) {
            .sidebar-filament {
                position: fixed;
                z-index: 40;
                height: 100vh;
                left: 0;
                top: 0;
                background: #fff;
                border-right: 1px solid #e5e7eb;
                box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            }
            .sidebar-filament.closed {
                margin-left: -16rem;
            }
            .sidebar-overlay {
                display: block;
            }
        }
        .sidebar-toggle-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 100;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.4rem 0.6rem;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            display: flex !important;
            align-items: center;
            transition: background 0.2s;
        }
        .sidebar-toggle-btn:hover {
            background: #f3f4f6;
        }
        @media (min-width: 1024px) {
            .sidebar-toggle-btn {
                display: flex !important;
            }
        }
        .main-content-filament {
            flex: 1 1 0%;
            min-width: 0;
            transition: margin-left 0.2s;
        }
        @media (max-width: 1023px) {
            .main-content-filament {
                margin-left: 0 !important;
            }
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            z-index: 30;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.25);
            transition: opacity 0.2s;
        }
        .sidebar-overlay.active {
            display: block;
        }
        #dashboardView{
            width: 100%;
        }
        #graficasView{
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen relative">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>

    <!-- Botón para mostrar/ocultar sidebar: SIEMPRE visible, fuera del sidebar -->
    <button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Mostrar/Ocultar menú" type="button"
        style="top: 5.5rem; left: 1rem; display: flex;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <!-- Overlay para móvil -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar tipo Filament -->
        <aside id="sidebarFilament" class="sidebar-filament bg-white border-r border-gray-200 flex flex-col h-screen fixed lg:static left-0 top-0">
            <div class="flex items-center border-b border-gray-200 relative h-16">
                <span class="text-xl font-bold text-blue-700 mx-auto" style="margin-top: 50px;">Panel de Control</span>
            </div>
            <nav class="flex-1 py-4">
                <ul>
                    <li>
                        <a href="#" id="navDashboard" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-layout-dashboard mr-3"></i> Historial Actualizaciones
                        </a>
                    </li>
                    <li>
                        <a href="#" id="navGraficas" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                            <i class="ti ti-chart-pie-2 mr-3"></i> Gráficas
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-200 text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> Gestor Viáticos
            </div>
        </aside>

        <!-- Contenido principal -->
        <main id="mainContentFilament" class="main-content-filament flex-1 min-h-screen ml-64 transition-all duration-200" style="overflow: scroll;height: 100vh;display: flex;justify-content: center;align-items: flex-start;margin: 0;">
            <div id="dashboardView">
                <?php require 'dashboard_content.php'; ?>
            </div>
            <div id="graficasView" style="display:none;">
                <?php require 'Graficas.php'; ?>
            </div>
        </main>
    </div>
    <script>
        // Sidebar navegación
        const dashboardView = document.getElementById('dashboardView');
        const graficasView = document.getElementById('graficasView');
        const navDashboard = document.getElementById('navDashboard');
        const navGraficas = document.getElementById('navGraficas');
        const sidebar = document.getElementById('sidebarFilament');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContentFilament');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function showDashboard() {
            dashboardView.style.display = '';
            graficasView.style.display = 'none';
            navDashboard.classList.add('active');
            navGraficas.classList.remove('active');
        }
        function showGraficas() {
            dashboardView.style.display = 'none';
            graficasView.style.display = '';
            navDashboard.classList.remove('active');
            navGraficas.classList.add('active');
        }
        navDashboard.addEventListener('click', function(e) {
            e.preventDefault();
            showDashboard();
        });
        navGraficas.addEventListener('click', function(e) {
            e.preventDefault();
            showGraficas();
        });
        // Por defecto mostrar dashboard
        showDashboard();

        // Sidebar toggle
        let sidebarOpen = window.innerWidth >= 1024;
        function openSidebar() {
            sidebar.classList.remove('closed');
            if (window.innerWidth < 1024) {
                sidebarOverlay.classList.add('active');
            }
            if (window.innerWidth >= 1024) {
                mainContent.classList.add('ml-64');
            }
            sidebarOpen = true;
        }
        function closeSidebar() {
            sidebar.classList.add('closed');
            sidebarOverlay.classList.remove('active');
            if (window.innerWidth >= 1024) {
                mainContent.classList.remove('ml-64');
            }
            sidebarOpen = false;
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

        // Responsive: cerrar sidebar por defecto en móvil
        function handleResize() {
            if (window.innerWidth < 1024) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
        window.addEventListener('resize', handleResize);
        handleResize();

        // Forzar mostrar el botón siempre
        document.getElementById('sidebarToggle').style.display = 'flex';
    </script>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>
