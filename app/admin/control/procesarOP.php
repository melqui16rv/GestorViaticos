<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosOP.php';

/**
 * Convierte una fecha de Excel (número) a un string con formato YYYY-mm-dd
 * (para campos DATE). Retorna null si no es válido.
 */
function excelDateToDate($excelDate) {
    if (!is_numeric($excelDate)) {
        return null;
    }
    $unixDate = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $unixDate);
}

/**
 * Convierte una fecha/hora de Excel (número) a un string con formato YYYY-mm-dd H:i:s
 * (para campos DATETIME). Retorna null si no es válido.
 */
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
 * Intenta parsear un valor como fecha/hora de Excel o texto “normal” de fecha/hora.
 * Devuelve un string en formato YYYY-mm-dd H:i:s o null si no es válido.
 */
function parseDateOrExcel($value) {
    $value = str_replace(',', '.', $value);
    if (is_numeric($value)) {
        return excelDateTimeToDateTime($value);
    }
    $time = strtotime($value);
    return ($time !== false) ? date("Y-m-d H:i:s", $time) : null;
}

/**
 * Para los campos que son DATE (solo fecha), almacenamos en formato YYYY-mm-dd.
 */
function parseDateOrExcelForDate($value) {
    $value = str_replace(',', '.', $value);
    if (is_numeric($value)) {
        // Convertir de excelDate a DATE (sin hora)
        return excelDateToDate($value);
    }
    $time = strtotime($value);
    return ($time !== false) ? date("Y-m-d", $time) : null;
}

/**
 * Limpia un valor numérico eliminando caracteres no válidos.
 * Por ejemplo, convierte "695.250,00" a "695250.00".
 */
function limpiarValorNumerico($valor) {
    // Reemplazar coma por punto para decimales
    $valor = str_replace(',', '.', $valor);
    // Eliminar todos los caracteres excepto números y puntos
    $valor = preg_replace('/[^0-9.]/', '', $valor);
    // Si hay más de un punto, eliminar los puntos adicionales
    if (substr_count($valor, '.') > 1) {
        $partes = explode('.', $valor);
        $valor = array_shift($partes) . '.' . implode('', $partes);
    }
    return $valor;
}

/**
 * Mapea y “depura” cada línea del CSV para crear un array asociativo con 52 columnas,
 * correspondiente a la nueva estructura de la tabla `op`.
 */
