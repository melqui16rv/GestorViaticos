<?php
ob_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosCDP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosCRP.php';

requireRole(['1']);
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
            background: #f3f4f6;
        }

        /* WebKit (Chrome, Safari, Edge moderno, Opera) */
        body::-webkit-scrollbar {
            display: none; /* Oculta la barra en navegadores WebKit */
        }

        /* Calculamos la altura adecuada para el botón considerando el nav */
        :root {
            --nav-height: 60px; /* Ajusta esto según la altura de tu barra de navegación */
        }

        /* Estilos mejorados para simular Filament UI */
        .sidebar-toggle-btn {
            position: fixed;
            top: calc(var(--nav-height) + 1rem); /* Posición debajo del nav */
            left: 1rem;
            z-index: 30; /* Un poco menor que el nav que suele ser 40-50 */
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.35rem;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex !important;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.2s;
            width: 2.25rem;
            height: 2.25rem;
        }

        .sidebar-toggle-btn:hover {
            background: #f3f4f6;
            transform: scale(1.05);
        }

        .sidebar-toggle-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Mejora en la sidebar al estilo Filament */
        .sidebar-filament {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 16rem;
            width: 16rem;
            max-width: 100vw;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            z-index: 20; /* Por debajo del nav y del botón */
            background-color: white;
            padding-top: var(--nav-height); /* Añadir espacio para el nav */
        }

        .sidebar-filament.closed {
            margin-left: -16rem;
        }

        /* Mejora en el contenido principal */
        .main-content-filament {
            flex: 1 1 0%;
            min-width: 0;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: calc(var(--nav-height) + 1rem); /* Espacio para el nav */
        }

        /* Pantallas medianas y grandes */
        @media (min-width: 1024px) {
            .sidebar-toggle-btn {
                top: calc(var(--nav-height) + 1.25rem);
                left: 1.25rem;
            }
            
            /* Cuando sidebar está abierto en pantallas grandes */
            .main-content-filament.sidebar-open {
                margin-left: 16rem;
                padding-left: 2rem;
            }
        }

        /* Pantallas pequeñas */
        @media (max-width: 1023px) {
            .sidebar-filament {
                position: fixed;
                height: 100vh;
                left: 0;
                top: 0;
            }
            
            .sidebar-toggle-btn {
                top: calc(var(--nav-height) + 0.75rem);
                left: 0.75rem;
            }
            
            /* Ajuste para crear espacio y evitar sobreposición */
            .main-content-filament {
                margin-left: 0 !important;
            }
            
            /* Cuando el sidebar está cerrado en móvil */
            body.sidebar-closed .main-content-filament {
                padding-left: 3.5rem;
            }
        }

        /* Mejorar el overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            z-index: 15; /* Por debajo del sidebar y botón */
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(2px);
            transition: opacity 0.3s;
            opacity: 0;
            /* Aseguramos que empiece debajo del nav */
            margin-top: var(--nav-height);
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Mejoras en los enlaces del sidebar */
        .sidebar-link {
            transition: all 0.2s;
            border-radius: 0.375rem;
            margin: 0 0.5rem;
            padding: 0.625rem 0.75rem;
        }

        .sidebar-link.active, .sidebar-link:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .sidebar-link.active {
            font-weight: 600;
        }

        /* Hacer que el botón de toggle gire cuando cambia el estado */
        .sidebar-toggle-btn .toggle-icon {
            transition: transform 0.3s;
        }

        body.sidebar-closed .sidebar-toggle-btn .toggle-icon {
            transform: rotate(180deg);
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
            <div id="graficasView" style="display:none;">
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
            // Si no se puede detectar automáticamente, mantener el valor por defecto en CSS
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
        
        // Por defecto mostrar dashboard
        showDashboard();

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
        
        // Inicialización
        handleResize();
    </script>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>