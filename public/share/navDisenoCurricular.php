<?php
// Iniciar buffer de salida ANTES de cualquier include
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';


if (isset($_GET['num_doc'])) {
    $numero_documento = $_GET['num_doc'];
    $result = new user();
    $datos_instructor = $result->obtenerDatosEstructuradosPorNumeroDocumento($numero_documento);
    if (empty($datos_instructor)) {
        $datos_instructor = null;
    }
} else {
    $datos_instructor = null;
}

if (isset($_SESSION['numero_documento'])) {
    $id = $_SESSION['numero_documento'];
    $usuario_result = new user();
    $datos_usuario = $usuario_result->obtenerDatosUsuarioLogueado($id);

    if (!empty($datos_usuario)) {
        $nombreUser = $datos_usuario[0]['nombre_completo'];
    } else {
        $nombreUser = 'Nombre no disponible';
    }
} else {
    $nombreUser = null;
}
?>
<html lang="es" style="--nav-height: 70px;scrollbar-width: none; /* Oculta la barra en Firefox */-ms-overflow-style: none;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/nav.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>Viaticos</title>
    <style>
        /* ===============================================
   NAVBAR RESPONSIVE CON TRANSICIONES SUAVES
   =============================================== */

/* Variables CSS para colores y espaciado */
:root {
    --primary-color: #2c3e50;
    --primary-dark: #1a2530;
    --primary-light: #3d5167;
    --secondary-color: #3498db;
    --secondary-dark: #2980b9;
    --secondary-light: #5faee3;
    --accent-color: #1abc9c;
    --accent-dark: #16a085;
    --accent-light: #48c9b0;
    --success-color: #2ecc71;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --light-color: #ffffff;
    --light-gray: #f5f7fa;
    --gray-lighter: #ecf0f1;
    --gray-light: #bdc3c7;
    --gray-medium: #95a5a6;
    --gray-dark: #7f8c8d;
    --text-primary: #2c3e50;
    --text-secondary: #576574;
    --text-muted: #7f8c8d;
    
    /* Sombras */
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.12);
    --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
    
    /* Transiciones suaves */
    --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Bordes redondeados */
    --border-radius-sm: 4px;
    --border-radius-md: 6px;
    --border-radius-lg: 10px;
    
    /* Altura del navbar adaptable */
    --nav-height-desktop: 70px;
    --nav-height-tablet: 65px;
    --nav-height-mobile: 60px;
    --nav-height-small: 58px;
    --nav-height-tiny: 56px;
}

/* Reset básico */
* {
    box-sizing: border-box;
}

/* ===============================================
   ESTRUCTURA BASE DEL NAVBAR
   =============================================== */

.navbar {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    box-shadow: var(--shadow-lg);
    position: sticky;
    top: 0;
    z-index: 1000;
    
    /* Transiciones suaves para todas las propiedades */
    transition: 
        height var(--transition-normal),
        padding var(--transition-normal),
        gap var(--transition-normal),
        box-shadow var(--transition-fast);
    
    /* Layout por defecto (desktop) */
    height: var(--nav-height-desktop);
    padding: 0 30px;
    gap: 25px;
}

.navbar:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

/* Contenedores principales */
.navbar-left {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    order: 1;
    transition: max-width var(--transition-normal), flex var(--transition-normal);
}

.navbar-center {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    order: 2;
    transition: opacity var(--transition-normal);
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-shrink: 0;
    order: 3;
    transition: gap var(--transition-normal);
}

/* ===============================================
   INFORMACIÓN DEL USUARIO (IZQUIERDA)
   =============================================== */

.navbar-left h2 {
    position: relative;
    color: var(--light-color);
    background-color: rgba(255, 255, 255, 0.1);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    white-space: nowrap;
    
    /* Propiedades que cambiarán suavemente */
    transition: 
        font-size var(--transition-normal),
        padding var(--transition-normal),
        margin var(--transition-normal);
    
    /* Valores por defecto (desktop) */
    font-size: 16px;
    font-weight: 500;
    padding: 8px 15px 8px 25px;
    margin: 0;
}

/* Indicador de estado conectado */
.navbar-left h2:before {
    content: '';
    position: absolute;
    border-radius: 50%;
    background-color: var(--success-color);
    box-shadow: 0 0 6px var(--success-color);
    
    /* Transición suave del tamaño del indicador */
    transition: 
        width var(--transition-normal),
        height var(--transition-normal),
        left var(--transition-normal);
    
    /* Valores por defecto */
    width: 8px;
    height: 8px;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
}

