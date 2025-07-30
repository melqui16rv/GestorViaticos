<?php
session_start();
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

// Variables para el usuario (solo si está logueado)
$isLoggedIn = isset($_SESSION['id_rol']);
$rol = null;
$nombreUsuario = null;
$userData = null;

if ($isLoggedIn) {
    $rol = $_SESSION['id_rol'];
    // Obtener información del usuario
    $userClass = new user();
    $userData = $userClass->obtenerDatosUsuarioLogueado($_SESSION['numero_documento']);
    $nombreUsuario = ($userData && is_array($userData) && count($userData) > 0) ? $userData[0]['nombre_completo'] : 'Usuario';
}

// Definir información de roles
$rolesInfo = [
    '1' => ['nombre' => 'Administrador', 'color' => '#dc2626', 'icon' => 'fa-user-shield'],
    '2' => ['nombre' => 'Gestor', 'color' => '#059669', 'icon' => 'fa-tasks'],
    '3' => ['nombre' => 'Presupuesto', 'color' => '#d97706', 'icon' => 'fa-calculator'],
    '4' => ['nombre' => 'SENNOVA General', 'color' => '#7c3aed', 'icon' => 'fa-flask'],
    '5' => ['nombre' => 'Tecnoparque', 'color' => '#0891b2', 'icon' => 'fa-cogs'],
    '6' => ['nombre' => 'Tecnoacademia', 'color' => '#be185d', 'icon' => 'fa-graduation-cap'],
    '7' => ['nombre' => 'Acceso', 'color' => '#374151', 'icon' => 'fa-key'],
    '9' => ['nombre' => 'Gestor Diseño Curricular', 'color' => '#be185d', 'icon' => 'fa-graduation-cap']
];

$rolActual = $isLoggedIn ? ($rolesInfo[$rol] ?? ['nombre' => 'Usuario', 'color' => '#6b7280', 'icon' => 'fa-user']) : null;

