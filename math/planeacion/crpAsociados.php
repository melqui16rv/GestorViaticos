<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class planeacion1 extends Conexion {
    private $conexion;

    public function __construct() {
        parent::__construct();
        $this->conexion = $this->obtenerConexion();
    }

    public function obtenerCRPsAsociados($cdp) {
        $query = "SELECT 
            CODIGO_CRP, 
            CDP AS CODIGO_CDP, 
            Numero_Documento, 
            Fecha_de_Registro, 
            Estado, 
            COALESCE(Valor_Inicial, 0) AS Valor_Inicial, 
            COALESCE(Valor_Actual, 0) AS Valor_Actual, 
            COALESCE(Saldo_por_Utilizar, 0) AS Saldo_por_Utilizar, 
            Nombre_Razon_Social
        FROM crp 
        WHERE CDP = :cdp
        ORDER BY Fecha_de_Registro DESC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':cdp', $cdp, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTotalCRPs($cdp) {
        // Primero obtener los datos del CDP
        $queryCDP = "SELECT 
            Valor_Actual as valor_cdp_aprobado,
            Saldo_por_Comprometer as saldo_cdp
        FROM cdp 
        WHERE Numero_Documento = :cdp
        LIMIT 1";

        $stmtCDP = $this->conexion->prepare($queryCDP);
        $stmtCDP->bindParam(':cdp', $cdp, PDO::PARAM_STR);
        $stmtCDP->execute();
        $resultCDP = $stmtCDP->fetch(PDO::FETCH_ASSOC);

        // Luego obtener los totales de CRP
        $queryCRP = "SELECT 
            COUNT(*) as total,
            SUM(COALESCE(Valor_Actual, 0)) as total_valor_crp,
            SUM(COALESCE(Saldo_por_Utilizar, 0)) as saldo_crp
        FROM crp 
        WHERE CDP = :cdp";

        $stmtCRP = $this->conexion->prepare($queryCRP);
        $stmtCRP->bindParam(':cdp', $cdp, PDO::PARAM_STR);
        $stmtCRP->execute();
        $resultCRP = $stmtCRP->fetch(PDO::FETCH_ASSOC);

        // Combinar resultados y asegurar valores por defecto
        return [
            'total' => (int)($resultCRP['total'] ?? 0),
            'valor_cdp_aprobado' => (float)($resultCDP['valor_cdp_aprobado'] ?? 0),
            'saldo_cdp' => (float)($resultCDP['saldo_cdp'] ?? 0),
            'total_valor_crp' => (float)($resultCRP['total_valor_crp'] ?? 0),
            'saldo_crp' => (float)($resultCRP['saldo_crp'] ?? 0)
        ];
    }
}
?>