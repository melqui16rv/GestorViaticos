<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

require_once __DIR__ . '/../../sql/conexion.php';


class consultaObejtos extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function obtenerObjetos() {
        $sql = "SELECT 
                    CODIGO_CDP,
                    SUM(Valor_Actual) AS Valor_Actual_Sum,
                    CASE
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'INSTRUCTOR.%' THEN 'INSTRUCTOR'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'MATERIALES FORMACION.%' THEN 'MATERIALES FORMACION'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'MATERIALES FORMACIÓN.%' THEN 'MATERIALES FORMACION'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'Contratar mediante el mecanismo de monto agotable la compra de materiales e insumos%' THEN 'MATERIALES FORMACION'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'VIATICOS.%' THEN 'VIATICOS'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'DOTACION.%' THEN 'DOTACION'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'SERVICIOS PERSONALES.%' THEN 'SERVICIOS PERSONALES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'MONITORES.%' THEN 'MONITORES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'SERVICIOS PUBLICOS.%' THEN 'SERVICIOS PUBLICOS'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'IMPUESTOS.%' THEN 'IMPUESTOS'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'PAPELERIA.%' THEN 'PAPELERIA'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'OTROS MATERIALES Y SUMINISTROS.%' THEN 'OTROS MATERIALES Y SUMINISTROS'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'PROTECCION APRENDICES.%' THEN 'PROTECCION APRENDICES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'Contratar mediante el mecanismo de monto agotable la compra de elementos de protección personal%' THEN 'PROTECCION APRENDICES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'NOMINA.%' THEN 'NOMINA'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'BIENESTAR APRENDICES.%' THEN 'BIENESTAR APRENDICES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'APOYO APRENDICES.%' THEN 'APOYO APRENDICES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'MANTENIMIENTO BIENES.%' OR REPLACE(Objeto, '\"', '') LIKE ': MANTENIMIENTO BIENES.%' THEN 'MANTENIMIENTO BIENES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'MOBILIARIO.%' THEN 'MOBILIARIO'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'OTROS EQUIPOS.%' THEN 'OTROS EQUIPOS'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'OTROS EQUIPOS. Contratar la compra de mobiliario%' THEN 'OTROS EQUIPOS'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'ECCL SERVICIOS PERSONALES.%' THEN 'ECCL SERVICIOS PERSONALES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'VIATICOS FORMACION.%' THEN 'VIATICOS FORMACION'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'ADECUACIONES.%' THEN 'ADECUACIONES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'ADECUACIONES. Realizar adecuaciones integrales%' THEN 'ADECUACIONES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'ADECUACIONES. Realizar la interventoría%' THEN 'ADECUACIONES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'ADECUACIONES. Realizar la interventoría técnica%' THEN 'ADECUACIONES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'MANTENIMIENTO INMUEBLES.%' THEN 'MANTENIMIENTO INMUEBLES'
                        WHEN REPLACE(Objeto, '\"', '') LIKE 'BIENESTAR EMPLEADOS.%' THEN 'BIENESTAR EMPLEADOS'
                        ELSE 'OTRO'
                    END AS Clasificacion_Objeto
                FROM cdp
                GROUP BY CODIGO_CDP, Clasificacion_Objeto;";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }


}