// Obtener estadísticas rápidas del sistema (solo si es administrador y está logueado)
$stats = [];
if ($isLoggedIn && ($rol == '1' || $rol == '7')) {
    try {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/metodos_solicitud.php';
        $solicitudClass = new solicitudRol();
        $resumen = $solicitudClass->obtenerResumenSolicitudes();
        foreach ($resumen as $stat) {
            $stats[$stat['estado']] = $stat['total'];
        }
    } catch (Exception $e) {
        // Si hay error, continuar sin estadísticas
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>CIDE - Sistema Integral SENNOVA | Centro Industrial Desarrollo Empresarial</title>
    
    <!-- Meta tags para SEO -->
    <meta name="description" content="Sistema Integral SENNOVA - Gestión de proyectos tecnológicos, presupuesto (CDP-CRP-OP), metas institucionales y programas de innovación del Centro Industrial Desarrollo Empresarial">
    <meta name="keywords" content="SENA, CIDE, SENNOVA, Tecnoparque, Tecnoacademia, Proyectos Tecnológicos, Presupuesto, CDP, CRP, OP, Innovación">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    
    <!-- CSS Framework -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
      <!-- Custom CSS -->
    <style>
        * { font-family: 'Inter', sans-serif; }        .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #5b21b6 100%);
            position: relative;
        }
        
        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.08) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }
        
        /* Add an overlay for better text contrast */
        .gradient-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.15);
            pointer-events: none;
        }
        
        /* Enhanced text shadows for better readability */
        .hero-text-shadow {
            text-shadow: 
                2px 2px 4px rgba(0,0,0,0.5),
                0 0 20px rgba(255,255,255,0.3),
                0 0 40px rgba(255,255,255,0.1);
        }
        
        .hero-subtitle-shadow {
            text-shadow: 
                1px 1px 3px rgba(0,0,0,0.4),
                0 0 15px rgba(255,255,255,0.2);
        }
        
        .badge-shadow {
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            box-shadow: 
                0 4px 15px rgba(0,0,0,0.2),
                inset 0 1px 0 rgba(255,255,255,0.2);
        }
        
        /* Improved gradient backgrounds for badges */
        .badge-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .badge-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .badge-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        
        .badge-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .badge-security {
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .hero-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.02) 50%, transparent 70%);
            background-size: 50px 50px, 30px 30px, 100% 100%;
            animation: patternMove 20s linear infinite;
        }
        
        @keyframes patternMove {
            0% { background-position: 0 0, 0 0, 0 0; }
            100% { background-position: 50px 50px, 30px 30px, 100px 100px; }
        }
        
        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .card-hover:hover::before {
            left: 100%;
        }
        
        .card-hover:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }
        
        .feature-icon {
            position: relative;
            overflow: hidden;
        }
        
        .feature-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            transition: transform 0.6s;
        }
        
        .card-hover:hover .feature-icon::before {
            transform: rotate(45deg) translate(50%, 50%);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite, glow 2s infinite alternate;
        }
        
        @keyframes glow {
            from { text-shadow: 0 0 5px rgba(255,255,255,0.5); }
            to { text-shadow: 0 0 20px rgba(255,255,255,0.8), 0 0 30px rgba(255,255,255,0.6); }
        }
        
        .role-badge {
            position: relative;
            overflow: hidden;
        }
        
        .role-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: badge-shine 3s infinite;
        }
        
        @keyframes badge-shine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
        
        .typing-animation {
            overflow: hidden;
            border-right: 3px solid rgba(255,255,255,0.7);
            white-space: nowrap;
            animation: typing 4s steps(40, end), blink-caret 0.75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent; }
            50% { border-color: rgba(255,255,255,0.7); }
        }
        
        .stats-counter {
            position: relative;
            overflow: hidden;
        }
        
        .stats-counter::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: counter-shine 2s infinite;
            animation-delay: var(--delay, 0s);
        }
        
        @keyframes counter-shine {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
          .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(209, 213, 219, 0.3);
        }
        
        /* New advanced animations */
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-delayed {
            animation: float 6s ease-in-out infinite;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(1deg); }
            66% { transform: translateY(10px) rotate(-1deg); }
        }
        
        .counter-animation {
            position: relative;
            overflow: hidden;
        }
        
        .counter-animation::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.2) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: counter-shine 3s infinite;
        }
        
        .animate-twinkle {
            animation: twinkle 2s ease-in-out infinite;
        }
        
        .animate-twinkle-delayed {
            animation: twinkle 2s ease-in-out infinite;
            animation-delay: 1s;
        }
        
        @keyframes twinkle {
            0%, 100% { 
                opacity: 0.3; 
                transform: scale(1); 
            }
            50% { 
                opacity: 1; 
                transform: scale(1.2); 
                box-shadow: 0 0 20px currentColor;
            }
        }
        
        /* Advanced hover effects */
        .group:hover .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        
        /* Backdrop blur for modern glassmorphism */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }
        
        /* Enhanced card effects */
        .transform {
            transition: transform 0.3s ease;
        }
        
        .hover\\:scale-105:hover {
            transform: scale(1.05);
        }
        
        .hover\\:scale-110:hover {
            transform: scale(1.1);
        }
        
        /* Custom gradient text */
        .bg-clip-text {
            background-clip: text;
            -webkit-background-clip: text;
        }
        
        .text-transparent {
            color: transparent;
        }
        
        /* Loading animation enhancement */
        .loading-overlay {
            backdrop-filter: blur(8px);
        }
        
        /* Smooth transitions for all interactive elements */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Enhanced shadow effects */
        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }
          /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
        }
        
        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Loading progress bar */
        .loading-progress {
            transition: width 0.3s ease;
        }
        
        /* Enhanced button animations */
        .btn-enhanced {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .btn-enhanced:active {
            transform: translateY(0);
        }
        
        /* Improved accessibility */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
        }
          /* High contrast mode support */
        @media (prefers-contrast: high) {
            .gradient-bg {
                background: #000;
                color: #fff;
            }
            
            .glass-effect {
                background: rgba(0, 0, 0, 0.9);
                border: 2px solid #fff;
            }
        }

        /* Modal styles (igual al de nav.php) */
        .modal-overlayyy {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            animation: fadeOut 0.3s forwards;
        }

        .modal-overlayyy.active {
            display: flex;
            animation: fadeIn 0.4s forwards;
        }

        .modal-containerrr {
            background: #ffffff;
            padding: 30px;
            width: 360px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: scale(0.95);
            animation: scaleIn 0.4s forwards;
            border: 1px solid #ecf0f1;
        }

        .modal-iconnn {
            font-size: 50px;
            color: #e74c3c;
            margin-bottom: 20px;
            animation: pulse 1.5s infinite;
        }

        .modal-contentttt h2 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .modal-contentttt p {
            font-size: 16px;
            color: #576574;
            margin: 15px 0;
            line-height: 1.5;
        }

        .modal-buttonsss {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn-cancelll,
        .btn-confirmmm {
            border: none;
            padding: 12px 0;
            width: 47%;
            font-size: 15px;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            outline: none;
        }

        .btn-cancelll {
            background: #ecf0f1;
            color: #576574;
            border: 1px solid #bdc3c7;
        }

        .btn-confirmmm {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
        }

        .btn-cancelll:hover {
            background: #bdc3c7;
            color: #2c3e50;
            transform: translateY(-2px);
        }

        .btn-confirmmm:hover {
            background: linear-gradient(to right, #c0392b, #e74c3c);
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.4);
            transform: translateY(-2px);
        }

        .btn-cancelll:active,
        .btn-confirmmm:active {
            transform: translateY(1px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
            }
            to {
                transform: scale(1);
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" id="loadingOverlay">
        <div class="text-white text-xl">
            <i class="fas fa-spinner fa-spin mr-3"></i>Cargando...
        </div>
    </div>    <!-- Logout Modal (igual al de nav.php) -->
    <div id="logoutModal" class="modal-overlayyy">
        <div class="modal-containerrr">
            <div class="modal-contentttt">
                <div class="modal-iconnn">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h2>Cerrar Sesión</h2>
                <p>¿Está seguro que desea cerrar la sesión?</p>
                <div class="modal-buttonsss">
                    <button id="cancelBtn" class="btn-cancelll">Cancelar</button>
                    <button id="confirmBtn" class="btn-confirmmm">Confirmar</button>
                </div>
            </div>
        </div>
    </div><!-- Hero Section -->
    <section class="min-h-screen bg-white relative overflow-hidden">
        <!-- Subtle decorative elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-br from-blue-50 to-transparent rounded-full opacity-50"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-gradient-to-br from-green-50 to-transparent rounded-full opacity-50"></div>

        <div class="container mx-auto px-6 relative z-10 min-h-screen flex items-center">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center w-full">
                <!-- Left Content -->
                <div data-aos="fade-right" data-aos-duration="1000">
                    <!-- Hero Header -->
                    <div class="text-center lg:text-left">
                        <div class="flex items-center justify-center lg:justify-start mb-8">                            <div class="logo-container mr-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-flask text-2xl text-white"></i>
                                </div>
                            </div><div>
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-2">CIDE</h1>
                                <!-- <p class="text-lg text-gray-600 font-medium">Sistema Integral</p> -->
                            </div>
                        </div>
                        
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-6 leading-tight">
                            Centro Industrial Desarrollo Empresarial
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-blue-600 font-extrabold">
                                Plataforma Integral
                            </span>
                        </h2>
                          <p class="text-xl text-gray-600 mb-10 leading-relaxed max-w-lg">
                            Sistema integral de gestión administrativa con módulos especializados: Administrador, Presupuesto (CDP-CRP-OP), 
                            SENNOVA (Tecnoparque y Tecnoacademia) y Gestor de Cuenta, con análisis en tiempo real.
                        </p><!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                            <a href="#features" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <span class="mr-2">Explorar Características</span>
                                <i class="fas fa-arrow-down group-hover:translate-y-1 transition-transform duration-300"></i>
                            </a>
                            
                            <?php if ($isLoggedIn): ?>
                                <!-- Botón cerrar sesión cuando está logueado -->
                                <button onclick="showLogoutModal()" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-xl hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <span class="mr-2">Cerrar Sesión</span>
                                    <i class="fas fa-sign-out-alt group-hover:translate-x-1 transition-transform duration-300"></i>
                                </button>
                            <?php else: ?>
                                <!-- Botón iniciar sesión cuando NO está logueado -->
                                <a href="<?php echo BASE_URL; ?>includes/session/login.php" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-gray-700 border-2 border-gray-300 rounded-xl hover:bg-gray-50 transform hover:scale-105 transition-all duration-300">
                                    <span class="mr-2">Iniciar Sesión</span>
                                    <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform duration-300"></i>
                                </a>
                            <?php endif; ?>
                        </div>                          <!-- Sistema Capabilities -->
                        <div class="grid grid-cols-2 gap-8 max-w-sm mx-auto lg:mx-0">
                            <div class="text-center lg:text-left">
                                <div class="text-3xl font-bold text-blue-600 mb-1 counter-animation" id="counter1" data-count="4">0</div>
                                <div class="text-sm text-gray-600">Módulos Sistema</div>
                            </div>
                            <div class="text-center lg:text-left">
                                <div class="text-3xl font-bold text-purple-600 mb-1 counter-animation" id="counter2" data-count="2">0</div>
                                <div class="text-sm text-gray-600">Áreas SENNOVA</div>
                            </div>
                        </div>
                        
                        <!-- Additional System Features -->
                        <div class="grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0 mt-6">
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-bold text-green-600 mb-1">CDP</div>
                                <div class="text-xs text-gray-500">Control Presupuestal</div>
                            </div>
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-bold text-orange-600 mb-1">CRP</div>
                                <div class="text-xs text-gray-500">Registro Presupuestal</div>
                            </div>
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-bold text-red-600 mb-1">OP</div>
                                <div class="text-xs text-gray-500">Órdenes de Pago</div>
                            </div>
                        </div>
                    </div>
                </div>                <!-- Right Content - User Welcome Card -->
                <div data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
                    <?php if ($isLoggedIn): ?>
                        <!-- Tarjeta para usuarios logueados -->
                        <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 relative overflow-hidden">
                            <!-- Decorative background -->
                            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                            
                            <div class="text-center relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                    <i class="fas <?php echo $rolActual['icon']; ?> text-3xl text-white"></i>
                                </div>
                                
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Hola, <?php echo htmlspecialchars($nombreUsuario); ?>!</h3>
                                <p class="text-gray-600 mb-6">Rol: <?php echo htmlspecialchars($rolActual['nombre']); ?></p>
                                
                                <!-- User Actions -->
                                <div class="space-y-3">
                                    <?php
                                    $enlaces = [];
                                    
                                    switch($rol) {
                                        case '1': // Admin
                                            $enlaces = [
                                                ['url' => 'app/admin/index.php', 'text' => 'Panel Admin', 'icon' => 'fa-tachometer-alt', 'color' => 'from-red-500 to-red-600'],
                                                ['url' => 'app/admin/solicitudes/index.php', 'text' => 'Gestionar Solicitudes', 'icon' => 'fa-users-cog', 'color' => 'from-purple-500 to-purple-600']
                                            ];
                                            break;
                                        case '2': // Gestor
                                            $enlaces = [
                                                ['url' => 'app/gestor/index.php', 'text' => 'Panel Gestor', 'icon' => 'fa-tasks', 'color' => 'from-green-500 to-green-600']
                                            ];
                                            break;
                                        case '3': // Presupuesto
                                            $enlaces = [
                                                ['url' => 'app/presupuesto/index.php', 'text' => 'Gestión Presupuestal', 'icon' => 'fa-calculator', 'color' => 'from-orange-500 to-orange-600'],
                                                ['url' => 'app/presupuesto/historialOP.php', 'text' => 'Historial OP', 'icon' => 'fa-history', 'color' => 'from-blue-500 to-blue-600']
                                            ];
                                            break;
                                        case '4': // SENNOVA General
                                        case '5': // Tecnoparque
                                        case '6': // Tecnoacademia
                                            $enlaces = [
                                                ['url' => 'app/SENNOVA/General/index.php', 'text' => 'Dashboard SENNOVA', 'icon' => 'fa-flask', 'color' => 'from-purple-500 to-purple-600']
                                            ];
                                            if ($rol == '5') {
                                                $enlaces[] = ['url' => 'app/SENNOVA/Tecnoparque/metas/index.php', 'text' => 'Metas Tecnoparque', 'icon' => 'fa-target', 'color' => 'from-cyan-500 to-cyan-600'];
                                            }
                                            break;
                                        case '7': // Acceso
                                            $enlaces = [
                                                ['url' => 'app/acceso/index.php', 'text' => 'Portal Acceso', 'icon' => 'fa-key', 'color' => 'from-gray-500 to-gray-600'],
                                                ['url' => 'app/acceso/solicitud.php', 'text' => 'Solicitar Rol', 'icon' => 'fa-user-plus', 'color' => 'from-indigo-500 to-indigo-600']
                                            ];
                                            break;
                                        case '9': // Acceso
                                            $enlaces = [
                                                ['url' => 'https://appscide.com/disenoCurricular/app/forms/index.php?accion=listar', 'text' => 'Gestionar Diseños', 'icon' => 'fa-user-plus', 'color' => 'from-indigo-500 to-indigo-600']
                                            ];
                                            break;
                                    }
                                    
                                    foreach($enlaces as $enlace): ?>
                                        <a href="<?php echo $enlace['url']; ?>" 
                                           class="block w-full bg-gradient-to-r <?php echo $enlace['color']; ?> text-white py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            <i class="fas <?php echo $enlace['icon']; ?> mr-3"></i>
                                            <?php echo $enlace['text']; ?>
                                        </a>
                                    <?php endforeach; ?>
                                    
                                    <!-- Nota: El botón de "Cerrar Sesión" se eliminó de aquí. Ahora solo está en la sección de Action Buttons con modal de confirmación -->
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Tarjeta para usuarios NO logueados -->
                        <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 relative overflow-hidden">
                            <!-- Decorative background -->
                            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                            
                            <div class="text-center relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                    <i class="fas fa-user-plus text-3xl text-white"></i>
                                </div>
                                
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Bienvenido al CIDE!</h3>
                                <p class="text-gray-600 mb-6">Accede al Sistema Integral SENNOVA</p>
                                
                                <!-- Información para visitantes -->
                                <div class="space-y-4">
                                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-800 mb-2">Funcionalidades Disponibles:</h4>
                                        <ul class="text-sm text-gray-600 space-y-1 text-left">
                                            <li>• Gestión de proyectos tecnológicos</li>
                                            <li>• Control presupuestal (CDP-CRP-OP)</li>
                                            <li>• Seguimiento de metas SENNOVA</li>
                                            <li>• Asesoramiento institucional</li>
                                        </ul>
                                    </div>
                                    
                                    <a href="<?php echo BASE_URL; ?>includes/session/login.php" 
                                       class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        <i class="fas fa-sign-in-alt mr-3"></i>
                                        Iniciar Sesión
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-6">            <div class="text-center mb-16">                <h2 class="text-4xl font-bold text-gray-800 mb-4" data-aos="fade-up">Módulos del Sistema Integral</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Plataforma modular para gestión administrativa, presupuestal, SENNOVA y control de cuentas
                </p>
            </div><div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center group" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                          <div class="feature-icon w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition-transform duration-300 relative">
                            <i class="fas fa-project-diagram text-2xl text-white"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full opacity-0 group-hover:opacity-100 animate-ping"></div>
                        </div>                        <h3 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-green-600 transition-colors duration-300">Módulo Administrador</h3>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors duration-300">Control total del sistema con gestión de usuarios, roles, permisos y configuraciones avanzadas.</p>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-blue-100 opacity-0 group-hover:opacity-30 transition-opacity duration-500 rounded-2xl"></div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="text-center group" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                        
                        <div class="feature-icon w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition-transform duration-300 relative">
                            <i class="fas fa-chart-line text-2xl text-white"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-500 rounded-full opacity-0 group-hover:opacity-100 animate-ping"></div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-green-600 transition-colors duration-300">Analytics Inteligente</h3>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors duration-300">Dashboards interactivos con métricas en tiempo real y reportes detallados.</p>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-green-50 to-green-100 opacity-0 group-hover:opacity-30 transition-opacity duration-500 rounded-2xl"></div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="text-center group" data-aos="fade-up" data-aos-delay="500">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                          <div class="feature-icon w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition-transform duration-300 relative">
                            <i class="fas fa-graduation-cap text-2xl text-white"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-purple-500 rounded-full opacity-0 group-hover:opacity-100 animate-ping"></div>
                        </div>                        <h3 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-purple-600 transition-colors duration-300">Módulo Tecnoacademia</h3>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors duration-300">Gestión integral de actividades formativas y vinculación con el sector productivo.</p>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-50 to-purple-100 opacity-0 group-hover:opacity-30 transition-opacity duration-500 rounded-2xl"></div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="text-center group" data-aos="fade-up" data-aos-delay="600">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-orange-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                          <div class="feature-icon w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition-transform duration-300 relative">
                            <i class="fas fa-calculator text-2xl text-white"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-orange-500 rounded-full opacity-0 group-hover:opacity-100 animate-ping"></div>
                        </div>                        <h3 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-orange-600 transition-colors duration-300">Gestión Presupuestal</h3>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors duration-300">Control integral del flujo CDP → CRP → OP con dashboards en tiempo real.</p>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-orange-100 opacity-0 group-hover:opacity-30 transition-opacity duration-500 rounded-2xl"></div>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="text-center group" data-aos="fade-up" data-aos-delay="700">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                          <div class="feature-icon w-16 h-16 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition-transform duration-300 relative">
                            <i class="fas fa-chart-bar text-2xl text-white"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-400 to-red-500 rounded-full opacity-0 group-hover:opacity-100 animate-ping"></div>
                        </div>                        <h3 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-red-600 transition-colors duration-300">Reportes Avanzados</h3>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors duration-300">Generación automática de informes SENNOVA con gráficas y métricas institucionales.</p>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-red-50 to-red-100 opacity-0 group-hover:opacity-30 transition-opacity duration-500 rounded-2xl"></div>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="text-center group" data-aos="fade-up" data-aos-delay="800">
                    <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 transform hover:scale-105 transition-all duration-500 hover:shadow-2xl relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-indigo-100 to-transparent rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                          <div class="feature-icon w-16 h-16 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition-transform duration-300 relative">
                            <i class="fas fa-user-shield text-2xl text-white"></i>
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-indigo-500 rounded-full opacity-0 group-hover:opacity-100 animate-ping"></div>
                        </div>                        <h3 class="text-xl font-bold text-gray-800 mb-4 group-hover:text-indigo-600 transition-colors duration-300">Control de Acceso</h3>
                        <p class="text-gray-600 group-hover:text-gray-700 transition-colors duration-300">Sistema de roles jerárquicos con solicitudes y aprobaciones automáticas.</p>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-indigo-100 opacity-0 group-hover:opacity-30 transition-opacity duration-500 rounded-2xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>    <!-- Stats Section -->
    <section class="py-20 gradient-bg relative overflow-hidden">
        <!-- Animated background patterns -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full animate-float"></div>
            <div class="absolute top-32 right-20 w-24 h-24 bg-white rounded-full animate-float-delayed"></div>
            <div class="absolute bottom-20 left-1/4 w-20 h-20 bg-white rounded-full animate-float"></div>
            <div class="absolute bottom-40 right-1/3 w-28 h-28 bg-white rounded-full animate-float-delayed"></div>
        </div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center text-white">
                <h2 class="text-4xl font-bold mb-4" data-aos="fade-up">Sistema en Números</h2>
                <p class="text-xl opacity-90 mb-16" data-aos="fade-up" data-aos-delay="200">
                    Cifras que demuestran la eficiencia de nuestro sistema
                </p>
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-8">                    <!-- Stat 1 -->
                    <div class="text-center group" data-aos="fade-up" data-aos-delay="200">
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 transform hover:scale-105 transition-all duration-500 hover:bg-opacity-20 relative overflow-hidden">
                            <!-- Shimmer effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                              <div class="relative z-10">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-th-large text-2xl text-white"></i>
                                </div>
                                <div class="text-5xl font-bold mb-2 counter-animation" data-count="4">0</div>
                                <p class="text-xl opacity-90">Módulos Sistema</p>
                                <p class="text-sm opacity-70 mt-2">Admin, Presupuesto, SENNOVA, Gestor</p>
                            </div>
                        </div>
                    </div>
                      <!-- Stat 2 -->
                    <div class="text-center group" data-aos="fade-up" data-aos-delay="300">
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 transform hover:scale-105 transition-all duration-500 hover:bg-opacity-20 relative overflow-hidden">
                            <!-- Shimmer effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                              <div class="relative z-10">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-flask text-2xl text-white"></i>
                                </div>
                                <div class="text-5xl font-bold mb-2 counter-animation" data-count="2">0</div>
                                <p class="text-xl opacity-90">Áreas SENNOVA</p>
                                <p class="text-sm opacity-70 mt-2">Tecnoparque + Tecnoacademia</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stat 3 -->
                    <div class="text-center group" data-aos="fade-up" data-aos-delay="400">
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 transform hover:scale-105 transition-all duration-500 hover:bg-opacity-20 relative overflow-hidden">
                            <!-- Shimmer effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                              <div class="relative z-10">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-users-cog text-2xl text-white"></i>
                                </div>
                                <div class="text-5xl font-bold mb-2 counter-animation" data-count="7">0</div>
                                <p class="text-xl opacity-90">Roles Sistema</p>
                                <p class="text-sm opacity-70 mt-2">Acceso multinivel</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stat 4 -->
                    <div class="text-center group" data-aos="fade-up" data-aos-delay="500">
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 transform hover:scale-105 transition-all duration-500 hover:bg-opacity-20 relative overflow-hidden">
                            <!-- Shimmer effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                              <div class="relative z-10">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-chart-line text-2xl text-white"></i>
                                </div>
                                <div class="text-5xl font-bold mb-2">100<span class="text-3xl">%</span></div>
                                <p class="text-xl opacity-90">Tiempo Real</p>
                                <p class="text-sm opacity-70 mt-2">Analytics en vivo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16 relative overflow-hidden">
        <!-- Decorative background -->
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900"></div>
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <!-- Logo with glow effect -->
                <div class="relative inline-block mb-6" data-aos="fade-up">
                    <div class="absolute inset-0 bg-blue-500 rounded-full blur-lg opacity-30 animate-pulse"></div>
                    <img src="<?php echo BASE_URL; ?>assets/img/public/logosena.png" alt="SENA Logo" 
                         class="w-20 h-20 mx-auto relative z-10 transform hover:scale-110 transition-transform duration-300">
                </div>
                  <!-- Main footer content -->
                <div data-aos="fade-up" data-aos-delay="200">                    <h3 class="text-2xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Plataforma SENNOVA - Sistema CIDE
                    </h3>
                    <p class="text-gray-400 mb-2 text-lg">
                        Centro Industrial y del Desarrollo Empresarial | Sistema de Investigación, Desarrollo Tecnológico e Innovación
                    </p>
                    <p class="text-gray-500 text-sm mb-6">
                        © <?php echo date('Y'); ?> | Versión 3.0 | Tecnoparque • Tecnoacademia • Gestión Presupuestal • Analytics
                    </p>
                </div>
                  <!-- Features badges -->
                <div class="flex flex-wrap justify-center gap-4 mb-8" data-aos="fade-up" data-aos-delay="400">
                    <span class="bg-gray-800 text-gray-300 px-4 py-2 rounded-full text-sm border border-gray-700 hover:border-blue-500 transition-colors duration-300">
                        <i class="fas fa-flask mr-2 text-blue-400"></i>Módulos Tecnoparque
                    </span>
                    <span class="bg-gray-800 text-gray-300 px-4 py-2 rounded-full text-sm border border-gray-700 hover:border-purple-500 transition-colors duration-300">
                        <i class="fas fa-graduation-cap mr-2 text-purple-400"></i>Tecnoacademia
                    </span>
                    <span class="bg-gray-800 text-gray-300 px-4 py-2 rounded-full text-sm border border-gray-700 hover:border-green-500 transition-colors duration-300">
                        <i class="fas fa-calculator mr-2 text-green-400"></i>CDP • CRP • OP
                    </span>
                    <span class="bg-gray-800 text-gray-300 px-4 py-2 rounded-full text-sm border border-gray-700 hover:border-orange-500 transition-colors duration-300">
                        <i class="fas fa-chart-line mr-2 text-orange-400"></i>Analytics Real-Time
                    </span>
                </div>                  <!-- Social links or additional info -->                <div class="border-t border-gray-800 pt-6" data-aos="fade-up" data-aos-delay="600">
                    <p class="text-gray-500 text-xs leading-relaxed">
                        Sistema integral que gestiona <strong class="text-blue-400">100 proyectos de base tecnológica</strong>, <strong class="text-purple-400">5 proyectos de extensionismo</strong> 
                        y seguimiento presupuestal CDP→CRP→OP con <strong class="text-green-400">asesoramiento institucional</strong> en tiempo real.<br>
                        Arquitectura: <span class="text-blue-400">PHP 8+</span> • <span class="text-purple-400">TailwindCSS 3</span> • <span class="text-green-400">Chart.js</span> • 
                        <span class="text-orange-400">mPDF</span> • <span class="text-red-400">MySQL 8</span> • <span class="text-indigo-400">JavaScript ES6</span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Animated particles -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-20 left-10 w-2 h-2 bg-blue-400 rounded-full animate-twinkle"></div>
            <div class="absolute top-40 right-20 w-1 h-1 bg-purple-400 rounded-full animate-twinkle-delayed"></div>
            <div class="absolute bottom-20 left-1/4 w-1.5 h-1.5 bg-green-400 rounded-full animate-twinkle"></div>
            <div class="absolute bottom-40 right-1/3 w-2 h-2 bg-yellow-400 rounded-full animate-twinkle-delayed"></div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Loading overlay
        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            // Hide loading overlay after page load
            window.addEventListener('load', function() {
                loadingOverlay.classList.add('hidden');
            });
        });        // Modal functionality (igual al de nav.php)
        const logoutModal = document.getElementById('logoutModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');

        // Función para mostrar el modal
        function showLogoutModal() {
            logoutModal.classList.add('active');
        }

        // Función para ocultar el modal
        function hideLogoutModal() {
            logoutModal.classList.remove('active');
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                hideLogoutModal();
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                hideLogoutModal();
                window.location.href = '<?php echo BASE_URL; ?>includes/session/salir.php';
            });
        }

        // Cerrar modal al hacer clic fuera de él
        logoutModal.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                hideLogoutModal();
            }
        });

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });        // Add loading effect to links
        document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript:"])').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('loadingOverlay').classList.remove('hidden');
            });
        });

        // Animated counters
        function animateCounters() {
            const counters = document.querySelectorAll('.counter-animation');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // 2 seconds
                const start = performance.now();
                
                function updateCounter(currentTime) {
                    const elapsed = currentTime - start;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Easing function for smooth animation
                    const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                    const current = Math.round(target * easeOutQuart);
                    
                    counter.textContent = current;
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                
                requestAnimationFrame(updateCounter);
            });
        }        // Trigger counter animation when stats section is visible
        const counterElements = document.querySelectorAll('.counter-animation');
        if (counterElements.length > 0) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        // Solo observar el primer elemento para evitar múltiples ejecuciones
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.5 });
            
            // Observar solo el primer contador para activar la animación de todos
            observer.observe(counterElements[0]);
        }

        // Add parallax effect to hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            const heroSection = document.querySelector('.hero-pattern');
            if (heroSection) {
                heroSection.style.transform = `translateY(${rate}px)`;
            }
        });

        // Add dynamic typing effect to hero title
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.innerHTML = '';
            
            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                } else {
                    // Remove cursor after typing is complete
                    setTimeout(() => {
                        element.classList.remove('typing-animation');
                    }, 1000);
                }
            }
            type();
        }

        // Initialize typing animation after page load
        window.addEventListener('load', () => {
            const typingElement = document.querySelector('.typing-animation');
            if (typingElement) {
                const originalText = typingElement.textContent;
                setTimeout(() => {
                    typeWriter(typingElement, originalText, 80);
                }, 500);
            }
        });

        // Add interactive hover effects for feature cards
        document.querySelectorAll('.group').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add click ripple effect
        function createRipple(event) {
            const button = event.currentTarget;
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Apply ripple effect to CTA buttons
        document.querySelectorAll('a[class*="bg-gradient"]').forEach(button => {
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.addEventListener('click', createRipple);
        });

        // Enhanced loading animation
        function showLoadingWithProgress() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 border-4 border-white border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <div class="text-white text-xl mb-2">Cargando...</div>
                    <div class="w-64 bg-gray-200 rounded-full h-2 mx-auto">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full loading-progress" style="width: 0%"></div>
                    </div>
                </div>
            `;
            overlay.classList.remove('hidden');
            
            // Simulate loading progress
            const progressBar = overlay.querySelector('.loading-progress');
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 20;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                }
                progressBar.style.width = progress + '%';
            }, 200);
        }
    </script>

</body>
</html>
