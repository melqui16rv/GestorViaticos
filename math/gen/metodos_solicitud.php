<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';


class solicitudRol extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Obtener datos del usuario logueado
    public function obtenerDatosUsuarioLogueado($numero_documento) {
        $sql = "SELECT * FROM usuario WHERE numero_documento = :id";
        $consult = $this->conexion->prepare($sql);
        $consult->bindParam(":id", $numero_documento);
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener roles disponibles para solicitud (excepto el 7)
    public function obtenerRolesDisponibles() {
        $sql = "SELECT id_rol, nombre_rol FROM roles_app WHERE id_rol != '7'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar solicitud de rol
    public function enviar_solicitud($numero_documento, $email, $id_rol_solicitado, $motivo, $fecha_solicitud) {
        $sql = "INSERT INTO solicitudes_rol (numero_documento, email, id_rol_solicitado, motivo, fecha_solicitud, estado) VALUES (:num_doc, :email, :rol, :motivo, :fecha, 'enviada')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':num_doc', $numero_documento);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $id_rol_solicitado);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':fecha', $fecha_solicitud);
        return $stmt->execute();
    }

    // Obtener solicitudes de rol por usuario
    public function obtenerSolicitudesPorUsuario($numero_documento) {
        $sql = "SELECT s.*, r.nombre_rol as rol_nombre FROM solicitudes_rol s LEFT JOIN roles_app r ON s.id_rol_solicitado = r.id_rol WHERE s.numero_documento = :num_doc ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':num_doc', $numero_documento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar solicitud de rol por id y usuario
    public function eliminarSolicitud($id_solicitud, $numero_documento) {
        $sql = "DELETE FROM solicitudes_rol WHERE id_solicitud = :id AND numero_documento = :num_doc AND estado = 'enviada'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_solicitud);
        $stmt->bindParam(':num_doc', $numero_documento);
        return $stmt->execute();
    }

    // Obtener una solicitud específica para edición
    public function obtenerSolicitudPorId($id_solicitud, $numero_documento) {
        $sql = "SELECT * FROM solicitudes_rol WHERE id_solicitud = :id AND numero_documento = :num_doc AND estado = 'enviada'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_solicitud);
        $stmt->bindParam(':num_doc', $numero_documento);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar solicitud de rol (solo si está en estado enviada)
    public function editarSolicitud($id_solicitud, $numero_documento, $id_rol_solicitado, $motivo, $otro_rol = null) {
        $sql = "UPDATE solicitudes_rol SET id_rol_solicitado = :rol, motivo = :motivo WHERE id_solicitud = :id AND numero_documento = :num_doc AND estado = 'enviada'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':rol', $id_rol_solicitado);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':id', $id_solicitud);
        $stmt->bindParam(':num_doc', $numero_documento);
        return $stmt->execute();
    }

    public function responder_solicitud() {
        // Se implementará en la parte de admin
    }

    // ==================== MÉTODOS PARA ADMINISTRADOR ====================
    
    // Obtener todas las solicitudes con filtros opcionales
    public function obtenerTodasSolicitudes($estado = null, $fecha_inicio = null, $fecha_fin = null) {
        $sql = "SELECT s.*, r.nombre_rol as rol_nombre, u.nombre_completo, u.email as email_usuario 
                FROM solicitudes_rol s 
                LEFT JOIN roles_app r ON s.id_rol_solicitado = r.id_rol 
                LEFT JOIN usuario u ON s.numero_documento = u.numero_documento 
                WHERE 1=1";
        
        $params = [];
        
        if ($estado) {
            $sql .= " AND s.estado = :estado";
            $params[':estado'] = $estado;
        }
        
        if ($fecha_inicio) {
            $sql .= " AND DATE(s.fecha_solicitud) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }
        
        if ($fecha_fin) {
            $sql .= " AND DATE(s.fecha_solicitud) <= :fecha_fin";
            $params[':fecha_fin'] = $fecha_fin;
        }
        
        $sql .= " ORDER BY s.fecha_solicitud DESC";
        
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener solo solicitudes enviadas (pendientes de respuesta)
    public function obtenerSolicitudesEnviadas() {
        return $this->obtenerTodasSolicitudes('enviada');
    }

    // Aceptar solicitud de rol
    public function aceptarSolicitud($id_solicitud, $admin_numero_documento, $observaciones_admin = '') {
        try {
            $this->conexion->beginTransaction();
            
            // Obtener datos de la solicitud
            $sqlSolicitud = "SELECT * FROM solicitudes_rol WHERE id_solicitud = :id";
            $stmt = $this->conexion->prepare($sqlSolicitud);
            $stmt->bindParam(':id', $id_solicitud);
            $stmt->execute();
            $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$solicitud || $solicitud['estado'] !== 'enviada') {
                $this->conexion->rollBack();
                return false;
            }
            
            // Actualizar el rol del usuario
            $sqlUpdateUsuario = "UPDATE usuario SET id_rol = :nuevo_rol WHERE numero_documento = :num_doc";
            $stmt = $this->conexion->prepare($sqlUpdateUsuario);
            $stmt->bindParam(':nuevo_rol', $solicitud['id_rol_solicitado']);
            $stmt->bindParam(':num_doc', $solicitud['numero_documento']);
            
            if (!$stmt->execute()) {
                $this->conexion->rollBack();
                return false;
            }
            
            // Actualizar estado de la solicitud
            $sqlUpdateSolicitud = "UPDATE solicitudes_rol SET 
                                   estado = 'aceptada', 
                                   fecha_respuesta = NOW(), 
                                   admin_respuesta = :admin_doc,
                                   observaciones_admin = :observaciones
                                   WHERE id_solicitud = :id";
            $stmt = $this->conexion->prepare($sqlUpdateSolicitud);
            $stmt->bindParam(':admin_doc', $admin_numero_documento);
            $stmt->bindParam(':observaciones', $observaciones_admin);
            $stmt->bindParam(':id', $id_solicitud);
            
            if ($stmt->execute()) {
                $this->conexion->commit();
                return true;
            } else {
                $this->conexion->rollBack();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conexion->rollBack();
            return false;
        }
    }

    // Rechazar solicitud de rol
    public function rechazarSolicitud($id_solicitud, $admin_numero_documento, $observaciones_admin = '') {
        $sql = "UPDATE solicitudes_rol SET 
                estado = 'rechazada', 
                fecha_respuesta = NOW(), 
                admin_respuesta = :admin_doc,
                observaciones_admin = :observaciones
                WHERE id_solicitud = :id AND estado = 'enviada'";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':admin_doc', $admin_numero_documento);
        $stmt->bindParam(':observaciones', $observaciones_admin);
        $stmt->bindParam(':id', $id_solicitud);
        
        return $stmt->execute();
    }

    // Obtener estadísticas de solicitudes
    public function obtenerEstadisticasSolicitudes() {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as total,
                    DATE(fecha_solicitud) as fecha
                FROM solicitudes_rol 
                GROUP BY estado, DATE(fecha_solicitud)
                ORDER BY fecha DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener resumen de solicitudes por estado
    public function obtenerResumenSolicitudes() {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as total
                FROM solicitudes_rol 
                GROUP BY estado";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener solicitud específica con detalles completos
    public function obtenerSolicitudCompleta($id_solicitud) {
        $sql = "SELECT s.*, 
                       r.nombre_rol as rol_nombre, 
                       u.nombre_completo, 
                       u.email as email_usuario,
                       u.telefono,
                       admin.nombre_completo as admin_nombre
                FROM solicitudes_rol s 
                LEFT JOIN roles_app r ON s.id_rol_solicitado = r.id_rol 
                LEFT JOIN usuario u ON s.numero_documento = u.numero_documento 
                LEFT JOIN usuario admin ON s.admin_respuesta = admin.numero_documento
                WHERE s.id_solicitud = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_solicitud);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si el usuario es administrador
    public function esAdministrador($numero_documento) {
        $sql = "SELECT id_rol FROM usuario WHERE numero_documento = :num_doc";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':num_doc', $numero_documento);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado && in_array($resultado['id_rol'], ['1', '7']); // Admin o Acceso
    }

    // Verificar si el usuario tiene su perfil completo
    public function esPerfilCompleto($numero_documento) {
        $sql = "SELECT numero_documento, tipo_doc, telefono FROM usuario WHERE numero_documento = :num_doc";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':num_doc', $numero_documento);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            return false;
        }
        
        // Verificar que los campos requeridos estén completos
        return !empty($usuario['numero_documento']) && 
               !empty($usuario['tipo_doc']) && 
               !empty($usuario['telefono']);
    }

    // Verificar si el usuario tiene solicitudes pendientes
    public function tieneSolicitudPendiente($numero_documento) {
        $sql = "SELECT COUNT(*) FROM solicitudes_rol WHERE numero_documento = :num_doc AND estado = 'enviada'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':num_doc', $numero_documento);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Actualizar perfil del usuario
    public function actualizarPerfilUsuario($numero_documento, $tipo_doc, $telefono) {
        $sql = "UPDATE usuario SET tipo_doc = :tipo, telefono = :tel WHERE numero_documento = :num_doc";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':tipo', $tipo_doc);
        $stmt->bindParam(':tel', $telefono);
        $stmt->bindParam(':num_doc', $numero_documento);
        return $stmt->execute();
    }

    // Enviar solicitud con actualización de perfil
    public function enviarSolicitudConPerfil($numero_documento_nuevo, $numero_documento_original, $email, $id_rol_solicitado, $motivo, $fecha_solicitud, $tipo_doc, $telefono) {
        try {
            $this->conexion->beginTransaction();
            
            // Primero actualizar el perfil (incluyendo numero_documento, tipo_doc y telefono)
            $sqlPerfil = "UPDATE usuario SET numero_documento = :num_doc, tipo_doc = :tipo, telefono = :tel WHERE numero_documento = :num_doc_original";
            $stmtPerfil = $this->conexion->prepare($sqlPerfil);
            $stmtPerfil->bindParam(':num_doc', $numero_documento_nuevo);
            $stmtPerfil->bindParam(':tipo', $tipo_doc);
            $stmtPerfil->bindParam(':tel', $telefono);
            $stmtPerfil->bindParam(':num_doc_original', $numero_documento_original);
            
            if (!$stmtPerfil->execute()) {
                $this->conexion->rollBack();
                return false;
            }

            // *** SINCRONIZACIÓN CON TABLA USERS CUANDO CAMBIA EL NÚMERO DE DOCUMENTO ***
            if ($numero_documento_original !== $numero_documento_nuevo) {
                // Solo sincronizar si ambos números son numéricos
                if (preg_match('/^[0-9]+$/', $numero_documento_original) && preg_match('/^[0-9]+$/', $numero_documento_nuevo)) {
                    // Actualizar solo el ID en la tabla users (mantener email y password originales)
                    $sqlUpdateUsers = "UPDATE users SET id = :nuevo_num, updated_at = NOW() WHERE id = :actual_num";
                    $consultUpdateUsers = $this->conexion->prepare($sqlUpdateUsers);
                    $consultUpdateUsers->bindValue(":nuevo_num", $numero_documento_nuevo);
                    $consultUpdateUsers->bindValue(":actual_num", $numero_documento_original);
                    $consultUpdateUsers->execute();
                }
            }
            
            // Luego crear la solicitud
            $sqlSolicitud = "INSERT INTO solicitudes_rol (numero_documento, email, id_rol_solicitado, motivo, fecha_solicitud, estado) VALUES (:num_doc, :email, :rol, :motivo, :fecha, 'enviada')";
            $stmtSolicitud = $this->conexion->prepare($sqlSolicitud);
            $stmtSolicitud->bindParam(':num_doc', $numero_documento_nuevo);
            $stmtSolicitud->bindParam(':email', $email);
            $stmtSolicitud->bindParam(':rol', $id_rol_solicitado);
            $stmtSolicitud->bindParam(':motivo', $motivo);
            $stmtSolicitud->bindParam(':fecha', $fecha_solicitud);
            
            if (!$stmtSolicitud->execute()) {
                $this->conexion->rollBack();
                return false;
            }
            
            $this->conexion->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return false;
        }
    }
    
    // Editar solicitud con actualización de perfil
    public function editarSolicitudConPerfil($id_solicitud, $numero_documento_nuevo, $numero_documento_original, $id_rol_solicitado, $motivo, $tipo_doc, $telefono) {
        try {
            $this->conexion->beginTransaction();
            
            // Primero actualizar el perfil si los datos han cambiado
            $sqlPerfil = "UPDATE usuario SET numero_documento = :num_doc, tipo_doc = :tipo, telefono = :tel WHERE numero_documento = :num_doc_original";
            $stmtPerfil = $this->conexion->prepare($sqlPerfil);
            $stmtPerfil->bindParam(':num_doc', $numero_documento_nuevo);
            $stmtPerfil->bindParam(':tipo', $tipo_doc);
            $stmtPerfil->bindParam(':tel', $telefono);
            $stmtPerfil->bindParam(':num_doc_original', $numero_documento_original);
            
            if (!$stmtPerfil->execute()) {
                $this->conexion->rollBack();
                return false;
            }

            // *** SINCRONIZACIÓN CON TABLA USERS CUANDO CAMBIA EL NÚMERO DE DOCUMENTO ***
            if ($numero_documento_original !== $numero_documento_nuevo) {
                // Solo sincronizar si ambos números son numéricos
                if (preg_match('/^[0-9]+$/', $numero_documento_original) && preg_match('/^[0-9]+$/', $numero_documento_nuevo)) {
                    // Solo actualizar el campo id en la tabla users, manteniendo email y password intactos
                    $sqlUpdateUsers = "UPDATE users SET id = :nuevo_num, updated_at = NOW() WHERE id = :actual_num";
                    $consultUpdateUsers = $this->conexion->prepare($sqlUpdateUsers);
                    $consultUpdateUsers->bindValue(":nuevo_num", $numero_documento_nuevo);
                    $consultUpdateUsers->bindValue(":actual_num", $numero_documento_original);
                    $consultUpdateUsers->execute();
                }
            }
            
            // Luego actualizar la solicitud
            $sqlSolicitud = "UPDATE solicitudes_rol SET numero_documento = :num_doc, id_rol_solicitado = :rol, motivo = :motivo WHERE id_solicitud = :id AND numero_documento = :num_doc_original";
            $stmtSolicitud = $this->conexion->prepare($sqlSolicitud);
            $stmtSolicitud->bindParam(':id', $id_solicitud);
            $stmtSolicitud->bindParam(':num_doc', $numero_documento_nuevo);
            $stmtSolicitud->bindParam(':rol', $id_rol_solicitado);
            $stmtSolicitud->bindParam(':motivo', $motivo);
            $stmtSolicitud->bindParam(':num_doc_original', $numero_documento_original);
            
            if (!$stmtSolicitud->execute()) {
                $this->conexion->rollBack();
                return false;
            }
            
            $this->conexion->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return false;
        }
    }
}