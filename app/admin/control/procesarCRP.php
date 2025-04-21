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
        // Limpiar la línea de espacios y caracteres especiales
        $linea = trim($linea);
        if (empty($linea)) continue;

        // Separar por punto y coma
        $datos = explode(';', $linea);
        
        // Si faltan campos, rellenar con nulls hasta tener 34 campos
        $datos = array_pad($datos, 34, null);

        try {
            $datosDepurados[] = [
                'CODIGO_CRP'           => !empty($datos[0]) ? trim($datos[0]) : null,
                'CODIGO_CDP'           => !empty($datos[1]) ? trim($datos[1]) : null,
                'Numero_Documento'     => !empty($datos[2]) ? trim($datos[2]) : null,
                'Fecha_de_Registro'    => !empty($datos[3]) ? parseDateOrExcel(trim($datos[3])) : null,
                'Fecha_de_Creacion'    => !empty($datos[4]) ? parseDateOrExcel(trim($datos[4])) : null,
                'Estado'               => !empty($datos[5]) ? trim($datos[5]) : null,
                'Dependencia'          => !empty($datos[6]) ? trim($datos[6]) : null,
                'Rubro'                => !empty($datos[7]) ? trim($datos[7]) : null,
                'Descripcion'          => !empty($datos[8]) ? trim($datos[8]) : null,
                'Fuente'               => !empty($datos[9]) ? trim($datos[9]) : null,
                'Valor_Inicial'        => !empty($datos[10]) ? floatval(limpiarValorNumerico($datos[10])) : null,
                'Valor_Operaciones'    => !empty($datos[11]) ? floatval(limpiarValorNumerico($datos[11])) : null,
                'Valor_Actual'         => !empty($datos[12]) ? floatval(limpiarValorNumerico($datos[12])) : null,
                'Saldo_por_Utilizar'   => !empty($datos[13]) ? floatval(limpiarValorNumerico($datos[13])) : null,
                'Tipo_Identificacion'  => !empty($datos[14]) ? trim($datos[14]) : null,
                'Identificacion'       => !empty($datos[15]) ? trim($datos[15]) : null,
                'Nombre_Razon_Social'  => !empty($datos[16]) ? trim($datos[16]) : null,
                'Medio_de_Pago'        => !empty($datos[17]) ? trim($datos[17]) : null,
                'Tipo_Cuenta'          => !empty($datos[18]) ? trim($datos[18]) : null,
                'Numero_Cuenta'        => !empty($datos[19]) ? trim($datos[19]) : null,
                'Estado_Cuenta'        => !empty($datos[20]) ? trim($datos[20]) : null,
                'Entidad_Nit'          => !empty($datos[21]) ? trim($datos[21]) : null,
                'Entidad_Descripcion'  => !empty($datos[22]) ? trim($datos[22]) : null,
                'Solicitud_CDP'        => !empty($datos[23]) ? trim($datos[23]) : null,
                'CDP'                  => !empty($datos[24]) ? trim($datos[24]) : null,
                'Compromisos'          => !empty($datos[25]) ? trim($datos[25]) : null,
                'Cuentas_por_Pagar'    => !empty($datos[26]) ? trim($datos[26]) : null,
                'Obligaciones'         => !empty($datos[27]) ? trim($datos[27]) : null,
                'Ordenes_de_Pago'      => !empty($datos[28]) ? trim($datos[28]) : null,
                'Reintegros'           => !empty($datos[29]) ? floatval(limpiarValorNumerico($datos[29])) : null,
                'Fecha_Documento_Soporte'    => !empty($datos[30]) ? parseDateOrExcel(trim($datos[30])) : null,
                'Tipo_Documento_Soporte'     => !empty($datos[31]) ? trim($datos[31]) : null,
                'Numero_Documento_Soporte'   => !empty($datos[32]) ? trim($datos[32]) : null,
                'Observaciones'              => !empty($datos[33]) ? trim($datos[33]) : null,
            ];
        } catch (Exception $e) {
            error_log("Error procesando línea $numeroLinea: " . $e->getMessage());
            continue;
        }
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