/* Contenedor del rol y nombre */
.indicadorRol {
    display: flex;
    align-items: center;
    text-transform: capitalize;
    letter-spacing: 0.5px;
    gap: 0;
}

/* Textos del rol y nombre */
.rol-text, 
.nombre-text {
    transition: 
        font-size var(--transition-normal),
        opacity var(--transition-normal),
        display var(--transition-normal);
}

.rol-text {
    flex-shrink: 0;
    opacity: 1;
}

.nombre-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
    font-weight: 500;
}

/* Separador entre rol y nombre */
.rol-text::after {
    content: " - ";
    color: rgba(255, 255, 255, 0.7);
    margin: 0 4px 0 2px;
    transition: margin var(--transition-normal);
}

/* ===============================================
   MENÚ CENTRAL
   =============================================== */

.menu {
    display: flex;
    height: 100%;
}

.menu-principal {
    display: flex;
    margin: 0;
    padding: 0;
    height: 10%;
    list-style: none;
    transition: opacity var(--transition-normal);
}

.menu-principal li {
    display: flex;
    align-items: center;
    height: 100%;
    margin: 0;
    position: relative;
}

.menu-principal a {
    display: flex;
    align-items: center;
    height: 100%;
    text-decoration: none;
    color: var(--light-color);
    position: relative;
    letter-spacing: 0.5px;
    
    /* Transiciones suaves */
    transition: 
        font-size var(--transition-normal),
        padding var(--transition-normal),
        color var(--transition-fast),
        background-color var(--transition-fast);
    
    /* Valores por defecto */
    font-size: 16px;
    font-weight: 500;
    padding: 0 20px;
}

/* Efecto de subrayado animado */
.menu-principal a::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background-color: var(--accent-color);
    transform: translateX(-50%);
    transition: width var(--transition-normal);
    border-radius: 3px 3px 0 0;
}

.menu-principal a:hover {
    color: var(--accent-light);
    background-color: rgba(255, 255, 255, 0.08);
}

.menu-principal a:hover::before {
    width: 70%;
}

/* Estado activo */
.activeURL {
    color: var(--light-color) !important;
    background-color: rgba(26, 188, 156, 0.2) !important;
    font-weight: 600 !important;
}

.activeURL::before {
    width: 80% !important;
    background-color: var(--accent-light) !important;
    height: 4px !important;
    box-shadow: 0 0 10px var(--accent-color);
}

/* ===============================================
   ACCIONES (DERECHA)
   =============================================== */

.boton_ir {
    background: linear-gradient(to right, var(--accent-color), var(--accent-dark));
    color: var(--light-color);
    text-decoration: none;
    border-radius: var(--border-radius-md);
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    outline: none;
    
    /* Transiciones suaves */
    transition: 
        padding var(--transition-normal),
        font-size var(--transition-normal),
        transform var(--transition-fast),
        background var(--transition-fast),
        box-shadow var(--transition-fast);
    
    /* Valores por defecto */
    display: flex;
    align-items: center;
    padding: 10px 22px;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.boton_ir:hover {
    background: linear-gradient(to right, var(--accent-dark), var(--secondary-dark));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(22, 160, 133, 0.4);
}

.boton_ir:active {
    transform: translateY(1px);
    box-shadow: 0 2px 5px rgba(22, 160, 133, 0.4);
}

/* Icono de cerrar sesión */
.boton_ir::after {
    content: '↪';
    font-size: 18px;
    margin-left: 8px;
    transition: margin-left var(--transition-normal);
}

/* ===============================================
   BOTÓN MENÚ HAMBURGUESA
   =============================================== */

.menu-toggle {
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: transparent;
    border: none;
    cursor: pointer;
    border-radius: var(--border-radius-sm);
    outline: none;
    
    /* Transiciones suaves */
    transition: 
        width var(--transition-normal),
        height var(--transition-normal),
        background-color var(--transition-fast);
    
    /* Valores por defecto */
    width: 40px;
    height: 40px;
}

.menu-toggle:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.menu-toggle span {
    display: block;
    background: var(--light-color);
    border-radius: 3px;
    transform-origin: center;
    
    /* Transiciones suaves para la animación */
    transition: 
        width var(--transition-normal),
        height var(--transition-normal),
        margin var(--transition-normal),
        transform var(--transition-normal),
        opacity var(--transition-normal),
        background-color var(--transition-fast);
    
    /* Valores por defecto */
    height: 3px;
    width: 24px;
    margin: 2.5px 0;
}

.menu-toggle:hover span {
    background-color: var(--accent-light);
}

/* Animación del botón cuando está activo */
.menu-toggle.active span:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
    background-color: var(--accent-light);
}

