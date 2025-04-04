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
        $query = "SELECT 
            COUNT(*) as total,
            (SELECT Valor_Actual FROM cdp WHERE Numero_Documento = :cdp) as valor_cdp_aprobado,
            (SELECT Saldo_por_Comprometer FROM cdp WHERE Numero_Documento = :cdp) as saldo_cdp,
            SUM(COALESCE(Valor_Actual, 0)) as total_valor_crp,
            SUM(COALESCE(Saldo_por_Utilizar, 0)) as saldo_crp
        FROM crp 
        WHERE CDP = :cdp";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':cdp', $cdp, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no hay resultados, establecer valores predeterminados
        if (!$result) {
            return [
                'total' => 0,
                'valor_cdp_aprobado' => 0,
                'saldo_cdp' => 0,
                'total_valor_crp' => 0,
                'saldo_crp' => 0
            ];
        }

        // Asegurarse de que los valores numéricos sean 0 si son NULL
        return [
            'total' => (int)$result['total'],
            'valor_cdp_aprobado' => (float)$result['valor_cdp_aprobado'] ?: 0,
            'saldo_cdp' => (float)$result['saldo_cdp'] ?: 0,
            'total_valor_crp' => (float)$result['total_valor_crp'] ?: 0,
            'saldo_crp' => (float)$result['saldo_crp'] ?: 0
        ];
    }
}
?>