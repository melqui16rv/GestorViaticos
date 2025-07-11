-- ================================================================
-- SCRIPT DE MIGRACIÓN COMPLETA - EJECUTAR EN ORDEN
-- ================================================================
-- Este script ejecuta todo el proceso de migración paso a paso
-- IMPORTANTE: Ejecutar cada sección una por una, no todo junto

-- ================================================================
-- PASO 0: VERIFICACIÓN INICIAL
-- ================================================================
-- Verificar estado actual antes de comenzar
SELECT '🔍 PASO 0: VERIFICACIÓN INICIAL' as estado;

-- Ver tablas existentes
SHOW TABLES LIKE 'users';
SHOW TABLES LIKE 'usuario';

-- Contar registros actuales
SELECT 
    'ESTADO INICIAL' as verificacion,
    (SELECT COUNT(*) FROM usuario) as total_usuario,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as usuario_numericos;

-- ================================================================
-- PASO 1: RESPALDO DE SEGURIDAD
-- ================================================================
-- Crear tabla de respaldo
SELECT '💾 PASO 1: CREANDO RESPALDO DE SEGURIDAD' as estado;

DROP TABLE IF EXISTS usuario_backup;
CREATE TABLE usuario_backup AS SELECT * FROM usuario;

DROP TABLE IF EXISTS users_backup;
CREATE TABLE users_backup AS SELECT * FROM users;

SELECT 'Respaldo creado exitosamente' as resultado;

-- ================================================================
-- PASO 2: DESACTIVAR DISPARADORES TEMPORALMENTE
-- ================================================================
SELECT '⏸️ PASO 2: DESACTIVANDO DISPARADORES' as estado;

-- Eliminar disparadores si existen
DROP TRIGGER IF EXISTS sync_users_to_usuario_insert;
DROP TRIGGER IF EXISTS sync_users_to_usuario_update;
DROP TRIGGER IF EXISTS sync_users_to_usuario_delete;

SELECT 'Disparadores desactivados' as resultado;

-- ================================================================
-- PASO 3: MIGRAR USUARIOS
-- ================================================================
SELECT '📤 PASO 3: MIGRANDO USUARIOS DE usuario → users' as estado;

-- Limpiar tabla users
DELETE FROM users;
ALTER TABLE users AUTO_INCREMENT = 1;

-- Migrar datos
INSERT INTO users (
    id,
    name,
    email,
    password,
    email_verified_at,
    remember_token,
    created_at,
    updated_at
)
SELECT 
    CAST(numero_documento AS UNSIGNED) as id,
    nombre_completo as name,
    email,
    COALESCE(contraseña, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') as password,
    NULL as email_verified_at,
    NULL as remember_token,
    NOW() as created_at,
    NOW() as updated_at
FROM usuario 
WHERE numero_documento REGEXP '^[0-9]+$'  
  AND numero_documento IS NOT NULL
  AND numero_documento != ''
  AND email IS NOT NULL
  AND email != ''
  AND nombre_completo IS NOT NULL
  AND nombre_completo != ''
ORDER BY CAST(numero_documento AS UNSIGNED);

-- Verificar migración
SELECT 
    'MIGRACIÓN COMPLETADA' as resultado,
    COUNT(*) as usuarios_migrados
FROM users;

-- ================================================================
-- PASO 4: REACTIVAR DISPARADORES
-- ================================================================
SELECT '▶️ PASO 4: REACTIVANDO DISPARADORES DE SINCRONIZACIÓN' as estado;

-- Crear disparador para INSERT
DELIMITER //
CREATE TRIGGER sync_users_to_usuario_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO usuario (
        numero_documento,
        nombre_completo,
        email,
        contraseña
    ) VALUES (
        NEW.id,
        NEW.name,
        NEW.email,
        NEW.password
    );
END//
DELIMITER ;

-- Crear disparador para UPDATE  
DELIMITER //
CREATE TRIGGER sync_users_to_usuario_update
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    UPDATE usuario SET
        numero_documento = NEW.id,
        nombre_completo = NEW.name,
        email = NEW.email,
        contraseña = NEW.password
    WHERE numero_documento = OLD.id;
END//
DELIMITER ;

-- Crear disparador para DELETE (opcional)
DELIMITER //
CREATE TRIGGER sync_users_to_usuario_delete
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    DELETE FROM usuario 
    WHERE numero_documento = OLD.id;
END//
DELIMITER ;

SELECT 'Disparadores reactivados exitosamente' as resultado;

-- ================================================================
-- PASO 5: VERIFICACIÓN FINAL
-- ================================================================
SELECT '✅ PASO 5: VERIFICACIÓN FINAL' as estado;

-- Verificar que los datos coinciden
SELECT 
    'VERIFICACIÓN DE SINCRONIZACIÓN' as verificacion,
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as total_usuario_numericos,
    (SELECT COUNT(*) FROM users) as total_users_migrados,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') = (SELECT COUNT(*) FROM users)
        THEN '✅ DATOS SINCRONIZADOS CORRECTAMENTE'
        ELSE '❌ DATOS NO COINCIDEN - REVISAR'
    END as estado_sincronizacion;

-- Ver algunos registros para verificar
SELECT 'MUESTRA DE DATOS MIGRADOS' as verificacion;

SELECT 
    u.numero_documento,
    u.nombre_completo,
    u.email as email_usuario,
    us.id,
    us.name,
    us.email as email_users
FROM usuario u
JOIN users us ON u.numero_documento = us.id
LIMIT 5;

-- Mostrar disparadores activos
SELECT 'DISPARADORES ACTIVOS' as verificacion;
SHOW TRIGGERS WHERE `Table` IN ('users', 'usuario');

SELECT '🎉 MIGRACIÓN COMPLETADA EXITOSAMENTE' as resultado_final;
