<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class admin extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Método para procesar CSV de CDP
    public function procesar_csv_cdp($datosDepurados) {
        $inserted = 0;
        $updated = 0;
        $errors = [];

        foreach ($datosDepurados as $dato) {
            try {
                $stmt = $this->conexion->prepare("
                    INSERT INTO cdp (
                        cdp_id, CODIGO_CDP, Numero_Documento, Fecha_de_Registro, Fecha_de_Creacion, Estado, Dependencia, Rubro, Fuente, Recurso, 
                        Valor_Inicial, Valor_Operaciones, Valor_Actual, Saldo_por_Comprometer, Objeto, Compromisos, Cuentas_por_Pagar, 
                        Obligaciones, Ordenes_de_Pago, Reintegros
                    ) VALUES (
                        :cdp_id, :CODIGO_CDP, :Numero_Documento, :Fecha_de_Registro, :Fecha_de_Creacion, :Estado, :Dependencia, :Rubro, :Fuente, :Recurso, 
                        :Valor_Inicial, :Valor_Operaciones, :Valor_Actual, :Saldo_por_Comprometer, :Objeto, :Compromisos, :Cuentas_por_Pagar, 
                        :Obligaciones, :Ordenes_de_Pago, :Reintegros
                    )
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
                        Reintegros = VALUES(Reintegros)
                ");

                $stmt->execute($dato);
                if ($stmt->rowCount() == 1) {
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

    // Métodos vacíos para procesar CSV de CRP y OP
    public function procesar_csv_crp($archivo) {
        // Aquí puedes agregar la lógica para procesar los CRP
    }

    public function procesar_csv_op($archivo) {
        // Aquí puedes agregar la lógica para procesar los OP
    }

    /**
     * Inserta o actualiza los datos en la tabla 'cdp'
     * 
     * @param array $cdpData Datos a insertar o actualizar
     * @return array ['inserted' => X, 'updated' => Y]
     */
    private function insertarCDP($cdpData) {
        $inserted = 0;
        $updated = 0;

        foreach ($cdpData as $cdp) {
            if ($cdp['tipo_modificacion'] === 'actualización') {
                // Actualización
                $stmt = $this->conexion->prepare("
                    UPDATE cdp SET 
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
                    WHERE CODIGO_CDP = :CODIGO_CDP
                ");
                $updated += $stmt->execute($cdp) ? 1 : 0;
            } else {
                // Inserción
                $stmt = $this->conexion->prepare("
                INSERT INTO cdp (
                    CODIGO_CDP, Numero_Documento, Fecha_de_Registro, Fecha_de_Creacion, Estado, 
                    Dependencia, Rubro, Fuente, Recurso, Valor_Inicial, Valor_Operaciones, 
                    Valor_Actual, Saldo_por_Comprometer, Objeto, Compromisos, Cuentas_por_Pagar, 
                    Obligaciones, Ordenes_de_Pago, Reintegros
                ) VALUES (
                    :CODIGO_CDP, :Numero_Documento, :Fecha_de_Registro, :Fecha_de_Creacion, :Estado, 
                    :Dependencia, :Rubro, :Fuente, :Recurso, :Valor_Inicial, :Valor_Operaciones, 
                    :Valor_Actual, :Saldo_por_Comprometer, :Objeto, :Compromisos, :Cuentas_por_Pagar, 
                    :Obligaciones, :Ordenes_de_Pago, :Reintegros
                )
            ");
            
                $inserted += $stmt->execute($cdp) ? 1 : 0;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }
}
