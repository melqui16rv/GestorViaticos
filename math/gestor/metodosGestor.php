<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class gestor extends Conexion {
    private $conexion;

    public function __construct() {
        parent::__construct(); // Llamar al constructor padre
        $this->conexion = $this->obtenerConexion(); // Usar el método heredado
    }

    public function obtenerSaldosAsignados($documento = '', $nombre = '', $cdp = '', $crp = '', $limit = 10, $offset = 0) {
        $sql = "SELECT sa.*, 
                       cdp.Numero_Documento AS Numero_Documento_CDP, 
                       crp.Numero_Documento AS Numero_Documento_CRP
                FROM saldos_asignados sa
                INNER JOIN cdp ON sa.cdp_id = cdp.cdp_id
                INNER JOIN crp ON sa.rp_id = crp.rp_id
                WHERE 1=1";
        $params = [];
    
        if (!empty($documento)) {
            $sql .= " AND sa.DOCUMENTO_PERSONA LIKE :documento";
            $params[':documento'] = "%$documento%";
        }
    
        if (!empty($nombre)) {
            $sql .= " AND sa.NOMBRE_PERSONA LIKE :nombre";
            $params[':nombre'] = "%$nombre%";
        }
    
        if (!empty($cdp)) {
            $sql .= " AND cdp.Numero_Documento LIKE :cdp";
            $params[':cdp'] = "%$cdp%";
        }
    
        if (!empty($crp)) {
            $sql .= " AND crp.Numero_Documento LIKE :crp";
            $params[':crp'] = "%$crp%";
        }
    
        $sql .= " ORDER BY sa.FECHA_REGISTRO DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conexion->prepare($sql);
    
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
    
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSaldosAsignadosConFechas($documento = '', $nombre = '', $cdp = '', $crp = '', $mes = '', $fechaInicio = '', $fechaFin = '', $limit = 10, $offset = 0) {
        $sql = "SELECT sa.*, 
                       cdp.Numero_Documento AS Numero_Documento_CDP, 
                       crp.Numero_Documento AS Numero_Documento_CRP
                FROM saldos_asignados sa
                INNER JOIN cdp ON sa.cdp_id = cdp.cdp_id
                INNER JOIN crp ON sa.rp_id = crp.rp_id
                WHERE 1=1";
        $params = [];

        if (!empty($documento)) {
            $sql .= " AND sa.DOCUMENTO_PERSONA LIKE :documento";
            $params[':documento'] = "%$documento%";
        }

        if (!empty($nombre)) {
            $sql .= " AND sa.NOMBRE_PERSONA LIKE :nombre";
            $params[':nombre'] = "%$nombre%";
        }

        if (!empty($cdp)) {
            $sql .= " AND cdp.Numero_Documento LIKE :cdp";
            $params[':cdp'] = "%$cdp%";
        }

        if (!empty($crp)) {
            $sql .= " AND crp.Numero_Documento LIKE :crp";
            $params[':crp'] = "%$crp%";
        }

        if (!empty($mes)) {
            $sql .= " AND MONTH(sa.FECHA_REGISTRO) = :mes";
            $params[':mes'] = $mes;
        }

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(sa.FECHA_REGISTRO) >= :fechaInicio";
            $params[':fechaInicio'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(sa.FECHA_REGISTRO) <= :fechaFin";
            $params[':fechaFin'] = $fechaFin;
        }

        $sql .= " ORDER BY sa.FECHA_REGISTRO DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conexion->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerSaldosAsignadosConFechas: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDetalleSaldo($idSaldo) {
        $sql = "SELECT sa.*, 
                       cdp.Numero_Documento AS Numero_Documento_CDP, 
                       crp.Numero_Documento AS Numero_Documento_CRP
                FROM saldos_asignados sa
                INNER JOIN cdp ON sa.cdp_id = cdp.cdp_id
                INNER JOIN crp ON sa.rp_id = crp.rp_id
                WHERE sa.ID_SALDO = :idSaldo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idSaldo', $idSaldo, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleSaldo: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerDetalleCDP($codigoCDP, $campos = '*') {
        $sql = "SELECT $campos FROM cdp WHERE CODIGO_CDP = :codigoCDP";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':codigoCDP', $codigoCDP, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleCDP: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerDetalleCRP($codigoCRP, $campos = '*') {
        $sql = "SELECT $campos FROM crp WHERE CODIGO_CRP = :codigoCRP";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':codigoCRP', $codigoCRP, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleCRP: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerDetalleCDPPorId($cdpId, $campos = '*') {
        $sql = "SELECT $campos FROM cdp WHERE cdp_id = :cdpId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':cdpId', $cdpId, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleCDPPorId: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerDetalleCRPPorId($rpId, $campos = '*') {
        $sql = "SELECT $campos FROM crp WHERE rp_id = :rpId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':rpId', $rpId, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalleCRPPorId: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerMesesDisponibles() {
        $sql = "SELECT DISTINCT MONTH(FECHA_REGISTRO) AS mes, 
                       MONTHNAME(FECHA_REGISTRO) AS nombre_mes 
                FROM saldos_asignados 
                ORDER BY mes";
        $stmt = $this->conexion->prepare($sql);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerMesesDisponibles: " . $e->getMessage());
            return [];
        }
    }

    // ================================
    //      MÉTODO PARA IMÁGENES
    // ================================
    public function obtenerImagenesDeSaldo($idSaldo) {
        $sql = "SELECT ID_IMAGEN, ID_SALDO, NOMBRE_ORIGINAL, RUTA_IMAGEN, FECHA_SUBIDA
                FROM imagenes_saldos_asignados
                WHERE ID_SALDO = :id_saldo";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_saldo', $idSaldo, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerImagenesDeSaldo: " . $e->getMessage());
            return [];
        }
    }
}
?>