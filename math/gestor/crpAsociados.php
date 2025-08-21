<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class gestor1 extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    /**
     * Inserta un saldo asignado en la tabla saldos_asignados y retorna el ID generado
     * en caso de éxito (o false si falla).
     */
    public function insertarSaldoAsignado($nombre, $documento, $fechaInicio, $fechaFin, $fechaPago, $saldoAsignado, $codigoCDP, $codigoCRP) {
        try {
            $sql = "INSERT INTO saldos_asignados (
                        NOMBRE_PERSONA,
                        DOCUMENTO_PERSONA,
                        FECHA_INICIO,
                        FECHA_FIN,
                        FECHA_PAGO,
                        SALDO_ASIGNADO,
                        cdp_id,
                        rp_id
                    ) VALUES (
                        :nombre,
                        :documento,
                        :fecha_inicio,
                        :fecha_fin,
                        :fecha_pago,
                        :saldo_asignado,
                        :cdp_id,
                        :rp_id
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':documento', $documento);
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
            $stmt->bindParam(':fecha_pago', $fechaPago);
            $stmt->bindParam(':saldo_asignado', $saldoAsignado);
            $stmt->bindParam(':cdp_id', $codigoCDP);
            $stmt->bindParam(':rp_id', $codigoCRP);

            if ($stmt->execute()) {
                // Retornar el ID autogenerado
                return $this->conexion->lastInsertId();
            } else {
                return false;
            }

        } catch (PDOException $e) {
            error_log("Error al insertar saldo asignado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Inserta el registro de la imagen asociada a un saldo en la tabla imagenes_saldos_asignados.
     */
    public function insertarImagenSaldoAsignado($idSaldo, $nombreOriginal, $rutaImagen) {
        try {
            $sql = "INSERT INTO imagenes_saldos_asignados (
                        ID_SALDO,
                        NOMBRE_ORIGINAL,
                        RUTA_IMAGEN
                    ) VALUES (
                        :id_saldo,
                        :nombre_original,
                        :ruta_imagen
                    )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_saldo', $idSaldo, \PDO::PARAM_INT);
            $stmt->bindParam(':nombre_original', $nombreOriginal);
            $stmt->bindParam(':ruta_imagen', $rutaImagen);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al insertar imagen de saldo: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerCDPsViaticos() {
        try {
            $sql = "SELECT DISTINCT c.* 
                    FROM crp r
                    INNER JOIN cdp c ON r.CODIGO_CDP = c.CODIGO_CDP 
                    WHERE c.Objeto LIKE '%VIATICOS%'
                    AND r.Saldo_por_Utilizar > 0";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener CDPs con VIATICOS: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCRPsPorCDP($codigoCDP) {
        try {
            $codigoCDP = trim($codigoCDP);
            
            $sql = "SELECT r.* FROM crp r 
                    WHERE TRIM(r.CODIGO_CDP) = :codigo_cdp 
                    AND r.Saldo_por_Utilizar > 0";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo_cdp', $codigoCDP, \PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerCRPsPorCDP para CDP '$codigoCDP': " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCdpIdPorCodigo($codigoCDP) {
        try {
            $sql = "SELECT cdp_id FROM cdp WHERE CODIGO_CDP = :codigo_cdp LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo_cdp', $codigoCDP, \PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['cdp_id'] : null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerCdpIdPorCodigo: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerRpIdPorCodigo($codigoCRP) {
        try {
            $sql = "SELECT rp_id FROM crp WHERE CODIGO_CRP = :codigo_crp LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo_crp', $codigoCRP, \PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['rp_id'] : null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerRpIdPorCodigo: " . $e->getMessage());
            return null;
        }
    }
}