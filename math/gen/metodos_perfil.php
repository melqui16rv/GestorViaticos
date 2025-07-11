<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';


class metodosPerfilUsuario extends Conexion{
    private $conexion;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->conexion = $conexionObj->obtenerConexion();
    }

    /**
     * Obtener datos del perfil del usuario actual
     */
    public function obtenerDatosPerfil($numero_documento) {
        try {
            $sql = "SELECT numero_documento, tipo_doc, telefono, nombre_completo, email, id_rol 
                    FROM usuario WHERE numero_documento = :numero_documento";
            $consult = $this->conexion->prepare($sql);
            $consult->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
            $consult->execute();
            
            $resultado = $consult->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'data' => $resultado
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener datos del perfil: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar información del perfil del usuario
     * Incluye sincronización automática con tabla users
     */
    public function actualizarPerfilUsuario($numero_documento_actual, $nuevo_numero_documento, $tipo_doc, $telefono) {
        try {
            // Iniciar transacción
            $this->conexion->beginTransaction();

            // Validar que el nuevo número de documento no esté siendo usado por otro usuario
            if ($numero_documento_actual !== $nuevo_numero_documento) {
                $sqlVerificar = "SELECT COUNT(*) FROM usuario WHERE numero_documento = :nuevo_num AND numero_documento != :actual_num";
                $consultVerificar = $this->conexion->prepare($sqlVerificar);
                $consultVerificar->bindValue(":nuevo_num", $nuevo_numero_documento);
                $consultVerificar->bindValue(":actual_num", $numero_documento_actual);
                $consultVerificar->execute();
                $existe = $consultVerificar->fetchColumn();

                if ($existe > 0) {
                    $this->conexion->rollback();
                    return [
                        'success' => false,
                        'message' => 'El número de documento ya está siendo utilizado por otro usuario'
                    ];
                }

                // *** ELIMINAR REGISTROS DE SOLICITUDES_ROL CUANDO CAMBIA EL NÚMERO DE DOCUMENTO ***
                // Esto evita errores de restricción de clave foránea al cambiar el número de documento
                $sqlDeleteSolicitudes = "DELETE FROM solicitudes_rol WHERE numero_documento = :actual_num";
                $consultDeleteSolicitudes = $this->conexion->prepare($sqlDeleteSolicitudes);
                $consultDeleteSolicitudes->bindValue(":actual_num", $numero_documento_actual);
                $resultadoDeleteSolicitudes = $consultDeleteSolicitudes->execute();

                if (!$resultadoDeleteSolicitudes) {
                    $this->conexion->rollback();
                    return [
                        'success' => false,
                        'message' => 'Error al eliminar las solicitudes de rol asociadas'
                    ];
                }
            }

            // Actualizar datos en la tabla usuario
            $sqlUpdateUsuario = "UPDATE usuario 
                               SET numero_documento = :nuevo_num, 
                                   tipo_doc = :tipo_doc, 
                                   telefono = :telefono 
                               WHERE numero_documento = :actual_num";
            
            $consultUpdateUsuario = $this->conexion->prepare($sqlUpdateUsuario);
            $consultUpdateUsuario->bindValue(":nuevo_num", $nuevo_numero_documento);
            $consultUpdateUsuario->bindValue(":tipo_doc", $tipo_doc);
            $consultUpdateUsuario->bindValue(":telefono", $telefono);
            $consultUpdateUsuario->bindValue(":actual_num", $numero_documento_actual);
            
            $resultadoUsuario = $consultUpdateUsuario->execute();

            if (!$resultadoUsuario) {
                $this->conexion->rollback();
                return [
                    'success' => false,
                    'message' => 'Error al actualizar el perfil del usuario'
                ];
            }

            // *** SINCRONIZACIÓN CON TABLA USERS CUANDO CAMBIA EL NÚMERO DE DOCUMENTO ***
            if ($numero_documento_actual !== $nuevo_numero_documento) {
                // Solo sincronizar si ambos números son numéricos
                if (preg_match('/^[0-9]+$/', $numero_documento_actual) && preg_match('/^[0-9]+$/', $nuevo_numero_documento)) {
                    // Solo actualizar el campo id en la tabla users, manteniendo email y password intactos
                    $sqlUpdateUsers = "UPDATE users SET id = :nuevo_num, updated_at = NOW() WHERE id = :actual_num";
                    $consultUpdateUsers = $this->conexion->prepare($sqlUpdateUsers);
                    $consultUpdateUsers->bindValue(":nuevo_num", $nuevo_numero_documento);
                    $consultUpdateUsers->bindValue(":actual_num", $numero_documento_actual);
                    $consultUpdateUsers->execute();

                    // Actualizar la sesión con el nuevo número de documento
                    if (isset($_SESSION['numero_documento']) && $_SESSION['numero_documento'] == $numero_documento_actual) {
                        $_SESSION['numero_documento'] = $nuevo_numero_documento;
                    }
                }
            }

            // Confirmar transacción
            $this->conexion->commit();

            return [
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'nuevo_numero_documento' => $nuevo_numero_documento
            ];

        } catch (PDOException $e) {
            $this->conexion->rollback();
            return [
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validar que el número de documento tenga el formato correcto
     */
    public function validarNumeroDocumento($numero_documento) {
        // Debe ser solo números y tener entre 6 y 12 dígitos
        if (!preg_match('/^[0-9]{6,12}$/', $numero_documento)) {
            return [
                'success' => false,
                'message' => 'El número de documento debe contener solo números y tener entre 6 y 12 dígitos'
            ];
        }
        return ['success' => true];
    }

    /**
     * Validar que el teléfono tenga el formato correcto
     */
    public function validarTelefono($telefono) {
        // Debe ser solo números y tener entre 7 y 15 dígitos
        if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
            return [
                'success' => false,
                'message' => 'El teléfono debe contener solo números y tener entre 7 y 15 dígitos'
            ];
        }
        return ['success' => true];
    }

    /**
     * Obtener tipos de documento disponibles
     */
    public function obtenerTiposDocumento() {
        return [
            'CC' => 'Cédula de Ciudadanía',
            'CE' => 'Cédula de Extranjería',
            'TI' => 'Tarjeta de Identidad',
            'PP' => 'Pasaporte'
        ];
    }
}
?>