function depurar_datos_op($archivo) {
    if (!isset($archivo['tmp_name']) || empty($archivo['tmp_name'])) {
        throw new Exception('No se recibió ningún archivo');
    }
    if (!is_readable($archivo['tmp_name'])) {
        throw new Exception('El archivo no es legible');
    }
    $filesize = filesize($archivo['tmp_name']);
    if ($filesize === 0) {
        throw new Exception('El archivo está vacío');
    }

    $lineas = file($archivo['tmp_name']);
    if (!$lineas || count($lineas) < 1) {
        throw new Exception('El archivo no contiene datos válidos');
    }

    $datosDepurados = [];
    $lineNumber = 0;

    foreach ($lineas as $linea) {
        $lineNumber++;
        // Elimina saltos de línea y espacios extra
        $linea = trim(preg_replace('/[\r\n]+/', '', $linea));
        if (empty($linea)) {
            continue;
        }

        // Separa por punto y coma
        $datos = explode(';', $linea);

        // Verificamos que tenga al menos 52 columnas
        if (count($datos) < 52) {
            throw new Exception("Error en la línea $lineNumber: La línea no tiene las 52 columnas requeridas.");
        }

        // Helper para parsear float
        $parseFloat = function($val, $lineNumber, $columnName) {
            $val = limpiarValorNumerico($val);
            if (!is_numeric($val)) {
                throw new Exception("Error en la línea $lineNumber, columna $columnName: $val no es un número válido.");
            }
            return floatval($val);
        };

        // Construimos el array asociativo con las 52 columnas
        try {
            $datosDepurados[] = [
                'op_id'                      => isset($datos[0]) ? trim($datos[0]) : null,
                'rp_id'                      => isset($datos[1]) ? trim($datos[1]) : null,
                'cdp_id'                     => isset($datos[2]) ? trim($datos[2]) : null,
                'CODIGO_OP'                  => trim($datos[3]),
                'CODIGO_CRP'                 => trim($datos[4]),
                'CODIGO_CDP'                 => trim($datos[5]),
                'Numero_Documento'           => trim($datos[6]),
                'Fecha_de_Registro'          => parseDateOrExcelForDate($datos[7]), // Note el espacio en el nombre
                'Fecha_de_Pago'              => parseDateOrExcel($datos[8]),
                'Estado'                     => trim($datos[9]),
                'Valor_Bruto'                => $parseFloat($datos[10], $lineNumber, 'Valor_Bruto'),
                'Valor_Deducciones'          => $parseFloat($datos[11], $lineNumber, 'Valor_Deducciones'),
                'Valor_Neto'                 => $parseFloat($datos[12], $lineNumber, 'Valor_Neto'),
                'Tipo_Beneficiario'          => trim($datos[13]),
                'Vigencia_Presupuestal'      => trim($datos[14]),
                'Tipo_Identificacion'        => trim($datos[15]),
                'Identificacion'             => trim($datos[16]), 
                'Nombre_Razon_Social'        => trim($datos[17]),
                'Medio_de_Pago'              => trim($datos[18]),
                'Tipo_Cuenta'                => trim($datos[19]),
                'Numero_Cuenta'              => trim($datos[20]),
                'Estado_Cuenta'              => trim($datos[21]),
                'Entidad_Nit'                => trim($datos[22]),
                'Entidad_Descripcion'        => trim($datos[23]),
                'Dependencia'                => trim($datos[24]),
                'Dependencia_Descripcion'    => trim($datos[25]),
                'Rubro'                      => trim($datos[26]),
                'Descripcion'                => trim($datos[27]),
                'Fuente'                     => trim($datos[28]),
                'Recurso'                    => trim($datos[29]),
                'Sit'                        => trim($datos[30]),
                'Valor_Pesos'                => $parseFloat($datos[31], $lineNumber, 'Valor_Pesos'),
                'Valor_Moneda'               => $parseFloat($datos[32], $lineNumber, 'Valor_Moneda'),
                'Valor_Reintegrado_Pesos'    => $parseFloat($datos[33], $lineNumber, 'Valor_Reintegrado_Pesos'),
                'Valor_Reintegrado_Moneda'   => $parseFloat($datos[34], $lineNumber, 'Valor_Reintegrado_Moneda'),
                'Tesoreria_Pagadora'         => trim($datos[35]),
                'Identificacion_Pagaduria'   => trim($datos[36]),
                'Cuenta_Pagaduria'           => trim($datos[37]),
                'Endosada'                   => trim($datos[38]),
                'Tipo_Identificacion2'       => trim($datos[39]),
                'Identificacion3'            => trim($datos[40]),
                'Razon_social'               => trim($datos[41]),
                'Numero_Cuenta4'             => trim($datos[42]),
                'Concepto_Pago'              => trim($datos[43]),
                'Solicitud_CDP'              => trim($datos[44]),
                'CDP'                        => trim($datos[45]),
                'Compromisos'                => trim($datos[46]),
                'Cuentas_por_Pagar'          => trim($datos[47]),
                'Fecha_Cuentas_por_Pagar'    => parseDateOrExcelForDate($datos[48]),
                'Obligaciones'               => trim($datos[49]),
                'Ordenes_de_Pago'            => trim($datos[50]),
                'Reintegros'                 => $parseFloat($datos[51], $lineNumber, 'Reintegros'),
                'Fecha_Doc_Soporte_Compromiso' => parseDateOrExcelForDate($datos[52]),
                'Tipo_Doc_Soporte_Compromiso' => trim($datos[53]),
                'Num_Doc_Soporte_Compromiso'  => trim($datos[54]),
                'Objeto_del_Compromiso'       => trim($datos[55])
            ];
        } catch (Exception $e) {
            throw new Exception("Error en la línea $lineNumber: " . $e->getMessage());
        }
    }

    return $datosDepurados;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        if (!isset($_FILES['dataop'])) {
            throw new Exception('No se recibió el archivo dataop');
        }
        if ($_FILES['dataop']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error en la subida del archivo: ' . $_FILES['dataop']['error']);
        }

        // Validación mínima de MIME Type (ajusta según tu criterio)
        $mimeType = mime_content_type($_FILES['dataop']['tmp_name']);
        if (!in_array($mimeType, ['text/csv', 'text/plain', 'application/vnd.ms-excel'])) {
            throw new Exception('Tipo de archivo no válido. Debe ser un archivo CSV');
        }

        $admin = new admin3();
        $datosDepurados = depurar_datos_op($_FILES['dataop']);

        if (empty($datosDepurados)) {
            throw new Exception('No se encontraron datos válidos para procesar en el archivo OP');
        }

        $resultado = $admin->procesar_csv_op($datosDepurados);

        // Registrar la actualización
        $stmt = $conn->prepare("
            INSERT INTO registros_actualizaciones 
            (tipo_tabla, nombre_archivo, registros_actualizados, registros_nuevos, usuario_id)
            VALUES ('OP', ?, ?, ?, ?)
        ");
        
        $nombre_archivo = $_FILES['dataop']['name'];
        $stmt->bindParam(1, $nombre_archivo, PDO::PARAM_STR);
        $stmt->bindParam(2, $resultado['updated'], PDO::PARAM_INT);
        $stmt->bindParam(3, $resultado['inserted'], PDO::PARAM_INT);
        $stmt->bindParam(4, $usuario_id, PDO::PARAM_STR);
        $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'inserted' => $resultado['inserted'] ?? 0,
            'updated'  => $resultado['updated'] ?? 0,
            'errors'   => $resultado['errors'] ?? []
        ]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage(),
            'detail'  => 'Error procesando el archivo OP. Por favor, revise el archivo y vuelva a intentarlo.'
        ]);
    }
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido',
        'message' => 'Solo se permiten solicitudes POST'
    ]);
    exit;
}
