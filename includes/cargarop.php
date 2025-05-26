<?php
function processCSV($filePath, $conn) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        // No saltar cabecera si no existe, pero si existe, saltar
        // fgetcsv($handle, 1000, ";");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $sql = "INSERT INTO op (
                op_id, rp_id, cdp_id, CODIGO_OP, CODIGO_CRP, CODIGO_CDP, Numero_Documento, Fecha_de_Registro, Fecha_de_Pago, Estado, Valor_Bruto, Valor_Deducciones, Valor_Neto, `Tipo Beneficiario`, `Vigencia Presupuestal`, Tipo_Identificacion, Identificacion, Nombre_Razon_Social, Medio_de_Pago, Tipo_Cuenta, Numero_Cuenta, Estado_Cuenta, Entidad_Nit, Entidad_Descripcion, Dependencia, Dependencia_Descripcion, Rubro, Descripcion, Fuente, Recurso, Sit, Valor_Pesos, Valor_Moneda, Valor_Reintegrado_Pesos, Valor_Reintegrado_Moneda, Tesoreria_Pagadora, Identificacion_Pagaduria, Cuenta_Pagaduria, Endosada, Tipo_Identificacion2, Identificacion3, Razon_social, Numero_Cuenta4, Concepto_Pago, Solicitud_CDP, CDP, Compromisos, Cuentas_por_Pagar, Fecha_Cuentas_por_Pagar, Obligaciones, Ordenes_de_Pago, Reintegros, Fecha_Doc_Soporte_Compromiso, Tipo_Doc_Soporte_Compromiso, Num_Doc_Soporte_Compromiso, Objeto_del_Compromiso
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
                rp_id = VALUES(rp_id),
                cdp_id = VALUES(cdp_id),
                CODIGO_OP = VALUES(CODIGO_OP),
                CODIGO_CRP = VALUES(CODIGO_CRP),
                CODIGO_CDP = VALUES(CODIGO_CDP),
                Numero_Documento = VALUES(Numero_Documento),
                Fecha_de_Registro = VALUES(Fecha_de_Registro),
                Fecha_de_Pago = VALUES(Fecha_de_Pago),
                Estado = VALUES(Estado),
                Valor_Bruto = VALUES(Valor_Bruto),
                Valor_Deducciones = VALUES(Valor_Deducciones),
                Valor_Neto = VALUES(Valor_Neto),
                `Tipo Beneficiario` = VALUES(`Tipo Beneficiario`),
                `Vigencia Presupuestal` = VALUES(`Vigencia Presupuestal`),
                Tipo_Identificacion = VALUES(Tipo_Identificacion),
                Identificacion = VALUES(Identificacion),
                Nombre_Razon_Social = VALUES(Nombre_Razon_Social),
                Medio_de_Pago = VALUES(Medio_de_Pago),
                Tipo_Cuenta = VALUES(Tipo_Cuenta),
                Numero_Cuenta = VALUES(Numero_Cuenta),
                Estado_Cuenta = VALUES(Estado_Cuenta),
                Entidad_Nit = VALUES(Entidad_Nit),
                Entidad_Descripcion = VALUES(Entidad_Descripcion),
                Dependencia = VALUES(Dependencia),
                Dependencia_Descripcion = VALUES(Dependencia_Descripcion),
                Rubro = VALUES(Rubro),
                Descripcion = VALUES(Descripcion),
                Fuente = VALUES(Fuente),
                Recurso = VALUES(Recurso),
                Sit = VALUES(Sit),
                Valor_Pesos = VALUES(Valor_Pesos),
                Valor_Moneda = VALUES(Valor_Moneda),
                Valor_Reintegrado_Pesos = VALUES(Valor_Reintegrado_Pesos),
                Valor_Reintegrado_Moneda = VALUES(Valor_Reintegrado_Moneda),
                Tesoreria_Pagadora = VALUES(Tesoreria_Pagadora),
                Identificacion_Pagaduria = VALUES(Identificacion_Pagaduria),
                Cuenta_Pagaduria = VALUES(Cuenta_Pagaduria),
                Endosada = VALUES(Endosada),
                Tipo_Identificacion2 = VALUES(Tipo_Identificacion2),
                Identificacion3 = VALUES(Identificacion3),
                Razon_social = VALUES(Razon_social),
                Numero_Cuenta4 = VALUES(Numero_Cuenta4),
                Concepto_Pago = VALUES(Concepto_Pago),
                Solicitud_CDP = VALUES(Solicitud_CDP),
                CDP = VALUES(CDP),
                Compromisos = VALUES(Compromisos),
                Cuentas_por_Pagar = VALUES(Cuentas_por_Pagar),
                Fecha_Cuentas_por_Pagar = VALUES(Fecha_Cuentas_por_Pagar),
                Obligaciones = VALUES(Obligaciones),
                Ordenes_de_Pago = VALUES(Ordenes_de_Pago),
                Reintegros = VALUES(Reintegros),
                Fecha_Doc_Soporte_Compromiso = VALUES(Fecha_Doc_Soporte_Compromiso),
                Tipo_Doc_Soporte_Compromiso = VALUES(Tipo_Doc_Soporte_Compromiso),
                Num_Doc_Soporte_Compromiso = VALUES(Num_Doc_Soporte_Compromiso),
                Objeto_del_Compromiso = VALUES(Objeto_del_Compromiso)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                str_repeat('s', 13) . str_repeat('d', 3) . str_repeat('s', 40),
                $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9],
                floatval($data[10]), floatval($data[11]), floatval($data[12]), $data[13], $data[14], $data[15], $data[16], $data[17], $data[18], $data[19], $data[20], $data[21], $data[22], $data[23], $data[24], $data[25], $data[26], $data[27], $data[28], $data[29], $data[30], floatval($data[31]), floatval($data[32]), floatval($data[33]), floatval($data[34]), $data[35], $data[36], $data[37], $data[38], $data[39], $data[40], $data[41], $data[42], $data[43], $data[44], $data[45], $data[46], $data[47], $data[48], $data[49], $data[50], $data[51], $data[52], $data[53], $data[54], $data[55]
            );
            $stmt->execute();
        }
        fclose($handle);
    }
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
