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

    // Manejo de la imagen
    $imagenRuta = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/uploads/saldos/';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        $nombreArchivo = uniqid('saldo_') . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            $imagenRuta = '/uploads/saldos/' . $nombreArchivo;
        } else {
            die('Error al guardar la imagen.');
        }
    }

    // Usar la conexión de la clase gestor1
    $gestor = new gestor1();
    $conexion = $gestor->obtenerConexion();

    if ($conexion === null) {
        die('Error: No se pudo establecer la conexión a la base de datos.');
    }

    // Guardar datos en la base de datos
    $query = "INSERT INTO saldos_asignados (NOMBRE_PERSONA, DOCUMENTO_PERSONA, FECHA_INICIO, FECHA_FIN, FECHA_PAGO, SALDO_ASIGNADO, CODIGO_CRP, CODIGO_CDP, IMAGEN_RUTA)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('sssssssss', $nombre, $documento, $fechaInicio, $fechaFin, $fechaPago, $saldoAsignado, $codigoCRP, $codigoCDP, $imagenRuta);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: insert_saldo_asiganado.php?estado=exito');
    } else {
        header('Location: insert_saldo_asiganado.php?estado=error');
    }
}
?>