.menu-toggle.active span:nth-child(2) {
    opacity: 0;
    transform: scale(0);
}

.menu-toggle.active span:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
    background-color: var(--accent-light);
}

/* ===============================================
   RESPONSIVE SYSTEM CON TRANSICIONES SUAVES
   =============================================== */

/* Desktop grande (1200px+) - Layout completo y espacioso */
@media screen and (min-width: 1200px) {
    .navbar {
        height: var(--nav-height-desktop);
        padding: 0 30px;
        gap: 25px;
    }
    
    .navbar-left h2 {
        font-size: 16px;
        padding: 8px 15px 8px 25px;
    }
    
    .menu-principal a {
        font-size: 16px;
        padding: 0 20px;
    }
    
    .boton_ir {
        padding: 10px 22px;
        font-size: 15px;
    }
}

/* Desktop estándar (900px - 1199px) - Ligeramente compacto */
@media screen and (min-width: 900px) and (max-width: 1199px) {
    .navbar {
        height: var(--nav-height-desktop);
        padding: 0 25px;
        gap: 20px;
    }
    
    .navbar-left h2 {
        font-size: 15px;
        padding: 7px 13px 7px 23px;
    }
    
    .navbar-left h2:before {
        width: 7px;
        height: 7px;
        left: 9px;
    }
    
    .menu-principal a {
        font-size: 15px;
        padding: 0 18px;
    }
    
    .boton_ir {
        padding: 9px 20px;
        font-size: 14px;
    }
    
    .rol-text {
        opacity: 0.95;
    }
}

/* Pantallas pequeñas (935px hacia abajo) - Solo mostrar rol, ocultar nombre */
@media screen and (max-width: 935px) {
    .nombre-text {
        display: none !important;
    }
    
    .rol-text::after {
        display: none !important;
    }
    
    .navbar-left h2 {
        max-width: none;
        overflow: visible;
    }
    
    .rol-text {
        white-space: nowrap;
        font-weight: 600 !important;
    }
}

/* Tablet grande (769px - 899px) - Transición hacia móvil */
@media screen and (min-width: 769px) and (max-width: 899px) {
    .navbar {
        height: var(--nav-height-tablet);
        padding: 0 20px;
        gap: 18px;
    }
    
    .navbar-left h2 {
        font-size: 14.5px;
        padding: 6px 12px 6px 22px;
    }
    
    .navbar-left h2:before {
        width: 6px;
        height: 6px;
        left: 8px;
    }
    
    .rol-text {
        font-size: 13px;
        opacity: 0.9;
    }
    
    .nombre-text {
        font-size: 14px;
        font-weight: 500;
    }
    
    .rol-text::after {
        margin: 0 3px 0 1px;
    }
    
    .menu-principal a {
        font-size: 14px;
        padding: 0 15px;
    }
    
    .boton_ir {
        padding: 8px 18px;
        font-size: 13px;
    }
}

/* Tablet (600px - 768px) - Menú desktop visible pero compacto */
@media screen and (min-width: 600px) and (max-width: 768px) {
    .navbar {
        height: var(--nav-height-tablet);
        padding: 0 18px;
        gap: 15px;
    }
    
    .navbar-left {
        max-width: 50%;
    }
    
    .navbar-left h2 {
        font-size: 14px;
        padding: 6px 11px 6px 20px;
    }
    
    .navbar-left h2:before {
        width: 6px;
        height: 6px;
        left: 7px;
    }
    
    .rol-text {
        font-size: 12px;
        opacity: 0.85;
    }
    
    .nombre-text {
        font-size: 13px;
        font-weight: 600;
    }
    
    .rol-text::after {
        margin: 0 2px;
    }
    
    .menu-principal a {
        font-size: 13px;
        padding: 0 12px;
    }
    
    .boton_ir {
        padding: 7px 15px;
        font-size: 12px;
    }
}

