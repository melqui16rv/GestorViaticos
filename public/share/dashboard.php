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
    <link rel="stylesheet" href="/assets/css/share/dashboard.css">
    <link rel="stylesheet" href="/assets/css/share/grafica.css">
    <style>
        body { background: #f3f4f6; }
        .sidebar-link.active, .sidebar-link:hover {
            background: #e0e7ef;
            color: #2563eb;
        }
        .sidebar-link {
            transition: background 0.2s, color 0.2s;
        }
    </style>
</head>
<body >
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    <!-- Sidebar tipo Filament -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-gray-200">
            <span class="text-xl font-bold text-blue-700">Gestor Viáticos</span>
        </div>
        <nav class="flex-1 py-4">
            <ul>
                <li>
                    <a href="#" id="navDashboard" class="sidebar-link flex items-center px-6 py-3 text-gray-700 font-medium cursor-pointer">
                        <i class="ti ti-layout-dashboard mr-3"></i> Dashboard
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
    <main class="flex-1 bg-gray-50 min-h-screen">
        <div id="dashboardView">
            <?php require __DIR__ . '/dashboard_content.php'; ?>
        </div>
        <div id="graficasView" style="display:none;">
            <?php require __DIR__ . '/Graficas.php'; ?>
        </div>
    </main>
    <script>
        // Sidebar navegación
        const dashboardView = document.getElementById('dashboardView');
        const graficasView = document.getElementById('graficasView');
        const navDashboard = document.getElementById('navDashboard');
        const navGraficas = document.getElementById('navGraficas');
        
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
        </script>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>
