<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class sennova_general_presuspuestal extends Conexion {
    private $conexion;

    public function __construct() {
        parent::__construct(); // Llamar al constructor padre
        $this->conexion = $this->obtenerConexion(); // Usar el método heredado
    }

    public function obtenerCDP($numeroDocumento = '', $fuente = '', $reintegros = '', $limit = 10, $offset = 0) {
        // Construir la consulta base
        $baseQuery = "SELECT 
                      Numero_Documento, 
                      Fecha_de_Registro, 
                      IFNULL(Fecha_de_Creacion, '') AS Fecha_de_Creacion, 
                      IFNULL(Estado, '') AS Estado, 
                      IFNULL(Dependencia, '') AS Dependencia, 
                      IFNULL(Fuente, '') AS Fuente, 
                      IFNULL(Valor_Actual, 0) AS Valor_Actual, 
                      IFNULL(Saldo_por_Comprometer, 0) AS Saldo_por_Comprometer, 
                      IFNULL(Reintegros, 0) AS Reintegros
                      FROM cdp
                      WHERE 1";
        
        $params = [];

        // Aplicar filtros
        if (!empty($numeroDocumento)) {
            $baseQuery .= " AND Numero_Documento LIKE :numeroDocumento";
            $params[':numeroDocumento'] = "%" . $numeroDocumento . "%";
        }

        if (!empty($fuente) && $fuente != "Todos") {
            $baseQuery .= " AND Fuente = :fuente";
            $params[':fuente'] = $fuente;
        }

        if (!empty($reintegros) && $reintegros != "Todos") {
            if ($reintegros == "Con reintegro") {
                $baseQuery .= " AND Reintegros <> 0";
            } elseif ($reintegros == "Sin reintegro") {
                $baseQuery .= " AND (Reintegros = 0 OR Reintegros IS NULL)";
            }
        }

        // Ordenar por fecha de registro
        $baseQuery .= " ORDER BY Fecha_de_Registro DESC";

        // Si limit es 'todos', obtener el total de registros
        if ($limit === 999999) {
            $countQuery = "SELECT COUNT(*) FROM cdp WHERE 1" . substr($baseQuery, strpos($baseQuery, "WHERE 1") + 7);
            $stmtCount = $this->conexion->prepare($countQuery);
            foreach ($params as $param => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmtCount->bindValue($param, $value, $type);
            }
            $stmtCount->execute();
            $totalRegistros = $stmtCount->fetchColumn();
            $limit = $totalRegistros; // Establecer límite al total de registros
        }

        // Agregar LIMIT y OFFSET a la consulta principal
        $baseQuery .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->conexion->prepare($baseQuery);
        
        // Vincular parámetros
        foreach ($params as $param => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método adicional para obtener el total de registros
    public function obtenerTotalCDP($numeroDocumento = '', $fuente = '', $reintegros = '') {
        $query = "SELECT COUNT(*) FROM cdp WHERE 1";
        $params = [];

        if (!empty($numeroDocumento)) {
            $query .= " AND Numero_Documento LIKE :numeroDocumento";
            $params[':numeroDocumento'] = "%" . $numeroDocumento . "%";
        }

        if (!empty($fuente) && $fuente != "Todos") {
            $query .= " AND Fuente = :fuente";
            $params[':fuente'] = $fuente;
        }

        if (!empty($reintegros) && $reintegros != "Todos") {
            if ($reintegros == "Con reintegro") {
                $query .= " AND Reintegros <> 0";
            } elseif ($reintegros == "Sin reintegro") {
                $query .= " AND (Reintegros = 0 OR Reintegros IS NULL)";
            }
        }

        $stmt = $this->conexion->prepare($query);
        foreach ($params as $param => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerOP($filtros = [], $limit = 10, $offset = 0) {
        if (!isset($_SESSION)) session_start();
        $rol = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
        // Definir dependencias permitidas según el rol
        if ($rol == '5') {
            $dependenciasPermitidas = ['69'];
        } elseif ($rol == '6') {
            $dependenciasPermitidas = ['70'];
        } else {
            $dependenciasPermitidas = ['62', '66', '69', '70'];
        }
        $query = "SELECT 
                  op.Numero_Documento,
                  op.Fecha_de_Registro,
                  op.Fecha_de_Pago,
                  op.Estado,
                  op.Nombre_Razon_Social,
                  op.Valor_Bruto,
                  op.Valor_Neto,
                  op.Estado_Cuenta,
                  op.Medio_de_Pago,
                  op.CDP,
                  op.CODIGO_CRP,
                  op.Objeto_del_Compromiso,
                  op.Dependencia
                  FROM op 
                  WHERE op.Objeto_del_Compromiso LIKE '%VIATICOS%'";

        $params = [];

        // Filtro por dependencias permitidas
        $query .= " AND (";
        $depConds = [];
        foreach ($dependenciasPermitidas as $dep) {
            $depConds[] = "op.Dependencia LIKE ?";
            $params[] = "%$dep";
        }
        $query .= implode(' OR ', $depConds) . ")";

        // Filtro por número de documento
        if (!empty($filtros['numeroDocumento'])) {
            $query .= " AND op.Numero_Documento LIKE ?";
            $params[] = "%" . $filtros['numeroDocumento'] . "%";
        }

        // Filtro por estado
        if (!empty($filtros['estado']) && $filtros['estado'] != "Todos") {
            $query .= " AND op.Estado = ?";
            $params[] = $filtros['estado'];
        }

        // Filtro por beneficiario
        if (!empty($filtros['beneficiario'])) {
            $query .= " AND op.Nombre_Razon_Social LIKE ?";
            $params[] = "%" . $filtros['beneficiario'] . "%";
        }

        // Filtro por mes
        if (!empty($filtros['mes'])) {
            $query .= " AND MONTH(op.Fecha_de_Registro) = ?";
            $params[] = $filtros['mes'];
        }

        // Filtro por rango de fechas
        if (!empty($filtros['fechaInicio']) && !empty($filtros['fechaFin'])) {
            $query .= " AND op.Fecha_de_Registro BETWEEN ? AND ?";
            $params[] = $filtros['fechaInicio'];
            $params[] = $filtros['fechaFin'];
        }

        $query .= " ORDER BY op.Fecha_de_Registro DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->conexion->prepare($query);
        foreach ($params as $idx => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($idx + 1, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>