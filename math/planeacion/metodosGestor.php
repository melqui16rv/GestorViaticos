<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class planeacion extends Conexion {
    private $conexion;

    public function __construct() {
        parent::__construct(); // Llamar al constructor padre
        $this->conexion = $this->obtenerConexion(); // Usar el método heredado
    }

    public function obtenerCDP($numeroDocumento = '', $fuente = '', $reintegros = '', $limit = 10, $offset = 0) {
        $query = "SELECT 
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

        // Filtro por Número de Documento (búsqueda parcial)
        if (!empty($numeroDocumento)) {
            $query .= " AND Numero_Documento LIKE :numeroDocumento";
            $params[':numeroDocumento'] = "%" . $numeroDocumento . "%";
        }

        // Filtro por Fuente (si no se selecciona "Todos")
        if (!empty($fuente) && $fuente != "Todos") {
            $query .= " AND Fuente = :fuente";
            $params[':fuente'] = $fuente;
        }

        // Filtro por Reintegros
        if (!empty($reintegros) && $reintegros != "Todos") {
            if ($reintegros == "Con reintegro") {
                $query .= " AND Reintegros <> 0";
            } elseif ($reintegros == "Sin reintegro") {
                $query .= " AND (Reintegros = 0 OR Reintegros IS NULL)";
            }
        }

        // Ordenar por Fecha_de_Registro de forma descendente y aplicar paginación
        $query .= " ORDER BY Fecha_de_Registro DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->conexion->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta");
        }

        // Vincular parámetros
        foreach ($params as $param => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar registro de depuración
        error_log(print_r($result, true));

        return $result;
    }
    public function obtenerOP($filtros = [], $limit = 10, $offset = 0) {
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
                  op.Objeto_del_Compromiso
                  FROM op 
                  WHERE op.Objeto_del_Compromiso LIKE '%VIATICOS%'";
        
        $params = [];

        // Filtro por número de documento
        if (!empty($filtros['numeroDocumento'])) {
            $query .= " AND op.Numero_Documento LIKE :numeroDocumento";
            $params[':numeroDocumento'] = "%" . $filtros['numeroDocumento'] . "%";
        }

        // Filtro por estado
        if (!empty($filtros['estado']) && $filtros['estado'] != "Todos") {
            $query .= " AND op.Estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        // Filtro por beneficiario
        if (!empty($filtros['beneficiario'])) {
            $query .= " AND op.Nombre_Razon_Social LIKE :beneficiario";
            $params[':beneficiario'] = "%" . $filtros['beneficiario'] . "%";
        }

        // Filtro por mes
        if (!empty($filtros['mes'])) {
            $query .= " AND MONTH(op.Fecha_de_Registro) = :mes";
            $params[':mes'] = $filtros['mes'];
        }

        // Filtro por rango de fechas
        if (!empty($filtros['fechaInicio']) && !empty($filtros['fechaFin'])) {
            $query .= " AND op.Fecha_de_Registro BETWEEN :fechaInicio AND :fechaFin";
            $params[':fechaInicio'] = $filtros['fechaInicio'];
            $params[':fechaFin'] = $filtros['fechaFin'];
        }

        $query .= " ORDER BY op.Fecha_de_Registro DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->conexion->prepare($query);
        
        foreach ($params as $param => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>