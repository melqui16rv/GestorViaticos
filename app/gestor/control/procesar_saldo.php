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
    // 1) Capturar datos del formulario
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

    // Obtener los IDs correspondientes a los códigos CDP y CRP
    $cdpId = $gestor->obtenerCdpIdPorCodigo($codigoCDP);
    $rpId = $gestor->obtenerRpIdPorCodigo($codigoCRP);

    if (!$cdpId || !$rpId) {
        error_log("No se pudieron obtener los IDs: CDP ID: $cdpId, RP ID: $rpId");
        header("Location: insert_saldo_asiganado.php?estado=error");
        exit;
    }

    // 2) Insertar el saldo asignado (devuelve el ID recién creado si todo sale bien)
    $nuevoSaldoId = $gestor->insertarSaldoAsignado(
        $nombre,
        $documento,
        $fechaInicio,
        $fechaFin,
        $fechaPago,
        $saldoAsignado,
        $cdpId,
        $rpId
    );

    // Verificar si se insertó correctamente
    if (!$nuevoSaldoId) {
        header("Location: insert_saldo_asiganado.php?estado=error");
        exit;
    }

    // 3) Verificar si se subió una imagen
    if (isset($_FILES['mi_imagen']) && $_FILES['mi_imagen']['error'] === UPLOAD_ERR_OK) {
        
        // Nombre temporal y nombre original
        $nombreTemporal = $_FILES['mi_imagen']['tmp_name'];
        $nombreOriginal = $_FILES['mi_imagen']['name'];
        $fileType       = $_FILES['mi_imagen']['type'];
        $extension      = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Validar MIME (ej: "image/jpeg", "image/png", etc.)
        if (!str_starts_with($fileType, 'image/')) {
            die("El archivo subido no es una imagen válida.");
        }

        // Validar extensión
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        if (!in_array($extension, $allowed)) {
            die("Solo se permiten extensiones: " . implode(', ', $allowed));
        }

        // 4) Mover el archivo a 'uploads/'
        $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . uniqid() . '_' . $nombreOriginal;
        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            // 5) Guardar la ruta en la BD (tabla imagenes_saldos_asignados)
            $rutaParaBD = '/uploads/' . basename($rutaDestino);
            $gestor->insertarImagenSaldoAsignado($nuevoSaldoId, $nombreOriginal, $rutaParaBD);
        }
        // else => Manejar error de move_uploaded_file si lo deseas
    }

    // 6) Redirigir con éxito
    header("Location: insert_saldo_asiganado.php?estado=exito");
    exit;
}
?>