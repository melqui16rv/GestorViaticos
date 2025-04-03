<?php
// ...existing code...

// Capturar la selección del usuario, por ejemplo, a través de un parámetro GET
$page = isset($_GET['page']) ? $_GET['page'] : 'default';

// Definir la ruta base de los archivos a incluir
$base_path = '../../../app/shareFolder/';

// Crear un array de páginas permitidas para evitar inclusiones no deseadas
$allowed_pages = ['navbar', 'footer', 'sidebar', 'default'];

// Verificar si la página solicitada está permitida
if (in_array($page, $allowed_pages)) {
    $file_to_include = $base_path . $page . '.php';
} else {
    // Página por defecto si la solicitada no está permitida
    $file_to_include = $base_path . 'default.php';
}

// Incluir el archivo correspondiente
require $file_to_include;

// ...existing code...


// ---------------------------------------
// colorres:
// #2d4e6a azul oscuro
// #4fb491 verde(esmeralda)
// #66ae98 verde(esmeralda, claro)
// #ffff blanco