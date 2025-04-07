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
                    FROM cdp c
                    INNER JOIN crp r ON c.CODIGO_CDP = r.CODIGO_CDP 
                    WHERE c.Objeto LIKE '%VIATICOS%'
                    AND c.Estado = 'Con Compromiso'";
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
            
            // Consulta modificada para ver todos los CRPs relacionados
            $sql = "SELECT r.* FROM crp r 
                    WHERE TRIM(r.CODIGO_CDP) = :codigo_cdp";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo_cdp', $codigoCDP, \PDO::PARAM_STR);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Log para depuración
            error_log("CDP consultado: '$codigoCDP'");
            error_log("Número de CRPs encontrados: " . count($resultados));
            
            if (empty($resultados)) {
                error_log("No se encontraron CRPs para el CDP: '$codigoCDP'");
                // Verificar directamente en la base de datos
                $sqlVerificacion = "SELECT COUNT(*) as total FROM crp WHERE TRIM(CODIGO_CDP) = :codigo_cdp";
                $stmtVerificacion = $this->conexion->prepare($sqlVerificacion);
                $stmtVerificacion->bindParam(':codigo_cdp', $codigoCDP, \PDO::PARAM_STR);
                $stmtVerificacion->execute();
                $totalReal = $stmtVerificacion->fetch(\PDO::FETCH_ASSOC)['total'];
                error_log("Total de CRPs en base de datos para este CDP: $totalReal");
            }
            
            return $resultados;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerCRPsPorCDP para CDP '$codigoCDP': " . $e->getMessage());
            return [];
        }
    }
}