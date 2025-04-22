<?php
session_start();
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

if (isset($_SESSION['id_rol'])) {
    $rol = $_SESSION['id_rol'];
} else {
    header("Location: " . "includes/session/login.php");
    exit;
}

$miClase = new user();
$estadisticas = $miClase->obtenerEstadisticasActualizaciones();
$estadisticasUsuarios = $miClase->obtenerEstadisticasPorUsuario();
$totalesRegistros = $miClase->obtenerTotalRegistros();
$actualizaciones = $miClase->obtenerUltimasActualizaciones(); // Cambiado para usar el método de la clase

// Procesar fechas del filtro
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'));
$estadisticasPorFecha = $miClase->obtenerEstadisticasPorFecha($fecha_inicio, $fecha_fin);

// Preparar datos para los gráficos
$datosGraficoBarras = [
    'labels' => array_column($estadisticas, 'tipo_tabla'),
    'actualizados' => array_column($estadisticas, 'total_registros_actualizados'),
    'nuevos' => array_column($estadisticas, 'total_registros_nuevos')
];

$datosGraficoLineas = [];
foreach ($estadisticasPorFecha as $estadistica) {
    $datosGraficoLineas[$estadistica['fecha']][$estadistica['tipo_tabla']] = [
        'actualizados' => $estadistica['actualizados'],
        'nuevos' => $estadistica['nuevos']
    ];
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <!-- Puedes agregar aquí tus propios estilos si lo deseas -->
    <link rel="stylesheet" href="/assets/css/share/dashboard.css">
</head>
<body class="flex min-h-screen">
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
        <!-- Dashboard -->
        <div id="dashboardView">
            <?php require __DIR__ . '/dashboard_content.php'; ?>
        </div>
        <!-- Gráficas -->
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
</body>
</html>
