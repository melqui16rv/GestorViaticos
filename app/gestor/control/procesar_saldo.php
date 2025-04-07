<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/metodosGestor.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/crpAsociados.php';

requireRole(['2']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $fechaInicio = $_POST['fecha_inicio'] ?? null;
    $fechaFin = $_POST['fecha_fin'] ?? null;
    $fechaPago = $_POST['fecha_pago'] ?? null;
    $saldoAsignado = $_POST['saldo_asignado'] ?? null;
    $codigoCDP = $_POST['codigo_cdp'] ?? null;
    $codigoCRP = $_POST['codigo_crp'] ?? null;
    $fechaPago = empty($fechaPago) ? null : $fechaPago;

    // Instancia de la clase gestor1
    $gestor = new gestor1();

    // 1) Insertar el saldo asignado (devuelve el ID recién creado si todo sale bien)
    $nuevoSaldoId = $gestor->insertarSaldoAsignado(
        $nombre,
        $documento,
        $fechaInicio,
        $fechaFin,
        $fechaPago,
        $saldoAsignado,
        $codigoCDP,
        $codigoCRP
    );

    if (!$nuevoSaldoId) {
        // Si no se insertó, redirigir con error
        header("Location: insert_saldo_asiganado.php?estado=error");
        exit;
    }

    // 2) Verificar si se subió una imagen
    if (isset($_FILES['mi_imagen']) && $_FILES['mi_imagen']['error'] === UPLOAD_ERR_OK) {
        // Nombre temporal y nombre real
        $nombreTemporal = $_FILES['mi_imagen']['tmp_name'];
        $nombreOriginal = $_FILES['mi_imagen']['name'];

        // Crear ruta destino única (para evitar colisiones de archivos con el mismo nombre)
        // Nota: 'uploads/' debe ser una carpeta con permisos de escritura
        $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . uniqid() . '_' . $nombreOriginal;

        // Mover el archivo desde la carpeta temporal a nuestro destino
        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            // Generar la ruta que se guardará en la BD (ej. relativa al DocumentRoot)
            // Podrías guardarla como '/uploads/nombregenerado.jpg'
            $rutaParaBD = '/uploads/' . basename($rutaDestino);

            // 3) Insertar el registro de la imagen en la tabla imagenes_saldos_asignados
            $gestor->insertarImagenSaldoAsignado($nuevoSaldoId, $nombreOriginal, $rutaParaBD);
        }
        // Si falla el move_uploaded_file, podrías manejar el error si lo deseas
    }

    // Redirigir con éxito
    header("Location: insert_saldo_asiganado.php?estado=exito");
    exit;
}
?>