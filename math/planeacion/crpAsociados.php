<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class planeacion1 extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }
    public function obtenerCRPsAsociados($codigoCDP) {
        try {
            $query = "SELECT CODIGO_CRP, CDP as CODIGO_CDP, Numero_Documento, 
                            Fecha_de_Registro, Estado, 
                            COALESCE(Valor_Inicial, 0) as Valor_Inicial, 
                            COALESCE(Valor_Actual, 0) as Valor_Actual, 
                            COALESCE(Saldo_por_Utilizar, 0) as Saldo_por_Utilizar, 
                            Nombre_Razon_Social
                     FROM crp 
                     WHERE CDP = :codigoCDP";
            
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':codigoCDP', $codigoCDP, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCRPsAsociados: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTotalCRPs($codigoCDP) {
        try {
            // Primero obtenemos los datos del CDP
            $queryCDP = "SELECT Valor_Actual, Saldo_por_Comprometer 
                        FROM cdp 
                        WHERE Numero_Documento = :codigoCDP";
            
            $stmtCDP = $this->conexion->prepare($queryCDP);
            $stmtCDP->bindParam(':codigoCDP', $codigoCDP, PDO::PARAM_STR);
            $stmtCDP->execute();
            $datosCDP = $stmtCDP->fetch(PDO::FETCH_ASSOC);

            // Luego obtenemos los totales de CRP
            $queryCRP = "SELECT 
                        COUNT(*) as total,
                        COALESCE(SUM(Valor_Inicial), 0) as total_valor_crp,
                        COALESCE(SUM(Saldo_por_Utilizar), 0) as saldo_crp
                     FROM crp 
                     WHERE CDP = :codigoCDP";
            
            $stmtCRP = $this->conexion->prepare($queryCRP);
            $stmtCRP->bindParam(':codigoCDP', $codigoCDP, PDO::PARAM_STR);
            $stmtCRP->execute();
            $datosCRP = $stmtCRP->fetch(PDO::FETCH_ASSOC);

            // Combinamos los resultados
            return [
                'total' => $datosCRP['total'],
                'valor_cdp_aprobado' => $datosCDP['Valor_Actual'] ?? 0,
                'total_valor_crp' => $datosCRP['total_valor_crp'],
                'saldo_cdp' => $datosCDP['Saldo_por_Comprometer'] ?? 0,
                'saldo_crp' => $datosCRP['saldo_crp']
            ];
            
        } catch (PDOException $e) {
            error_log("Error en obtenerTotalCRPs: " . $e->getMessage());
            return [
                'total' => 0,
                'valor_cdp_aprobado' => 0,
                'total_valor_crp' => 0,
                'saldo_cdp' => 0,
                'saldo_crp' => 0
            ];
        }
    }
}
?>