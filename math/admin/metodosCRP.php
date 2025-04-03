<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class admin2 extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Método para procesar CSV de crp
    public function procesar_csv_crp($datosDepurados) {
        $inserted = 0;
        $updated = 0;
        $errors = [];

        foreach ($datosDepurados as $dato) {
            try {
                $stmt = $this->conexion->prepare("
                    INSERT INTO crp (
                        CODIGO_CRP,
                        CODIGO_CDP,
                        Numero_Documento,
                        Fecha_de_Registro,
                        Fecha_de_Creacion,
                        Estado,
                        Dependencia,
                        Rubro,
                        Descripcion,
                        Fuente,
                        Valor_Inicial,
                        Valor_Operaciones,
                        Valor_Actual,
                        Saldo_por_Utilizar,
                        Tipo_Identificacion,
                        Identificacion,
                        Nombre_Razon_Social,
                        Medio_de_Pago,
                        Tipo_Cuenta,
                        Numero_Cuenta,
                        Estado_Cuenta,
                        Entidad_Nit,
                        Entidad_Descripcion,
                        Solicitud_CDP,
                        CDP,
                        Compromisos,
                        Cuentas_por_Pagar,
                        Obligaciones,
                        Ordenes_de_Pago,
                        Reintegros,
                        Fecha_Documento_Soporte,
                        Tipo_Documento_Soporte,
                        Numero_Documento_Soporte,
                        Observaciones
                    ) VALUES (
                        :CODIGO_CRP,
                        :CODIGO_CDP,
                        :Numero_Documento,
                        :Fecha_de_Registro,
                        :Fecha_de_Creacion,
                        :Estado,
                        :Dependencia,
                        :Rubro,
                        :Descripcion,
                        :Fuente,
                        :Valor_Inicial,
                        :Valor_Operaciones,
                        :Valor_Actual,
                        :Saldo_por_Utilizar,
                        :Tipo_Identificacion,
                        :Identificacion,
                        :Nombre_Razon_Social,
                        :Medio_de_Pago,
                        :Tipo_Cuenta,
                        :Numero_Cuenta,
                        :Estado_Cuenta,
                        :Entidad_Nit,
                        :Entidad_Descripcion,
                        :Solicitud_CDP,
                        :CDP,
                        :Compromisos,
                        :Cuentas_por_Pagar,
                        :Obligaciones,
                        :Ordenes_de_Pago,
                        :Reintegros,
                        :Fecha_Documento_Soporte,
                        :Tipo_Documento_Soporte,
                        :Numero_Documento_Soporte,
                        :Observaciones
                    )
                    ON DUPLICATE KEY UPDATE
                        CODIGO_CDP              = VALUES(CODIGO_CDP),
                        Numero_Documento        = VALUES(Numero_Documento),
                        Fecha_de_Registro       = VALUES(Fecha_de_Registro),
                        Fecha_de_Creacion       = VALUES(Fecha_de_Creacion),
                        Estado                  = VALUES(Estado),
                        Dependencia            = VALUES(Dependencia),
                        Rubro                   = VALUES(Rubro),
                        Descripcion            = VALUES(Descripcion),
                        Fuente                  = VALUES(Fuente),
                        Valor_Inicial           = VALUES(Valor_Inicial),
                        Valor_Operaciones       = VALUES(Valor_Operaciones),
                        Valor_Actual            = VALUES(Valor_Actual),
                        Saldo_por_Utilizar      = VALUES(Saldo_por_Utilizar),
                        Tipo_Identificacion     = VALUES(Tipo_Identificacion),
                        Identificacion          = VALUES(Identificacion),
                        Nombre_Razon_Social     = VALUES(Nombre_Razon_Social),
                        Medio_de_Pago           = VALUES(Medio_de_Pago),
                        Tipo_Cuenta             = VALUES(Tipo_Cuenta),
                        Numero_Cuenta           = VALUES(Numero_Cuenta),
                        Estado_Cuenta           = VALUES(Estado_Cuenta),
                        Entidad_Nit             = VALUES(Entidad_Nit),
                        Entidad_Descripcion     = VALUES(Entidad_Descripcion),
                        Solicitud_CDP           = VALUES(Solicitud_CDP),
                        CDP                     = VALUES(CDP),
                        Compromisos             = VALUES(Compromisos),
                        Cuentas_por_Pagar       = VALUES(Cuentas_por_Pagar),
                        Obligaciones            = VALUES(Obligaciones),
                        Ordenes_de_Pago         = VALUES(Ordenes_de_Pago),
                        Reintegros              = VALUES(Reintegros),
                        Fecha_Documento_Soporte = VALUES(Fecha_Documento_Soporte),
                        Tipo_Documento_Soporte  = VALUES(Tipo_Documento_Soporte),
                        Numero_Documento_Soporte= VALUES(Numero_Documento_Soporte),
                        Observaciones           = VALUES(Observaciones)
                ");

                $stmt->execute($dato);

                // rowCount() en INSERT ... ON DUPLICATE KEY UPDATE
                // puede devolver:
                //  - 1 si insertó un registro nuevo
                //  - 2 si actualizó
                //  (algunos drivers de PDO pueden comportarse distinto).
                if ($stmt->rowCount() === 1) {
                    $inserted++;
                } else {
                    $updated++;
                }
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'errors' => $errors
        ];
    }
    /**
     * Inserta o actualiza los datos en la tabla 'crp'
     * 
     * @param array $crpData Datos a insertar o actualizar
     * @return array ['inserted' => X, 'updated' => Y]
     */
    private function insertarcrp($crpData) {
        $inserted = 0;
        $updated = 0;

        foreach ($crpData as $crp) {
            if ($crp['tipo_modificacion'] === 'actualización') {
                // Actualización
                $stmt = $this->conexion->prepare("
                    UPDATE crp SET 
                        Numero_Documento = :Numero_Documento,
                        Fecha_de_Registro = :Fecha_de_Registro,
                        Fecha_de_Creacion = :Fecha_de_Creacion,
                        Estado = :Estado,
                        Dependencia = :Dependencia,
                        Rubro = :Rubro,
                        Fuente = :Fuente,
                        Recurso = :Recurso,
                        Valor_Inicial = :Valor_Inicial,
                        Valor_Operaciones = :Valor_Operaciones,
                        Valor_Actual = :Valor_Actual,
                        Saldo_por_Comprometer = :Saldo_por_Comprometer,
                        Objeto = :Objeto,
                        Compromisos = :Compromisos,
                        Cuentas_por_Pagar = :Cuentas_por_Pagar,
                        Obligaciones = :Obligaciones,
                        Ordenes_de_Pago = :Ordenes_de_Pago,
                        Reintegros = :Reintegros
                    WHERE CODIGO_crp = :CODIGO_crp
                ");
                $updated += $stmt->execute($crp) ? 1 : 0;
            } else {
                // Inserción
                $stmt = $this->conexion->prepare("
                INSERT INTO crp (
                    CODIGO_crp, Numero_Documento, Fecha_de_Registro, Fecha_de_Creacion, Estado, 
                    Dependencia, Rubro, Fuente, Recurso, Valor_Inicial, Valor_Operaciones, 
                    Valor_Actual, Saldo_por_Comprometer, Objeto, Compromisos, Cuentas_por_Pagar, 
                    Obligaciones, Ordenes_de_Pago, Reintegros
                ) VALUES (
                    :CODIGO_crp, :Numero_Documento, :Fecha_de_Registro, :Fecha_de_Creacion, :Estado, 
                    :Dependencia, :Rubro, :Fuente, :Recurso, :Valor_Inicial, :Valor_Operaciones, 
                    :Valor_Actual, :Saldo_por_Comprometer, :Objeto, :Compromisos, :Cuentas_por_Pagar, 
                    :Obligaciones, :Ordenes_de_Pago, :Reintegros
                )
            ");
            
                $inserted += $stmt->execute($crp) ? 1 : 0;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }
}
