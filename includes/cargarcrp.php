<?php
function processCSV($filePath, $conn) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        // No saltar cabecera si no existe, pero si existe, saltar
        // fgetcsv($handle, 1000, ";");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $sql = "INSERT INTO crp (
                rp_id, cdp_id, CODIGO_CRP, CODIGO_CDP, Numero_Documento, Fecha_de_Registro, Fecha_de_Creacion, Estado, Dependencia, Rubro, Descripcion, Fuente, Valor_Inicial, Valor_Operaciones, Valor_Actual, Saldo_por_Utilizar, Tipo_Identificacion, Identificacion, Nombre_Razon_Social, Medio_de_Pago, Tipo_Cuenta, Numero_Cuenta, Estado_Cuenta, Entidad_Nit, Entidad_Descripcion, Solicitud_CDP, CDP, Compromisos, Cuentas_por_Pagar, Obligaciones, Ordenes_de_Pago, Reintegros, Fecha_Documento_Soporte, Tipo_Documento_Soporte, Numero_Documento_Soporte, Observaciones
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                cdp_id = VALUES(cdp_id),
                CODIGO_CRP = VALUES(CODIGO_CRP),
                CODIGO_CDP = VALUES(CODIGO_CDP),
                Numero_Documento = VALUES(Numero_Documento),
                Fecha_de_Registro = VALUES(Fecha_de_Registro),
                Fecha_de_Creacion = VALUES(Fecha_de_Creacion),
                Estado = VALUES(Estado),
                Dependencia = VALUES(Dependencia),
                Rubro = VALUES(Rubro),
                Descripcion = VALUES(Descripcion),
                Fuente = VALUES(Fuente),
                Valor_Inicial = VALUES(Valor_Inicial),
                Valor_Operaciones = VALUES(Valor_Operaciones),
                Valor_Actual = VALUES(Valor_Actual),
                Saldo_por_Utilizar = VALUES(Saldo_por_Utilizar),
                Tipo_Identificacion = VALUES(Tipo_Identificacion),
                Identificacion = VALUES(Identificacion),
                Nombre_Razon_Social = VALUES(Nombre_Razon_Social),
                Medio_de_Pago = VALUES(Medio_de_Pago),
                Tipo_Cuenta = VALUES(Tipo_Cuenta),
                Numero_Cuenta = VALUES(Numero_Cuenta),
                Estado_Cuenta = VALUES(Estado_Cuenta),
                Entidad_Nit = VALUES(Entidad_Nit),
                Entidad_Descripcion = VALUES(Entidad_Descripcion),
                Solicitud_CDP = VALUES(Solicitud_CDP),
                CDP = VALUES(CDP),
                Compromisos = VALUES(Compromisos),
                Cuentas_por_Pagar = VALUES(Cuentas_por_Pagar),
                Obligaciones = VALUES(Obligaciones),
                Ordenes_de_Pago = VALUES(Ordenes_de_Pago),
                Reintegros = VALUES(Reintegros),
                Fecha_Documento_Soporte = VALUES(Fecha_Documento_Soporte),
                Tipo_Documento_Soporte = VALUES(Tipo_Documento_Soporte),
                Numero_Documento_Soporte = VALUES(Numero_Documento_Soporte),
                Observaciones = VALUES(Observaciones)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssssssd...", // Ajustar el tipo de datos según corresponda
                $data[0], $data[1], $data[2], $data[3], $data[4],
                parseDateOrExcel($data[5]), parseDateOrExcel($data[6]), $data[7], $data[8], $data[9],
                $data[10], $data[11], floatval(limpiarValorNumerico($data[12])), floatval(limpiarValorNumerico($data[13])), floatval(limpiarValorNumerico($data[14])), floatval(limpiarValorNumerico($data[15])),
                $data[16], $data[17], $data[18], $data[19], $data[20], $data[21], $data[22], $data[23], $data[24], $data[25], $data[26], $data[27], $data[28], $data[29], $data[30],
                isset($data[31]) ? floatval(limpiarValorNumerico($data[31])) : null,
                isset($data[32]) ? parseDateOrExcel($data[32]) : null,
                isset($data[33]) ? $data[33] : null,
                isset($data[34]) ? $data[34] : null,
                isset($data[35]) ? $data[35] : null
            );
            $stmt->execute();
        }
        fclose($handle);
    }
}

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