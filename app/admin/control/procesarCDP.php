<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosCDP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

function excelDateToDateTime($excelDate) {
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

function depurar_datos_cdp($archivo) {
    $lineas = file($archivo['tmp_name']);
    $datosDepurados = [];

    foreach ($lineas as $linea) {
        $datos = explode(';', $linea);
        // Mapear los campos según la nueva estructura
        $datosDepurados[] = [
            'cdp_id' => trim($datos[0]),
            'CODIGO_CDP' => trim($datos[1]),
            'Numero_Documento' => trim($datos[2]),
            'Fecha_de_Registro' => parseDateOrExcel(trim($datos[3])),
            'Fecha_de_Creacion' => parseDateOrExcel(trim($datos[4])),
            'Estado' => trim($datos[5]),
            'Dependencia' => trim($datos[6]),
            'Rubro' => trim($datos[7]),
            'Fuente' => trim($datos[8]),
            'Recurso' => trim($datos[9]),
            'Valor_Inicial' => is_numeric(limpiarValorNumerico($datos[10])) ? floatval(limpiarValorNumerico($datos[10])) : null,
            'Valor_Operaciones' => is_numeric(limpiarValorNumerico($datos[11])) ? floatval(limpiarValorNumerico($datos[11])) : null,
            'Valor_Actual' => is_numeric(limpiarValorNumerico($datos[12])) ? floatval(limpiarValorNumerico($datos[12])) : null,
            'Saldo_por_Comprometer' => is_numeric(limpiarValorNumerico($datos[13])) ? floatval(limpiarValorNumerico($datos[13])) : null,
            'Objeto' => trim($datos[14]),
            'Compromisos' => trim($datos[15]),
            'Cuentas_por_Pagar' => trim($datos[16]),
            'Obligaciones' => trim($datos[17]),
            'Ordenes_de_Pago' => trim($datos[18]),
            'Reintegros' => isset($datos[19]) && is_numeric(limpiarValorNumerico($datos[19])) ? floatval(limpiarValorNumerico($datos[19])) : null
        ];
    }

    return $datosDepurados;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dataCDP'])) {
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
        
        $admin = new admin();
        $datosDepurados = depurar_datos_cdp($_FILES['dataCDP']);
        $resultado = $admin->procesar_csv_cdp($datosDepurados);

        // Registrar la actualización
        $stmt = $conn->prepare("
            INSERT INTO registros_actualizaciones 
            (tipo_tabla, nombre_archivo, registros_actualizados, registros_nuevos, usuario_id)
            VALUES ('CDP', ?, ?, ?, ?)
        ");
        
        $nombre_archivo = $_FILES['dataCDP']['name'];
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
        'error' => 'Método no permitido o archivo no recibido',
        'message' => 'Asegúrese de que está enviando una solicitud POST y que el archivo se ha subido correctamente.'
    ]);
    exit;
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
