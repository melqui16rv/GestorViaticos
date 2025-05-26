<?php
function processCSV($filePath, $conn) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        // No saltar cabecera si no existe, pero si existe, saltar
        // fgetcsv($handle, 1000, ";");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $sql = "INSERT INTO cdp (
                cdp_id, CODIGO_CDP, Numero_Documento, Fecha_de_Registro, Fecha_de_Creacion, Estado, Dependencia, Rubro, Fuente, Recurso, Valor_Inicial, Valor_Operaciones, Valor_Actual, Saldo_por_Comprometer, Objeto, Compromisos, Cuentas_por_Pagar, Obligaciones, Ordenes_de_Pago, Reintegros
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                CODIGO_CDP = VALUES(CODIGO_CDP),
                Numero_Documento = VALUES(Numero_Documento),
                Fecha_de_Registro = VALUES(Fecha_de_Registro),
                Fecha_de_Creacion = VALUES(Fecha_de_Creacion),
                Estado = VALUES(Estado),
                Dependencia = VALUES(Dependencia),
                Rubro = VALUES(Rubro),
                Fuente = VALUES(Fuente),
                Recurso = VALUES(Recurso),
                Valor_Inicial = VALUES(Valor_Inicial),
                Valor_Operaciones = VALUES(Valor_Operaciones),
                Valor_Actual = VALUES(Valor_Actual),
                Saldo_por_Comprometer = VALUES(Saldo_por_Comprometer),
                Objeto = VALUES(Objeto),
                Compromisos = VALUES(Compromisos),
                Cuentas_por_Pagar = VALUES(Cuentas_por_Pagar),
                Obligaciones = VALUES(Obligaciones),
                Ordenes_de_Pago = VALUES(Ordenes_de_Pago),
                Reintegros = VALUES(Reintegros)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssssddddsssssd",
                $data[0], $data[1], $data[2], parseDateOrExcel($data[3]), parseDateOrExcel($data[4]),
                $data[5], $data[6], $data[7], $data[8], $data[9],
                floatval(limpiarValorNumerico($data[10])), floatval(limpiarValorNumerico($data[11])), floatval(limpiarValorNumerico($data[12])), floatval(limpiarValorNumerico($data[13])),
                $data[14], $data[15], $data[16], $data[17], $data[18],
                isset($data[19]) ? floatval(limpiarValorNumerico($data[19])) : null
            );
            $stmt->execute();
        }
        fclose($handle);
    }
}

function limpiarValorNumerico($valor) {
    // Reemplazar puntos por nada y comas por puntos para decimales
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    // Eliminar todos los caracteres excepto números y puntos
    $valor = preg_replace('/[^0-9.]/', '', $valor);
    return $valor;
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
?>

<script>
async function uploadCSV(file) {
    const formData = new FormData();
    formData.append("file", file);

    let response = await fetch("upload.php", {
        method: "POST",
        body: formData
    });

    let result = await response.json();
    alert(result.message);
}
</script>
