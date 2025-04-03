<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class gestor1 extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function insertarSaldoAsignado($nombre, $documento, $fechaInicio, $fechaFin, $fechaPago, $saldoAsignado, $codigoCDP, $codigoCRP) {
        try {
            $sql = "INSERT INTO saldos_asignados (
                        NOMBRE_PERSONA,
                        DOCUMENTO_PERSONA,
                        FECHA_INICIO,
                        FECHA_FIN,
                        FECHA_PAGO,
                        SALDO_ASIGNADO,
                        CODIGO_CDP,
                        CODIGO_CRP
                    ) VALUES (
                        :nombre,
                        :documento,
                        :fecha_inicio,
                        :fecha_fin,
                        :fecha_pago,
                        :saldo_asignado,
                        :codigo_cdp,
                        :codigo_crp
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':documento', $documento);
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
            $stmt->bindParam(':fecha_pago', $fechaPago);
            $stmt->bindParam(':saldo_asignado', $saldoAsignado);
            $stmt->bindParam(':codigo_cdp', $codigoCDP);
            $stmt->bindParam(':codigo_crp', $codigoCRP);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al insertar saldo asignado: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerCDPsViaticos() {
        try {
            $sql = "SELECT DISTINCT c.* 
                    FROM cdp c
                    INNER JOIN crp r ON c.CODIGO_CDP = r.CODIGO_CDP 
                    WHERE c.Objeto LIKE '%VIATICOS%'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener CDPs con VIATICOS: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCRPsPorCDP($codigoCDP) {
        try {
            $codigoCDP = trim($codigoCDP);
            $sql = "SELECT * FROM crp 
                    WHERE TRIM(CODIGO_CDP) = :codigo_cdp 
                    AND Saldo_por_Utilizar IS NOT NULL 
                    AND Saldo_por_Utilizar > 0";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo_cdp', $codigoCDP, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($resultados)) {
                error_log("No se encontraron CRPs con saldo disponible para el CDP: '$codigoCDP'");
            }
            return $resultados;
        } catch (PDOException $e) {
            error_log("Error al obtener CRPs por CDP ('$codigoCDP'): " . $e->getMessage());
            return [];
        }
    }
}