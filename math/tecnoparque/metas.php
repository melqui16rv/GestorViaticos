<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

require_once __DIR__ . '/../../sql/conexion.php';

class metas_tecnoparque extends Conexion{
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
    public function obtenerVisitasApre($filtros = []) {
        try {
            // Cambia el SELECT para incluir 'nodo'
            $sql = "SELECT id_visita, nodo, encargado, numAsistentes, fechaCharla FROM listadosvisitasApre WHERE 1=1";
            $params = [];

            // Debug
            error_log("Construyendo consulta SQL con filtros: " . print_r($filtros, true));

            // Filtro por encargado
            if (!empty($filtros['encargado'])) {
                $sql .= " AND encargado = :encargado";
                $params[':encargado'] = $filtros['encargado'];
            }

            // Filtro por mes y año
            if (!empty($filtros['mes']) && !empty($filtros['anio'])) {
                $sql .= " AND MONTH(fechaCharla) = :mes AND YEAR(fechaCharla) = :anio";
                $params[':mes'] = $filtros['mes'];
                $params[':anio'] = $filtros['anio'];
            }

            // Ordenamiento
            $sql .= " ORDER BY fechaCharla " . 
                    (isset($filtros['orden']) && $filtros['orden'] === 'ASC' ? 'ASC' : 'DESC');

            // Límite
            if (!empty($filtros['limite'])) {
                $sql .= " LIMIT :limite";
            }

            error_log("SQL Query: " . $sql);
            error_log("Params: " . print_r($params, true));

            $stmt = $this->conexion->prepare($sql);

            // Bind parameters
            foreach ($params as $param => $value) {
                if ($param === ':mes' || $param === ':anio' || $param === ':limite') {
                    $stmt->bindValue($param, (int)$value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $value, PDO::PARAM_STR);
                }
            }

            // Bind limite separately (si no se incluyó en el foreach)
            if (!empty($filtros['limite']) && !isset($params[':limite'])) {
                $stmt->bindValue(':limite', (int)$filtros['limite'], PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Resultados obtenidos: " . count($result));
            return $result;

        } catch (PDOException $e) {
            error_log("Error en obtenerVisitasApre: " . $e->getMessage());
            throw new Exception("Error al obtener las visitas: " . $e->getMessage());
        }
    }

    public function obtenerEncargadosUnicos() {
        $sql = "SELECT DISTINCT encargado FROM listadosvisitasApre ORDER BY encargado";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
    public function insertarVisitasApre($encargado, $numAsistentes, $fechaCharla) {
        $sql = "INSERT INTO listadosvisitasApre (encargado, numAsistentes, fechaCharla) 
                VALUES (:encargado, :numAsistentes, :fechaCharla)";
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
            $stmt->bindParam(':numAsistentes', $numAsistentes, PDO::PARAM_INT);
            $stmt->bindParam(':fechaCharla', $fechaCharla, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar visita: " . $e->getMessage());
            return false;
        }
    }

    // delete
    public function eliminarVisitasApre($id_visita) {
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

    public function obtenerProyectosTecPorTipo($tipo = 'Tecnológico') {
        $sql = "SELECT * FROM proyectos_tecnoparque WHERE tipo = :tipo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSumaProyectosTecPorTipo($tipo = 'Tecnológico') {
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

    public function obtenerSumaProyectosTecTerminadosPorTipo($tipo = 'Tecnológico') {
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

    public function obtenerIndicadoresVisitas() {
        $sql = "SELECT 
                    COUNT(*) AS total_charlas, 
                    SUM(numAsistentes) AS total_asistentes, 
                    AVG(numAsistentes) AS promedio_asistentes 
                FROM listadosvisitasApre";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlEncargados = "SELECT encargado, SUM(numAsistentes) AS total_asistentes 
                          FROM listadosvisitasApre 
                          GROUP BY encargado";
        $stmtEncargados = $this->conexion->prepare($sqlEncargados);
        $stmtEncargados->execute();
        $encargadosData = $stmtEncargados->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total_charlas' => $result['total_charlas'] ?? 0,
            'total_asistentes' => $result['total_asistentes'] ?? 0,
            // Se elimina el decimal convirtiendo el promedio a entero
            'promedio_asistentes' => round($result['promedio_asistentes'] ?? 0),
            'encargados' => array_column($encargadosData, 'encargado'),
            'asistentes_por_encargado' => array_column($encargadosData, 'total_asistentes')
        ];
    }

    public function obtenerIndicadoresVisitasFiltradas($visitas) {
        $total_charlas = count($visitas);
        $total_asistentes = 0;
        $asistentes_por_encargado = [];
        
        foreach ($visitas as $visita) {
            $total_asistentes += $visita['numAsistentes'];
            if (!isset($asistentes_por_encargado[$visita['encargado']])) {
                $asistentes_por_encargado[$visita['encargado']] = 0;
            }
            $asistentes_por_encargado[$visita['encargado']] += $visita['numAsistentes'];
        }
        
        $promedio_asistentes = $total_charlas > 0 ? round($total_asistentes / $total_charlas) : 0;
        
        return [
            'total_charlas' => $total_charlas,
            'total_asistentes' => $total_asistentes,
            'promedio_asistentes' => $promedio_asistentes,
            'encargados' => array_keys($asistentes_por_encargado),
            'asistentes_por_encargado' => array_values($asistentes_por_encargado)
        ];
    }

    public function obtenerMesesUnicos() {
        $sql = "SELECT DISTINCT 
                MONTH(fechaCharla) as mes, 
                YEAR(fechaCharla) as anio 
                FROM listadosvisitasApre 
                ORDER BY anio DESC, mes DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- CRUD para asesoramiento ---
    public function obtenerAsesoramientos($filtros = []) {
        $sql = "SELECT * FROM asesoramiento WHERE 1=1";
        $params = [];
        if (!empty($filtros['tipo'])) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['encargado'])) {
            $sql .= " AND encargadoAsesoramiento = :encargado";
            $params[':encargado'] = $filtros['encargado'];
        }
        $sql .= " ORDER BY fechaAsesoramiento DESC";
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarAsesoramiento($tipo, $encargado, $entidad, $fecha) {
        $sql = "INSERT INTO asesoramiento (tipo, encargadoAsesoramiento, nombreEntidadImpacto, fechaAsesoramiento)
                VALUES (:tipo, :encargado, :entidad, :fecha)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarAsesoramiento($id, $tipo, $encargado, $entidad, $fecha) {
        $sql = "UPDATE asesoramiento SET tipo = :tipo, encargadoAsesoramiento = :encargado, nombreEntidadImpacto = :entidad, fechaAsesoramiento = :fecha WHERE id_asesoramiendo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarAsesoramiento($id) {
        $sql = "DELETE FROM asesoramiento WHERE id_asesoramiendo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerIndicadoresAsesoramiento() {
        $sql = "SELECT tipo, COUNT(*) as cantidad FROM asesoramiento GROUP BY tipo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $tipos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $sql2 = "SELECT encargadoAsesoramiento, COUNT(*) as cantidad FROM asesoramiento GROUP BY encargadoAsesoramiento";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute();
        $encargados = $stmt2->fetchAll(PDO::FETCH_KEY_PAIR);

        $sql3 = "SELECT COUNT(*) as total FROM asesoramiento";
        $stmt3 = $this->conexion->prepare($sql3);
        $stmt3->execute();
        $total = $stmt3->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return [
            'por_tipo' => $tipos,
            'por_encargado' => $encargados,
            'total' => $total
        ];
    }

    // --- Métodos para asesorar_aprendiz y asesorar_asociado ---
    // Obtener asesoramientos a aprendices
    public function obtenerAsesorarApre($filtros = []) {
        $sql = "SELECT * FROM asesorar_aprendiz WHERE 1=1";
        $params = [];
        if (!empty($filtros['encargado'])) {
            $sql .= " AND encargado = :encargado";
            $params[':encargado'] = $filtros['encargado'];
        }
        if (!empty($filtros['mes']) && !empty($filtros['anio'])) {
            $sql .= " AND MONTH(fecha) = :mes AND YEAR(fecha) = :anio";
            $params[':mes'] = $filtros['mes'];
            $params[':anio'] = $filtros['anio'];
        }
        $sql .= " ORDER BY fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $k => $v) {
            if ($k === ':mes' || $k === ':anio') {
                $stmt->bindValue($k, (int)$v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener asesoramientos a asociados
    public function obtenerAsesorarAso($filtros = []) {
        $sql = "SELECT * FROM asesorar_asociado WHERE 1=1";
        $params = [];
        if (!empty($filtros['encargado'])) {
            $sql .= " AND encargado = :encargado";
            $params[':encargado'] = $filtros['encargado'];
        }
        if (!empty($filtros['mes']) && !empty($filtros['anio'])) {
            $sql .= " AND MONTH(fecha) = :mes AND YEAR(fecha) = :anio";
            $params[':mes'] = $filtros['mes'];
            $params[':anio'] = $filtros['anio'];
        }
        $sql .= " ORDER BY fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $k => $v) {
            if ($k === ':mes' || $k === ':anio') {
                $stmt->bindValue($k, (int)$v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Métodos de inserción para asesorar_aprendiz y asesorar_asociado
    public function insertarAsesorarApre($encargado, $entidad, $fecha) {
        $sql = "INSERT INTO asesorar_aprendiz (encargado, entidad, fecha) VALUES (:encargado, :entidad, :fecha)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function insertarAsesorarAso($encargado, $entidad, $fecha) {
        $sql = "INSERT INTO asesorar_asociado (encargado, entidad, fecha) VALUES (:encargado, :entidad, :fecha)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Métodos de actualización para asesorar_aprendiz y asesorar_asociado
    public function actualizarAsesorarApre($id, $encargado, $entidad, $fecha) {
        $sql = "UPDATE asesorar_aprendiz SET encargado = :encargado, entidad = :entidad, fecha = :fecha WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function actualizarAsesorarAso($id, $encargado, $entidad, $fecha) {
        $sql = "UPDATE asesorar_asociado SET encargado = :encargado, entidad = :entidad, fecha = :fecha WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':entidad', $entidad, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Métodos de eliminación para asesorar_aprendiz y asesorar_asociado
    public function eliminarAsesorarApre($id) {
        $sql = "DELETE FROM asesorar_aprendiz WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function eliminarAsesorarAso($id) {
        $sql = "DELETE FROM asesorar_asociado WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Métodos para proyectos externos (proyectos_ext)
    public function obtenerProyectosExt($filtros = []) {
        $sql = "SELECT * FROM proyectos_ext WHERE 1=1";
        $params = [];
        if (!empty($filtros['encargado'])) {
            $sql .= " AND encargado = :encargado";
            $params[':encargado'] = $filtros['encargado'];
        }
        $sql .= " ORDER BY fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function insertarProyectosExt($encargado, $nombre, $fecha) {
        $sql = "INSERT INTO proyectos_ext (encargado, nombre, fecha) VALUES (:encargado, :nombre, :fecha)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function actualizarProyectosExt($id, $encargado, $nombre, $fecha) {
        $sql = "UPDATE proyectos_ext SET encargado = :encargado, nombre = :nombre, fecha = :fecha WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':encargado', $encargado, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function eliminarProyectosExt($id) {
        $sql = "DELETE FROM proyectos_ext WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}