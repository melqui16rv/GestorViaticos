<?php
function processCSV($filePath, $conn) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";"); // Saltar la primera línea (cabeceras)
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $sql = "INSERT INTO cdp_data (CODIGO_CDP, Numero_Documento, Fecha_de_Registro, Fecha_de_Creacion, Estado, Dependencia, Rubro, Fuente, Recurso, Valor_Inicial, Valor_Operaciones, Valor_Actual, Saldo_por_Comprometer, Objeto, Compromisos, Cuentas_por_Pagar, Obligaciones, Ordenes_de_Pago, Reintegros) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sississsssddddsiiii",
                $data[0], intval($data[1]), parseDateOrExcel($data[2]), parseDateOrExcel($data[3]), 
                $data[4], intval($data[5]), $data[6], $data[7], $data[8], 
                floatval(limpiarValorNumerico($data[9])), floatval(limpiarValorNumerico($data[10])), 
                floatval(limpiarValorNumerico($data[11])), floatval(limpiarValorNumerico($data[12])), 
                $data[13], intval($data[14]), intval($data[15]), intval($data[16]), 
                intval($data[17]), floatval(limpiarValorNumerico($data[18]))
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
