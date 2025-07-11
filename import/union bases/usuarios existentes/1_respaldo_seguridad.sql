-- ================================================================
-- PASO 1: RESPALDO DE SEGURIDAD
-- ================================================================
-- EJECUTAR PRIMERO - Crear respaldo de los datos actuales

-- Crear tabla de respaldo para usuarios
CREATE TABLE usuario_backup_migracion AS SELECT * FROM usuario;

-- Crear tabla de respaldo para users (si tiene datos)
CREATE TABLE users_backup_migracion AS SELECT * FROM users;

-- Verificar que se crearon los respaldos
SELECT COUNT(*) as total_usuario_backup FROM usuario_backup_migracion;
SELECT COUNT(*) as total_users_backup FROM users_backup_migracion;

-- Mostrar los datos que vamos a migrar
SELECT 
    numero_documento,
    nombre_completo,
    email,
    id_rol,
    'Datos a migrar' as estado
FROM usuario 
ORDER BY numero_documento;
