<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class metas_tecnoparqueExt extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function obtenerProyectosTec() {
        $sql = "SELECT * FROM proyectos_tecnologicos";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerAsesorarAso() {

    }
    public function obtenerAsesorarApre() {

    }
    public function obtenerProyectosExt() {

    }
    public function obtenerVisitasApre() {
        $sql = "SELECT * FROM listadosvisitasApre";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    // uptade
    public function actualizarProyectosTec($id_PBT, $terminados, $en_proceso) {
        // La consulta debe usar id_PBT que es el nombre correcto de la columna
        $sql = "UPDATE proyectos_tecnologicos 
                SET terminados = :terminados, 
                    en_proceso = :en_proceso 
                WHERE id_PBT = :id_PBT";
                
        try {
            $stmt = $this->conexion->prepare($sql);
            
            // Vinculamos los parámetros con los valores recibidos
            $stmt->bindParam(':terminados', $terminados, PDO::PARAM_INT);
            $stmt->bindParam(':en_proceso', $en_proceso, PDO::PARAM_INT);
            $stmt->bindParam(':id_PBT', $id_PBT, PDO::PARAM_INT);
            
            $stmt->execute();
            
            // Retornamos true si se actualizó al menos una fila
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al actualizar proyecto tecnológico: " . $e->getMessage());
            return false;
        }
    }
    public function actualizarAsesorarAso() {

    }
    public function actualizarAsesorarApre() {

    }
    public function actualizarProyectosExt() {

    }
    public function actualizarVisitasApre($id_visita, $encargado, $numAsistentes, $fechaCharla) {
        $sql = "UPDATE listadosvisitasApre 
                SET encargado = :encargado, 
                    numAsistentes = :numAsistentes, 
                    fechaCharla = :fechaCharla 
                WHERE id_visita = :id_visita";
                
        try {
            $stmt = $this->conexion->prepare($sql);
            
            // Vinculamos los parámetros con los valores recibidos
            $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
            $stmt->bindParam(':numAsistentes', $numAsistentes, PDO::PARAM_INT);
            $stmt->bindParam(':fechaCharla', $fechaCharla, PDO::PARAM_STR);
            $stmt->bindParam(':id_visita', $id_visita, PDO::PARAM_INT);
            
            $stmt->execute();
            
            // Retornamos true si se actualizó al menos una fila
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al actualizar visita: " . $e->getMessage());
            return false;
        }

    }

    // insert
    public function insertarAsesorarAso() {

    }
    public function insertarAsesorarApre() {

    }
    public function insertarProyectosExt() {

    }
    public function insertarVisitasApre() {

    }

    // delete
    public function eliminarProyectosTec() {

    }
    public function eliminarAsesorarAso() {

    }
    public function eliminarAsesorarApre() {

    }
    public function eliminarProyectosExt() {

    }
    public function eliminarVisitasApre() {
        $sql = "DELETE FROM listadosvisitasApre WHERE id_visita = :id_visita";
        
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_visita', $id_visita, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al eliminar visita: " . $e->getMessage());
            return false;
        }

    }

    // metodos de visualizacion
    // ...
    // ....
    // .....
    public function obtenerSumaProyectosTecTerminados() {
        // Se obtiene la suma total de todos los proyectos en los estados terminados y en proceso y el total
        $sql = "SELECT 
            SUM(terminados) as total_terminados, 
            SUM(en_proceso) as total_en_proceso 
            FROM proyectos_tecnologicos";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $total_terminados = $result['total_terminados'] ?? 0;
        $total_en_proceso = $result['total_en_proceso'] ?? 0;
        $total = $total_terminados + $total_en_proceso;
        
        return array(
            'total_terminados' => $total_terminados, 
            'total_en_proceso' => $total_en_proceso, 
            'total' => $total
        );
    }
    public function obtenerSumaVisitasApre() {
        // Se obtiene la suma total de asistentes y el conteo de visitas
        $sql = "SELECT 
            SUM(numAsistentes) as total_numAsistentes,
            COUNT(*) as total_visitas
            FROM listadosvisitasApre";
        
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Usar el operador de fusión de null para valores predeterminados
            $total_numAsistentes = $result['total_numAsistentes'] ?? 0;
            $total_visitas = $result['total_visitas'] ?? 0;
            
            return array(
                'total_numAsistentes' => $total_numAsistentes,
                'total_visitas' => $total_visitas
            );
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al obtener suma de visitas: " . $e->getMessage());
            return array(
                'total_numAsistentes' => 0,
                'total_visitas' => 0
            );
        }
    }

    public function obtenerProyectosTecPorTipo($tipo = 'Extensionismo') {
        $sql = "SELECT * FROM proyectos_tecnoparque WHERE tipo = :tipo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSumaProyectosTecPorTipo($tipo = 'Extensionismo') {
        $sql = "SELECT 
            SUM(terminados) as total_terminados, 
            SUM(en_proceso) as total_en_proceso 
            FROM proyectos_tecnoparque
            WHERE tipo = :tipo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_terminados = $result['total_terminados'] ?? 0;
        $total_en_proceso = $result['total_en_proceso'] ?? 0;
        $total = $total_terminados + $total_en_proceso;
        return array(
            'total_terminados' => $total_terminados, 
            'total_en_proceso' => $total_en_proceso, 
            'total' => $total
        );
    }

    public function obtenerSumaProyectosTecTerminadosPorTipo($tipo = 'Extensionismo') {
        $sql = "SELECT SUM(terminados) as total_terminados FROM proyectos_tecnoparque WHERE tipo = :tipo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_terminados = $result['total_terminados'] ?? 0;
        return [
            'total_terminados' => $total_terminados,
            'meta' => 100,
            'avance_porcentaje' => $total_terminados > 0 ? round(($total_terminados / 100) * 100, 1) : 0
        ];
    }

    /**
     * Obtener un proyecto por su ID (para comparar antes de actualizar)
     */
    public function obtenerProyectoPorId($id_PBT) {
        $sql = "SELECT * FROM proyectos_tecnoparque WHERE id_PBT = :id_PBT";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_PBT', $id_PBT, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar todos los campos editables de un proyecto (incluyendo fecha_actualizacion)
     */
    public function actualizarProyectoTecCompleto($id_PBT, $terminados, $en_proceso, $fecha_actualizacion) {
        $sql = "UPDATE proyectos_tecnoparque 
                SET terminados = :terminados, 
                    en_proceso = :en_proceso, 
                    fecha_actualizacion = :fecha_actualizacion
                WHERE id_PBT = :id_PBT";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':terminados', $terminados, PDO::PARAM_INT);
        $stmt->bindParam(':en_proceso', $en_proceso, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_actualizacion', $fecha_actualizacion, PDO::PARAM_STR);
        $stmt->bindParam(':id_PBT', $id_PBT, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

}