<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class admin3 extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function procesar_csv_op($datosDepurados) {
        $inserted = 0;
        $updated  = 0;
        $errors   = [];

        $sql = "
            INSERT INTO op (
                CODIGO_OP,
                CODIGO_CRP,
                CODIGO_CDP,
                Numero_Documento,
                Fecha_de_Registro,
                Fecha_de_Pago,
                Estado,
                Valor_Bruto,
                Valor_Deducciones,
                Valor_Neto,
                `Tipo Beneficiario`,
                `Vigencia Presupuestal`,
                Tipo_Identificacion,
                Identificacion,
                Nombre_Razon_Social,
                Medio_de_Pago,
                Tipo_Cuenta,
                Numero_Cuenta,
                Estado_Cuenta,
                Entidad_Nit,
                Entidad_Descripcion,
                Dependencia,
                Dependencia_Descripcion,
                Rubro,
                Descripcion,
                Fuente,
                Recurso,
                Sit,
                Valor_Pesos,
                Valor_Moneda,
                Valor_Reintegrado_Pesos,
                Valor_Reintegrado_Moneda,
                Tesoreria_Pagadora,
                Identificacion_Pagaduria,
                Cuenta_Pagaduria,
                Endosada,
                Tipo_Identificacion2,
                Identificacion3,
                Razon_social,
                Numero_Cuenta4,
                Concepto_Pago,
                Solicitud_CDP,
                CDP,
                Compromisos,
                Cuentas_por_Pagar,
                Fecha_Cuentas_por_Pagar,
                Obligaciones,
                Ordenes_de_Pago,
                Reintegros,
                Fecha_Doc_Soporte_Compromiso,
                Tipo_Doc_Soporte_Compromiso,
                Num_Doc_Soporte_Compromiso,
                Objeto_del_Compromiso
            ) VALUES (
                :CODIGO_OP,
                :CODIGO_CRP,
                :CODIGO_CDP,
                :Numero_Documento,
                :Fecha_de_Registro,
                :Fecha_de_Pago,
                :Estado,
                :Valor_Bruto,
                :Valor_Deducciones,
                :Valor_Neto,
                :Tipo_Beneficiario,
                :Vigencia_Presupuestal,
                :Tipo_Identificacion,
                :Identificacion,
                :Nombre_Razon_Social,
                :Medio_de_Pago,
                :Tipo_Cuenta,
                :Numero_Cuenta,
                :Estado_Cuenta,
                :Entidad_Nit,
                :Entidad_Descripcion,
                :Dependencia,
                :Dependencia_Descripcion,
                :Rubro,
                :Descripcion,
                :Fuente,
                :Recurso,
                :Sit,
                :Valor_Pesos,
                :Valor_Moneda,
                :Valor_Reintegrado_Pesos,
                :Valor_Reintegrado_Moneda,
                :Tesoreria_Pagadora,
                :Identificacion_Pagaduria,
                :Cuenta_Pagaduria,
                :Endosada,
                :Tipo_Identificacion2,
                :Identificacion3,
                :Razon_social,
                :Numero_Cuenta4,
                :Concepto_Pago,
                :Solicitud_CDP,
                :CDP,
                :Compromisos,
                :Cuentas_por_Pagar,
                :Fecha_Cuentas_por_Pagar,
                :Obligaciones,
                :Ordenes_de_Pago,
                :Reintegros,
                :Fecha_Doc_Soporte_Compromiso,
                :Tipo_Doc_Soporte_Compromiso,
                :Num_Doc_Soporte_Compromiso,
                :Objeto_del_Compromiso
            ) ON DUPLICATE KEY UPDATE
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

        $stmt = $this->conexion->prepare($sql);

        foreach ($datosDepurados as $dato) {
            try {
                $stmt->execute($dato);
                $rowCount = $stmt->rowCount();

                if ($rowCount === 1) {
                    $inserted++;
                } elseif ($rowCount >= 2) {
                    $updated++;
                } else {
                    $updated++;
                }
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return [
            'inserted' => $inserted,
            'updated'  => $updated,
            'errors'   => $errors
        ];
    }
}