/* Móvil grande (481px - 599px) - Menú hamburguesa activado */
@media screen and (min-width: 481px) and (max-width: 599px) {
    .navbar {
        height: var(--nav-height-mobile);
        padding: 0 15px;
        gap: 12px;
    }
    
    .navbar-left {
        flex-shrink: 1;
        min-width: 0;
        max-width: 45%;
    }
    
    .navbar-left h2 {
        font-size: 13.5px;
        padding: 6px 10px 6px 18px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .navbar-left h2:before {
        width: 6px;
        height: 6px;
        left: 6px;
    }
    
    .rol-text {
        font-size: 11.5px;
        opacity: 0.8;
    }
    
    .nombre-text {
        font-size: 12.5px;
        font-weight: 600;
    }
    
    .rol-text::after {
        content: " - ";
        margin: 0 1px;
    }
    
    /* Navbar center oculto solo en desktop - removido para permitir menú móvil */
    
    .navbar-right {
        gap: 12px;
    }
    
    .boton_ir {
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .boton_ir::after {
        margin-left: 6px;
    }
    
    .menu-toggle {
        display: flex;
        width: 35px;
        height: 35px;
    }
    
    .menu-toggle span {
        width: 22px;
        height: 2.5px;
        margin: 2px 0;
    }
}

/* Móvil (361px - 480px) - Layout más compacto */
@media screen and (min-width: 361px) and (max-width: 480px) {
    .navbar {
        height: var(--nav-height-small);
        padding: 0 12px;
        gap: 10px;
    }
    
    .navbar-left {
        max-width: 42%;
    }
    
    .navbar-left h2 {
        font-size: 13px;
        padding: 5px 9px 5px 16px;
    }
    
    .navbar-left h2:before {
        width: 5px;
        height: 5px;
        left: 6px;
    }
    
    .rol-text {
        font-size: 11px;
        opacity: 0.75;
    }
    
    .nombre-text {
        font-size: 12px;
        font-weight: 600;
    }
    
    .boton_ir {
        padding: 5px 10px;
        font-size: 11px;
    }
    
    .menu-toggle {
        width: 32px;
        height: 32px;
    }
    
    .menu-toggle span {
        width: 20px;
        height: 2px;
    }
}

/* Móvil pequeño (320px - 360px) - Solo nombre, sin rol */
@media screen and (min-width: 320px) and (max-width: 360px) {
    .navbar {
        height: var(--nav-height-tiny);
        padding: 0 10px;
        gap: 8px;
    }
    
    .navbar-left {
        max-width: 38%;
    }
    
    .navbar-left h2 {
        font-size: 12px;
        padding: 4px 8px 4px 14px;
    }
    
    .navbar-left h2:before {
        width: 4px;
        height: 4px;
        left: 5px;
    }
    
    /* Ocultar rol completamente */
    .rol-text {
        display: none;
    }
    
    .rol-text::after {
        display: none;
    }
    
    .nombre-text {
        font-size: 11px;
        font-weight: 600;
        margin-left: 0;
    }
    
    .boton_ir {
        padding: 4px 8px;
        font-size: 10px;
    }
    
    .boton_ir::after {
        margin-left: 4px;
        font-size: 14px;
    }
    
    .menu-toggle {
        width: 30px;
        height: 30px;
    }
    
    .menu-toggle span {
        width: 18px;
        height: 2px;
        margin: 1.5px 0;
    }
}

/* Móvil muy pequeño (menos de 320px) - Layout mínimo */
@media screen and (max-width: 319px) {
    .navbar {
        height: 54px;
        padding: 0 8px;
        gap: 6px;
    }
    
    .navbar-left {
        max-width: 35%;
    }
    
    .navbar-left h2 {
        font-size: 11px;
        padding: 3px 6px 3px 12px;
    }
    
    .navbar-left h2:before {
        width: 3px;
        height: 3px;
        left: 4px;
    }
    
    .rol-text {
        display: none;
    }
    
    .rol-text::after {
        display: none;
    }
    
    .nombre-text {
        font-size: 10px;
        font-weight: 600;
    }
    
    .boton_ir {
        display: none; /* Ocultar en pantallas extremadamente pequeñas */
    }
    
    .menu-toggle {
        width: 28px;
        height: 28px;
    }
    
    .menu-toggle span {
        width: 16px;
        height: 1.5px;
        margin: 1px 0;
    }
}

/* ===============================================
   MENÚ MÓVIL OVERLAY
   =============================================== */

@media screen and (max-width: 768px) {
    /* Asegurar que navbar-center sea visible en móvil */
    .navbar-center {
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    
    /* Panel móvil simple sin backdrop */
    .navbar-center .menu-principal {
        position: fixed;
        top: var(--nav-height-mobile);
        left: 0;
        width: 100%;
        height: calc(100vh - var(--nav-height-mobile));
        background: var(--primary-dark);
        z-index: 9999 !important;
        overflow-y: auto;
        
        /* Layout móvil */
        flex-direction: column;
        padding: 0;
        
        /* Estado inicial - oculto con deslizamiento lateral */
        opacity: 0;
        transform: translateX(-100%);
        visibility: hidden;
        transition: 
            opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            visibility 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Estado activo del menú móvil - Panel deslizante */
    .navbar-center .menu-principal.active {
        opacity: 1 !important;
        transform: translateX(0) !important;
        visibility: visible !important;
        pointer-events: all !important;
    }
    
    /* Resetear efectos de desktop */
    .navbar-center .menu-principal::before {
        display: none;
    }
    
    .navbar-center .menu-principal a::before {
        display: none;
    }
    
    .navbar-center .menu-principal .activeURL {
        background-color: rgba(26, 188, 156, 0.2) !important;
        border-left-color: var(--accent-color) !important;
        color: var(--accent-light) !important;
        font-weight: 600 !important;
    }
    
    /* Activar elementos móviles */
    .menu-toggle {
        display: flex !important;
    }
    
    .session-button-mobile {
        display: flex !important;
    }
    
    .session-button-desktop {
        display: none !important;
    }
    
    /* Estilos para enlaces individuales del menú móvil - más compactos */
    .navbar-center .menu-principal li {
        width: 100%;
        height: auto;
        margin: 0;
    }
    
    .navbar-center .menu-principal a {
        width: 100%;
        height: auto;
        padding: 12px 25px !important; /* Reducido de 16px a 12px para menor espaciado vertical */
        margin: 0;
        line-height: 1.3 !important; /* Línea más compacta */
        font-size: 15px !important;
        font-weight: 500 !important;
        border-left: 4px solid transparent;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        transition: all 0.2s ease !important;
        background-color: transparent !important;
    }
    
    .navbar-center .menu-principal a:hover {
        background-color: rgba(26, 188, 156, 0.1) !important;
        border-left-color: var(--accent-color) !important;
        padding-left: 30px !important;
    }
}

/* ===============================================
   ANIMACIONES
   =============================================== */

@keyframes slideDown {
    from { 
        opacity: 0;
        transform: translateY(-20px); 
    }
    to { 
        opacity: 1;
        transform: translateY(0); 
    }
}

@keyframes fadeSlideIn {
    from { 
        opacity: 0; 
        transform: translateX(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

/* Animación inicial del indicador de usuario */
.navbar-left h2 {
    opacity: 0;
    animation: fadeSlideIn 0.8s ease-out 0.2s forwards;
}

/* ===============================================
   MEJORAS DE ACCESIBILIDAD
   =============================================== */

/* Focus states */
.menu-principal a:focus-visible,
.boton_ir:focus-visible,
.menu-toggle:focus-visible {
    outline: 3px solid var(--accent-color);
    outline-offset: 2px;
    background-color: rgba(26, 188, 156, 0.1);
}

/* Optimizaciones para dispositivos táctiles */
@media (hover: none) and (pointer: coarse) {
    .menu-principal a {
        min-height: 48px;
        padding: 16px 25px;
    }
    
    .menu-toggle {
        min-width: 48px;
        min-height: 48px;
    }
    
    .boton_ir {
        min-height: 44px;
        padding: 12px 16px;
    }
    
    /* Eliminar efectos hover */
    .menu-principal a:hover {
        padding-left: 25px;
        background-color: inherit;
        border-left-color: transparent;
    }
    
    .boton_ir:hover {
        transform: none;
        box-shadow: var(--shadow-sm);
    }
    
    .menu-toggle:hover {
        background-color: inherit;
    }
}

/* Soporte para movimiento reducido */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .navbar-center .menu-principal.active {
        animation: none;
        opacity: 1;
        transform: none;
    }
}

/* ===============================================
   ESTILOS BASE DEL BODY
   =============================================== */

body {
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: var(--light-gray);
    color: var(--text-primary);
    /* line-height: 1.6; */
    font-size: 16px;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    
    /* Ocultar barras de scroll */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE y Edge antiguos */
}

/* WebKit (Chrome, Safari, Edge moderno, Opera) */
body::-webkit-scrollbar {
    display: none;
}

/* Mejoras para rendimiento en móviles */
*,
*::before,
*::after {
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Permitir selección en textos importantes */
.navbar-left h2,
.menu-principal a {
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
    user-select: text;
}

/* ===============================================
   MODAL DE CONFIRMACIÓN (LOGOUT)
   =============================================== */

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
    transition: opacity var(--transition-normal);
}

.modal-overlayyy.active {
    display: flex;
    opacity: 1;
    animation: fadeIn 0.4s forwards;
}

.modal-containerrr {
    background: var(--light-color);
    padding: 30px;
    width: 90%;
    max-width: 360px;
    border-radius: var(--border-radius-lg);
    text-align: center;
    box-shadow: var(--shadow-lg);
    transform: scale(0.95);
    animation: scaleIn 0.4s forwards;
    border: 1px solid var(--gray-lighter);
}

.modal-iconnn {
    font-size: 50px;
    color: var(--danger-color);
    margin-bottom: 20px;
    animation: pulse 1.5s infinite;
}

.modal-contentttt h2 {
    margin: 0;
    font-size: 24px;
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 10px;
}

.modal-contentttt p {
    font-size: 16px;
    color: var(--text-secondary);
    margin: 15px 0;
    line-height: 1.5;
}

.modal-buttonsss {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    gap: 10px;
}

.btn-cancelll,
.btn-confirmmm {
    border: none;
    padding: 12px 20px;
    flex: 1;
    font-size: 15px;
    cursor: pointer;
    border-radius: var(--border-radius-md);
    font-weight: 600;
    transition: all var(--transition-normal);
    letter-spacing: 0.5px;
    outline: none;
}

.btn-cancelll {
    background: var(--gray-lighter);
    color: var(--text-secondary);
    border: 1px solid var(--gray-light);
}

.btn-confirmmm {
    background: linear-gradient(to right, var(--danger-color), #c0392b);
    color: var(--light-color);
    box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
}

.btn-cancelll:hover {
    background: var(--gray-light);
    color: var(--text-primary);
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

/* ===============================================
   ANIMACIONES ADICIONALES
   =============================================== */

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleIn {
    from { 
        transform: scale(0.9); 
        opacity: 0; 
    }
    to { 
        transform: scale(1); 
        opacity: 1; 
    }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* ===============================================
   SOPORTE PARA PWA Y DISPOSITIVOS CON NOTCH
   =============================================== */

/* Soporte para dispositivos con notch (iPhone X+) */
@supports (padding: max(0px)) {
    .navbar {
        padding-left: max(15px, env(safe-area-inset-left));
        padding-right: max(15px, env(safe-area-inset-right));
    }
    
    @media screen and (max-width: 768px) {
        .navbar-center .menu-principal {
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }
        
        .navbar-center .menu-principal a {
            padding-left: max(25px, calc(25px + env(safe-area-inset-left)));
            width: 100%;
        }
    }
}

/* Soporte para PWA y modo standalone */
@media (display-mode: standalone) {
    .navbar {
        padding-top: env(safe-area-inset-top);
    }
}

/* Optimización para alto contraste */
@media (prefers-contrast: high) {
    .navbar {
        border-bottom: 2px solid var(--accent-color);
    }
    
    .navbar-center .menu-principal a {
        border-bottom-width: 2px;
    }
    
    .activeURL {
        border-left-width: 6px !important;
    }
}

/* ===============================================
   BOTONES DE SESIÓN RESPONSIVOS
   =============================================== */

/* Botón de sesión móvil - prioritario en móvil */
.session-button-mobile {
    display: none;
    margin-right: 10px;
    order: -1; /* Prioridad máxima */
}

.session-button-mobile .mobile-session {
    display: flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, var(--accent-color), var(--accent-dark));
    color: var(--light-color);
    text-decoration: none;
    border-radius: var(--border-radius-sm);
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 500;
    box-shadow: var(--shadow-sm);
    transition: 
        all var(--transition-fast),
        transform var(--transition-fast);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.session-button-mobile .mobile-session:hover {
    background: linear-gradient(135deg, var(--accent-light), var(--accent-color));
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.session-button-mobile .mobile-session i {
    font-size: 11px;
}

.session-button-mobile .mobile-session span {
    font-size: 11px;
    font-weight: 600;
}

/* ===============================================
   REGLAS POR DEFECTO PARA DESKTOP
   =============================================== */

/* Por defecto: botón de sesión desktop visible, móvil oculto */
.session-button-mobile {
    display: none;
}

.session-button-desktop {
    display: flex;
}

/* Por defecto: menú hamburguesa oculto en desktop */
.menu-toggle {
    display: none;
}

    </style>
</head>
<body>
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
    </div>    <div class="navbar" style="z-index: 999999;">
        <!-- Información del usuario a la izquierda -->
        <div class="navbar-left">
            <!-- Botón de sesión prioritario en móvil -->
            <div class="session-button-mobile">                <?php if(isset($_SESSION['id_rol'])):?>
                    <a href="<?php echo BASE_URL; ?>includes/session/salir.php" class="boton_ir mobile-session">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>includes/session/login.php" class="boton_ir mobile-session">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Entrar</span>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Información del usuario -->
            <?php if (isset($_SESSION['id_rol']) && $nombreUser !== null): ?>
                <h2 class="indicadorRol">
                    <span class="rol-text">
                        <?php 
                            switch ($_SESSION['id_rol']) {
                                case '1':
                                    echo "Admin";
                                    break;
                                case '2':
                                    echo "Gestor";
                                    break;
                                case '3':
                                    echo "Presupuesto";
                                    break;
                                case '4':
                                    echo "SENNOVA";
                                    break;
                                case '5':
                                    echo "Tecnoparque";
                                    break;
                                case '6':
                                    echo "Tecnoacademia";
                                    break;
                                case '7':
                                    echo "Acceso";
                                    break;
                                default:
                                    echo "";
                            }
                        ?>
                    </span>
                    <span class="nombre-text">
                        <?php echo htmlspecialchars($nombreUser); ?>
                    </span>
                </h2>
            <?php endif; ?>
        </div>        <!-- Navegación central -->
        <div class="navbar-center">
            <nav class="menu">
                <ul class="menu-principal" id="menu-principal">
                <?php
                    // Definir la ruta actual una sola vez y en minúsculas
                    $currentPath = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                ?>
                <!-- ----- inicio para rolres 1,2 y 3--------- -->
                    <?php if (isset($_SESSION['id_rol']) && in_array($_SESSION['id_rol'], ['9'])): ?>
                        <li>
                            <?php
                                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);                        
                                ?>
                            <a href="/home/appscide/public_html/disenoCurricular/app/forms/index.php" 
                            class="<?php echo ($currentPath === '/home/appscide/public_html/disenoCurricular/app/forms/index.php') ? 'activeURL' : ''; ?>">Diseños Curriculares</a>
                        </li>
                        <?php endif; ?>
                        <!-- ----- fin para rolres 1,2 y 3--------- -->
                        
                        <!-- ----- inicio para rol 4--------- -->
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '7'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 4 (SENNOVA) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Solicitud Rol
                                </a>
                            </li>
                        <!-- Puedes agregar más opciones aquí para el rol 4 -->
                        <?php endif; ?>
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '4'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 4 (SENNOVA) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Presupuesto - SENNOVA
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/tecnoparque/metas/index.php') ? 'activeURL' : ''; ?>">
                                    Metas - Tecnoparque
                                </a>
                            </li>
                        <!-- Puedes agregar más opciones aquí para el rol 4 -->
                        <?php endif; ?>
                        <!-- ----- fin para rol 4--------- -->
                        <!-- ----- inicio para rol 5--------- -->
                        
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '5'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 5 (Tecnoparque) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Presupuesto
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/Tecnoparque/metas/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/tecnoparque/metas/index.php') ? 'activeURL' : ''; ?>">
                                    Metas
                                </a>
                            </li>

                        <?php endif; ?>
                        <!-- ----- fin para rol 5--------- -->
                        <!-- ----- inicio para rol 6--------- -->
                        
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '6'): ?>
                            <!-- Bloque exclusivo para usuarios con rol 6 (Tecnoacademia) -->
                            <li>
                                <a href="<?php echo BASE_URL; ?>app/SENNOVA/General/index.php"
                                    class="<?php echo ($currentPath === '/viaticosapp/app/sennova/general/index.php') ? 'activeURL' : ''; ?>">
                                    Presupuesto
                                </a>
                            </li>
                        <!-- Puedes agregar más opciones aquí para el rol 6 -->
                    <?php endif; ?>
                <!-- ----- fin para rol 6--------- -->
                <!-- ----- inicio para rol 3--------- -->
                    <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '3'): ?>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/presupuesto/index.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/presupuesto/index.php') ? 'activeURL' : ''; ?>">Registros RP</a>
                            
                        </li>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/presupuesto/historialOP.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/presupuesto/historialOP.php') ? 'activeURL' : ''; ?>">Registros RP (Viáticos)</a>
                        </li>
                <!-- ----- fin para rol 3--------- -->
                <!-- ----- inicio para rol 1--------- -->
                    <?php elseif (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '1'): ?>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/admin/index.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/admin/index.php') ? 'activeURL' : ''; ?>">Panel de control</a>
                        </li>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/admin/solicitudes/index.php" 
                            class="<?php echo ($currentPath === '/viaticosApp/app/admin/solicitudes') ? 'activeURL' : ''; ?>">Solicitudes de cambio de rol</a>
                        </li>
                <!-- ----- fin para rol 1--------- -->
                <!-- ----- inicio para rol 2--------- -->
                    <?php elseif (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '2'): ?>
                        <li>
                            <?php
                            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                            ?>
                            
                            <a href="<?php echo BASE_URL; ?>app/gestor/index.php" 
                               class="<?php echo ($currentPath === '/viaticosApp/app/gestor/index.php') ? 'activeURL' : ''; ?>">
                               Gestor
                            </a>
                        </li>
                <!-- ----- fin para rol 2--------- -->                    <?php endif; ?>
                  <!-- ----- Enlace de perfil para todos los usuarios logueados--------- -->
                <!-- <?php //if (isset($_SESSION['numero_documento'])): ?>
                    <li>
                        <a href="<?php //echo BASE_URL; ?>public/share/cuenta.php" 
                           class="<?php //echo ($currentPath === '/viaticosapp/public/share/cuenta.php') ? 'activeURL' : ''; ?>">
                           <i class="fas fa-user-circle" style="margin-right: 8px;"></i>Mi Perfil
                        </a>
                    </li>
                <?php //endif; ?> -->
                <!-- ----- fin enlace de perfil--------- -->
                
                </ul>
            </nav>
        </div>        <!-- Acciones a la derecha -->
        <div class="navbar-right">
            <!-- Botón hamburguesa - solo visible en móvil -->
            <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú de navegación">
                <span></span>
                <span></span>
                <span></span>
            </button>
              <!-- Botón de sesión para desktop -->
            <div class="session-button-desktop">
                <?php if(isset($_SESSION['id_rol'])):?>
                    <a href="<?php echo BASE_URL; ?>includes/session/salir.php" class="boton_ir">Cerrar sesión</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>includes/session/login.php" class="boton_ir">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>assets/js/header.js"></script>
</body>
</html>
<script>
function salir() {
    const modal = document.getElementById('logoutModal');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    
    modal.classList.add('active');
    
    return new Promise((resolve) => {
        confirmBtn.onclick = () => {
            modal.classList.remove('active');
            resolve(true);
            window.location.href = '<?php echo BASE_URL; ?>includes/session/salir.php';
        };
        
        cancelBtn.onclick = () => {
            modal.classList.remove('active');
            resolve(false);
        };
        
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                resolve(false);
            }
        };
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los enlaces de cerrar sesión
    const logoutButtons = document.querySelectorAll('a[href*="salir.php"]');
    
    logoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir navegación inmediata
            salir().then(confirmed => {
                // Solo navegar si el usuario confirmó
                if (confirmed) {
                    window.location.href = this.href;
                }
            });
        });
    });
});
</script>
