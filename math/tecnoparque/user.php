<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class user_tecnoparque extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }


    public function crearUsuario($num_doc, $tipo_doc, $nombre_completo, $contraseña, $email, $telefono, $id_rol) {
        // Verificar si el número de documento ya existe
        $sqlVerificar = "SELECT COUNT(*) FROM usuario WHERE numero_documento = :num";
        $consultVerificar = $this->conexion->prepare($sqlVerificar);
        $consultVerificar->bindValue(":num", $num_doc);
        $consultVerificar->execute();
        $existe = $consultVerificar->fetchColumn();
    
        if ($existe > 0) {
            echo "<script type='text/javascript'>
            alert('Error: El número de documento ya está registrado.');
            window.location='usuario.php';
            </script>";
            return;
        }
    
        // *** Cambiar el método de hash de contraseña a bcrypt ***
        $contraseña = password_hash($contraseña, PASSWORD_BCRYPT);
        // Antes: $contraseña = hash('sha256', $contraseña);
    


    
        $sql = "INSERT INTO usuario (numero_documento, tipo_doc, nombre_completo, contraseña, email, telefono, id_rol)
                VALUES (:num, :tipo, :nom, :pass, :email, :tel, :id)";
        $consult = $this->conexion->prepare($sql);
        $consult->bindValue(":num", $num_doc);
        $consult->bindValue(":tipo", $tipo_doc);
        $consult->bindValue(":nom", $nombre_completo);
        $consult->bindValue(":pass", $contraseña);
        $consult->bindValue(":email", $email);
        $consult->bindValue(":tel", $telefono);
        $consult->bindValue(":id", $id_rol);
    
        $resultado = $consult->execute();
    
        if ($resultado) {
            echo "<script type='text/javascript'>
            alert('Usuario adicionado correctamente...');
            window.location='../index.php';
            </script>";
        } else {
            echo "<script type='text/javascript'>
            alert('Error en la asignación del registro...');
            window.location='../index.php';
            </script>";
        }
    }


    public function eliminarUsuario($numero) {
        try {
            $this->conexion->beginTransaction();
    
            // Eliminar el usuario
            $sqlDeleteUsuario = "DELETE FROM usuario WHERE numero_documento = :num";
            $stmtDeleteUsuario = $this->conexion->prepare($sqlDeleteUsuario);
            $stmtDeleteUsuario->bindValue(":num", $numero, PDO::PARAM_STR);
            $stmtDeleteUsuario->execute();
    
            $this->conexion->commit();
    
            // Respuesta de éxito
            return ["success" => true, "message" => "Usuario eliminado con éxito."];
        } catch (PDOException $e) {
            $this->conexion->rollBack();
    
            // Mensaje específico si hay una restricción de clave foránea
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Integrity constraint violation: 1451') !== false) {
                return ["success" => false, "message" => "No se puede eliminar el instructor porque tiene vacantes asignadas."];
            } else {
                return ["success" => false, "message" => "Ocurrió un error al eliminar el usuario. Por favor, inténtelo de nuevo.", "error" => $e->getMessage()];
            }
        }
    }  

    public function actualizar_usuario($numero, $tipo_doc, $contraseña, $nombre_completo, $email, $telefono, $rol) {
    
        // Verificar si se proporciona una nueva contraseña
        if (!empty($contraseña)) {
            // *** Cambiar el método de hash de contraseña a bcrypt ***
            $contraseña = password_hash($contraseña, PASSWORD_BCRYPT);
            // Antes: $contraseña = hash('sha256', $contraseña);

            $sql = "UPDATE usuario SET tipo_doc = :tipo, contraseña = :pass, nombre_completo = :nombre, email = :em, telefono = :tel, id_rol = :id WHERE numero_documento = :num";
        } else {
            // Si no hay nueva contraseña, no se actualiza
            $sql = "UPDATE usuario SET tipo_doc = :tipo, nombre_completo = :nombre, email = :em, telefono = :tel, id_rol = :id WHERE numero_documento = :num";
        }
    
        $consult = $this->conexion->prepare($sql);
        $consult->bindParam(":num", $numero);
        $consult->bindParam(":tipo", $tipo_doc);
    
        // Solo se enlaza la contraseña si se proporciona una nueva
        if (!empty($contraseña)) {
            $consult->bindParam(":pass", $contraseña);
        }
    
        $consult->bindParam(":nombre", $nombre_completo);
        $consult->bindParam(":em", $email);
        $consult->bindParam(":tel", $telefono);
        $consult->bindParam(":id", $rol);
    
        $resultado = $consult->execute();
    
        if ($resultado) {    
            echo "<script type='text/javascript'>
            alert('Usuario actualizado correctamente...');
            window.location='../index.php';
            </script>";
        } else {
            echo "<script type='text/javascript'>
            alert('Error al actualizar el usuario...');
            window.location='../index.php';
            </script>";
        }
    }


    public function iniciarSesion($num_doc, $password) {
        // Modificamos la consulta para obtener el usuario por número de documento
        $sql = "SELECT * FROM usuario WHERE numero_documento = :nro_doc";
        $consult = $this->conexion->prepare($sql);
        $consult->bindParam(':nro_doc', $num_doc, PDO::PARAM_STR);
        $consult->execute();
        
        $result = $consult->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $contraseñaAlmacenada = $result['contraseña'];
            // Intentamos verificar la contraseña con bcrypt
            if (password_verify($password, $contraseñaAlmacenada)) {
                // Contraseña correcta con bcrypt
                $_SESSION['numero_documento'] = $result['numero_documento'];
                $rol = $result['id_rol'];
                $_SESSION['id_rol'] = $rol;
                
                // Redireccionar según el rol
                switch ($rol) {
                    case '6':
                        header('Location: ' . BASE_URL . 'app/SENNOVA/General/index.php');
                        break;
                    case '5':
                        header('Location: ' . BASE_URL . 'app/SENNOVA/General/index.php');
                        break;
                    case '4':
                        header('Location: ' . BASE_URL . 'app/SENNOVA/General/index.php');
                        break;
                    case '3':
                        header('Location: ' . BASE_URL . 'app/presupuesto/index.php');
                        break;
                    case '2':
                        header('Location: ' . BASE_URL . 'app/gestor/index.php');
                        break;
                    case '1':
                        header('Location: ' . BASE_URL . 'app/admin/index.php');
                        break;
                    default:
                        echo "<div class='alerta text-center'>Rol no válido.</div>";
                }
                return true;
            } else {
                // Si no coincide con bcrypt, verificamos si coincide con SHA-256
                if (hash('sha256', $password) === $contraseñaAlmacenada) {
                    // Actualizamos la contraseña a bcrypt
                    $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
                    $sqlUpdate = "UPDATE usuario SET contraseña = :nuevoHash WHERE numero_documento = :nro_doc";
                    $stmtUpdate = $this->conexion->prepare($sqlUpdate);
                    $stmtUpdate->bindParam(':nuevoHash', $nuevoHash);
                    $stmtUpdate->bindParam(':nro_doc', $num_doc);
                    $stmtUpdate->execute();
                    
                    // Iniciar sesión
                    $_SESSION['numero_documento'] = $result['numero_documento'];
                    $rol = $result['id_rol'];
                    $_SESSION['id_rol'] = $rol;
                    
                    // Redireccionar según el rol
                    switch ($rol) {
                        case '3':
                            header('Location: ' . BASE_URL . 'app/presupuesto/');
                            break;
                        case '2':
                            header('Location: ' . BASE_URL . 'app/gestor/');
                            break;
                        case '1':
                            header('Location: ' . BASE_URL . 'app/admin/');
                            break;
                        default:
                            echo "<div class='alerta text-center'>Rol no válido.</div>";
                    }
                    return true;
                } else {
                    // Contraseña incorrecta
                    echo "<div class='alerta text-center'>Credenciales no válidas.</div>";
                    return false;
                }
            }
        } else {
            echo "<div class='alerta text-center'>Usuario no encontrado.</div>";
            return false;
        }
    }

    public function obtenerDatosEstructuradosPorNumeroDocumento($numero_documento) {
        $sql = "
            SELECT 
                usuario.numero_documento AS numero_documento,
                usuario.tipo_doc AS tipo_doc,
                usuario.nombre_completo AS nombre_completo,
                usuario.email AS email,
                usuario.telefono AS telefono,
                usuario.id_rol AS id_rol
            FROM usuario
            WHERE usuario.numero_documento = :numero_documento
        ";
    
        $consult = $this->conexion->prepare($sql);
        $consult->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
        $consult->execute();
        $result = $consult->fetch(PDO::FETCH_ASSOC);
    
        if ($result === false || empty($result)) {
            return null; // Devuelve null si no hay resultados
        }
    
        // Estructuración de los datos
        return ['usuario' => $result];
    }
    
    public function obtenerDatosUsuarioLogueado($id){
        $sql="SELECT * FROM usuario WHERE numero_documento=:id";
        $consult=$this->conexion->prepare($sql);
        $consult->bindParam(":id", $id);
        $consult->execute();
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscar_usuario($termino) {
        if (empty($termino)) {
            $sql = "SELECT usuario.*, 'N/A' as nombre_rol 
                    FROM usuario 
                    ORDER BY usuario.nombre_completo ASC 
                    LIMIT 10";
            $consult = $this->conexion->prepare($sql);
            $consult->execute();
        } else {
            $sql = "SELECT usuario.*, 'N/A' as nombre_rol 
                    FROM usuario 
                    WHERE usuario.numero_documento LIKE :termino 
                    OR usuario.nombre_completo LIKE :termino 
                    ORDER BY usuario.nombre_completo ASC
                    LIMIT 10";
            $consult = $this->conexion->prepare($sql);
            $termino = "%$termino%";
            $consult->bindParam(':termino', $termino, PDO::PARAM_STR);
            $consult->execute();
        }
        
        return $consult->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function ver_un_usuario($numero_doc) {
        $sql = "SELECT * FROM usuario WHERE numero_documento = :numero";
        $consult = $this->conexion->prepare($sql);
        $consult->bindParam(":numero", $numero_doc, PDO::PARAM_STR);
        $consult->execute();
        $result = $consult->fetch(PDO::FETCH_ASSOC); // Cambiamos a fetch para un solo registro
        return $result; // Regresamos un solo resultado
    }

    /**
     * Obtiene estadísticas generales de las actualizaciones por tipo de tabla
     */
    public function obtenerEstadisticasActualizaciones() {
        $sql = "SELECT 
                    tipo_tabla,
                    COUNT(*) as total_actualizaciones,
                    SUM(registros_actualizados) as total_registros_actualizados,
                    SUM(registros_nuevos) as total_registros_nuevos,
                    MAX(fecha_actualizacion) as ultima_actualizacion
                FROM registros_actualizaciones
                GROUP BY tipo_tabla";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de actualizaciones por fecha
     */
    public function obtenerEstadisticasPorFecha($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    DATE(fecha_actualizacion) as fecha,
                    tipo_tabla,
                    SUM(registros_actualizados) as actualizados,
                    SUM(registros_nuevos) as nuevos
                FROM registros_actualizaciones
                WHERE fecha_actualizacion BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY DATE(fecha_actualizacion), tipo_tabla
                ORDER BY fecha_actualizacion";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas por usuario
     */
    public function obtenerEstadisticasPorUsuario() {
        $sql = "SELECT 
                    u.nombre_completo,
                    ra.tipo_tabla,
                    COUNT(*) as total_actualizaciones,
                    SUM(ra.registros_actualizados) as total_actualizados,
                    SUM(ra.registros_nuevos) as total_nuevos
                FROM registros_actualizaciones ra
                JOIN usuario u ON ra.usuario_id = u.numero_documento
                GROUP BY u.numero_documento, ra.tipo_tabla
                ORDER BY u.nombre_completo, ra.tipo_tabla";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el total de registros actuales en cada tabla
     */
    public function obtenerTotalRegistros() {
        $totales = [];
        
        $tablas = ['cdp', 'crp', 'op'];
        foreach ($tablas as $tabla) {
            $sql = "SELECT COUNT(*) as total FROM " . $tabla;
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $totales[$tabla] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }
        
        return $totales;
    }

    /**
     * Obtiene las últimas actualizaciones de la tabla registros_actualizaciones
     */
    public function obtenerUltimasActualizaciones() {
        $sql = "SELECT 
                ra.tipo_tabla,
                ra.nombre_archivo,
                ra.fecha_actualizacion,
                ra.registros_actualizados,
                ra.registros_nuevos,
                u.nombre_completo as usuario
              FROM registros_actualizaciones ra
              JOIN usuario u ON ra.usuario_id = u.numero_documento
              ORDER BY ra.fecha_actualizacion DESC
              LIMIT 10";
              
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}