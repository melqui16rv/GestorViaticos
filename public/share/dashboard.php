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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard_content.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/grafica.css">
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
                <span class="text-xl font-bold text-blue-700 mx-auto">Panel de Control</span>
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
        <main id="mainContentFilament" class="main-content-filament flex-1 min-h-screen transition-all duration-200" style="overflow: scroll;height: 100vh;display: flex;justify-content: center;align-items: flex-start;margin: 0;">
            <div id="dashboardView">
                <?php require 'dashboard_content.php'; ?>
            </div>
            <div id="graficasView">
                <?php require 'Graficas.php'; ?>
            </div>
        </main>
    </div>

    <script>
        // Configurar la altura del nav para los estilos
        document.addEventListener('DOMContentLoaded', function() {
            // Intentar obtener la altura real del nav
            const navElement = document.querySelector('nav'); // Ajusta este selector según tu estructura
            if (navElement) {
                const navHeight = navElement.offsetHeight;
                document.documentElement.style.setProperty('--nav-height', navHeight + 'px');
            }
            
            // Inicializar la primera vista (dashboard por defecto)
            showDashboard();
            
            // Inicializar el estado del sidebar según el tamaño de pantalla
            handleResize();
        });
    
        // Sidebar navegación
        const dashboardView = document.getElementById('dashboardView');
        const graficasView = document.getElementById('graficasView');
        const navDashboard = document.getElementById('navDashboard');
        const navGraficas = document.getElementById('navGraficas');
        const sidebar = document.getElementById('sidebarFilament');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContentFilament');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const body = document.body;

        function showDashboard() {
            dashboardView.style.display = 'block';
            graficasView.style.display = 'none';
            navDashboard.classList.add('active');
            navGraficas.classList.remove('active');
        }
        
        function showGraficas() {
            dashboardView.style.display = 'none';
            graficasView.style.display = 'block';
            navDashboard.classList.remove('active');
            navGraficas.classList.add('active');
        }
        
        navDashboard.addEventListener('click', function(e) {
            e.preventDefault();
            showDashboard();
            if (window.innerWidth < 1024) {
                closeSidebar(); // En móvil, cerrar sidebar después de navegar
            }
        });
        
        navGraficas.addEventListener('click', function(e) {
            e.preventDefault();
            showGraficas();
            if (window.innerWidth < 1024) {
                closeSidebar(); // En móvil, cerrar sidebar después de navegar
            }
        });

        // Sidebar toggle
        let sidebarOpen = window.innerWidth >= 1024;
        
        function openSidebar() {
            sidebar.classList.remove('closed');
            mainContent.classList.add('sidebar-open');
            body.classList.remove('sidebar-closed');
            
            if (window.innerWidth < 1024) {
                sidebarOverlay.classList.add('active');
            }
            
            sidebarOpen = true;
            
            // Cambiar el icono a un "×" (cerrar)
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
            
            // Cambiar el icono a "≡" (hamburguesa)
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

        // Responsive: cerrar sidebar por defecto en móvil
        function handleResize() {
            if (window.innerWidth < 1024) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
        
        window.addEventListener('resize', handleResize);
    </script>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>