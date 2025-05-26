<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosCRP.php';

function excelDateToDate($excelDate) {
    if (!is_numeric($excelDate)) {
        return null;
    }
    $unixDate = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $unixDate);
}

function excelDateTimeToDateTime($excelDateTime) {
    $excelDateTime = str_replace(',', '.', $excelDateTime);

    if (!is_numeric($excelDateTime)) {
        return null;
    }

    $days = floor($excelDateTime);
    $fraction = $excelDateTime - $days;

    $unixDateTime = ($days - 25569) * 86400;

    $seconds = round($fraction * 86400);
    $unixDateTime += $seconds;

    return gmdate("Y-m-d H:i:s", $unixDateTime);
}

function parseDateOrExcel($value) {
    $value = str_replace(',', '.', $value);
    if (is_numeric($value)) {
        return excelDateTimeToDateTime($value);
    } else {
        $time = strtotime($value);
        return ($time !== false) ? date("Y-m-d H:i:s", $time) : null;
    }
}

/**
 * Limpia un valor numérico eliminando caracteres no válidos.
 * Por ejemplo, convierte "48.294.866,00" a "48294866.00".
 */
function limpiarValorNumerico($valor) {
    // Reemplazar puntos por nada y comas por puntos para decimales
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    // Eliminar todos los caracteres excepto números y puntos
    $valor = preg_replace('/[^0-9.]/', '', $valor);
    return $valor;
}

function depurar_datos_crp($archivo) {
    // Validación básica del archivo
    if (!isset($archivo['tmp_name']) || empty($archivo['tmp_name'])) {
        throw new Exception('No se recibió ningún archivo');
    }

    // Leer todas las líneas del archivo
    $lineas = file($archivo['tmp_name']);
    if (empty($lineas)) {
        throw new Exception('El archivo está vacío');
    }

    $datosDepurados = [];

    foreach ($lineas as $numeroLinea => $linea) {
        $linea = trim($linea);
        if (empty($linea)) continue;
        $datos = explode(';', $linea);
        // Mapear los campos según la nueva estructura
        $datosDepurados[] = [
            'rp_id' => isset($datos[0]) ? trim($datos[0]) : null,
            'cdp_id' => isset($datos[1]) ? trim($datos[1]) : null,
            'CODIGO_CRP' => isset($datos[2]) ? trim($datos[2]) : null,
            'CODIGO_CDP' => isset($datos[3]) ? trim($datos[3]) : null,
            'Numero_Documento' => isset($datos[4]) ? trim($datos[4]) : null,
            'Fecha_de_Registro' => isset($datos[5]) ? parseDateOrExcel(trim($datos[5])) : null,
            'Fecha_de_Creacion' => isset($datos[6]) ? parseDateOrExcel(trim($datos[6])) : null,
            'Estado' => isset($datos[7]) ? trim($datos[7]) : null,
            'Dependencia' => isset($datos[8]) ? trim($datos[8]) : null,
            'Rubro' => isset($datos[9]) ? trim($datos[9]) : null,
            'Descripcion' => isset($datos[10]) ? trim($datos[10]) : null,
            'Fuente' => isset($datos[11]) ? trim($datos[11]) : null,
            'Valor_Inicial' => isset($datos[12]) ? floatval(limpiarValorNumerico($datos[12])) : null,
            'Valor_Operaciones' => isset($datos[13]) ? floatval(limpiarValorNumerico($datos[13])) : null,
            'Valor_Actual' => isset($datos[14]) ? floatval(limpiarValorNumerico($datos[14])) : null,
            'Saldo_por_Utilizar' => isset($datos[15]) ? floatval(limpiarValorNumerico($datos[15])) : null,
            'Tipo_Identificacion' => isset($datos[16]) ? trim($datos[16]) : null,
            'Identificacion' => isset($datos[17]) ? trim($datos[17]) : null,
            'Nombre_Razon_Social' => isset($datos[18]) ? trim($datos[18]) : null,
            'Medio_de_Pago' => isset($datos[19]) ? trim($datos[19]) : null,
            'Tipo_Cuenta' => isset($datos[20]) ? trim($datos[20]) : null,
            'Numero_Cuenta' => isset($datos[21]) ? trim($datos[21]) : null,
            'Estado_Cuenta' => isset($datos[22]) ? trim($datos[22]) : null,
            'Entidad_Nit' => isset($datos[23]) ? trim($datos[23]) : null,
            'Entidad_Descripcion' => isset($datos[24]) ? trim($datos[24]) : null,
            'Solicitud_CDP' => isset($datos[25]) ? trim($datos[25]) : null,
            'CDP' => isset($datos[26]) ? trim($datos[26]) : null,
            'Compromisos' => isset($datos[27]) ? trim($datos[27]) : null,
            'Cuentas_por_Pagar' => isset($datos[28]) ? trim($datos[28]) : null,
            'Obligaciones' => isset($datos[29]) ? trim($datos[29]) : null,
            'Ordenes_de_Pago' => isset($datos[30]) ? trim($datos[30]) : null,
            'Reintegros' => isset($datos[31]) ? floatval(limpiarValorNumerico($datos[31])) : null,
            'Fecha_Documento_Soporte' => isset($datos[32]) ? parseDateOrExcel(trim($datos[32])) : null,
            'Tipo_Documento_Soporte' => isset($datos[33]) ? trim($datos[33]) : null,
            'Numero_Documento_Soporte' => isset($datos[34]) ? trim($datos[34]) : null,
            'Observaciones' => isset($datos[35]) ? trim($datos[35]) : null
        ];
    }

    if (empty($datosDepurados)) {
        throw new Exception('No se pudo procesar ninguna línea del archivo');
    }

    return $datosDepurados;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dataCRP'])) {
    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['numero_documento'])) {
            throw new Exception('Usuario no autenticado');
        }

        $usuario_id = $_SESSION['numero_documento'];
        $conexion = new Conexion();
        $conn = $conexion->obtenerConexion();
        
        $admin = new admin2();
        $datosDepurados = depurar_datos_crp($_FILES['dataCRP']);
        $resultado = $admin->procesar_csv_crp($datosDepurados);

        // Registrar la actualización
        $stmt = $conn->prepare("
            INSERT INTO registros_actualizaciones 
            (tipo_tabla, nombre_archivo, registros_actualizados, registros_nuevos, usuario_id)
            VALUES ('CRP', ?, ?, ?, ?)
        ");
        
        $nombre_archivo = $_FILES['dataCRP']['name'];
        $stmt->bindParam(1, $nombre_archivo, PDO::PARAM_STR);
        $stmt->bindParam(2, $resultado['updated'], PDO::PARAM_INT);
        $stmt->bindParam(3, $resultado['inserted'], PDO::PARAM_INT);
        $stmt->bindParam(4, $usuario_id, PDO::PARAM_STR);
        $stmt->execute();

        $response = [
            'success' => true,
            'inserted' => $resultado['inserted'],
            'updated' => $resultado['updated'],
            'errors' => $resultado['errors']
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => ' Metodo no permitido o archivo no recibido',
        'message' => ' Asegurese de que esta  enviando una solicitud POST y que el archivo se ha subido correctamente.'
    ]);
    exit;
}
