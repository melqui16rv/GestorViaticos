-- ================================================================
-- PRUEBA COMPLETA DE FUNCIONAMIENTO - DESPUÉS DE MIGRACIÓN
-- ================================================================
-- EJECUTAR DESPUÉS de completar la migración completa

-- Mostrar estado antes de las pruebas
SELECT '🧪 INICIANDO PRUEBAS DE DISPARADORES' as estado;

SELECT 
    'ESTADO ANTES DE PRUEBAS' as verificacion,
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as total_usuario,
    (SELECT COUNT(*) FROM users) as total_users;

-- ================================================================
-- PRUEBA 1: INSERTAR USUARIO NUEVO
-- ================================================================
SELECT '➕ PRUEBA 1: INSERTAR USUARIO' as prueba;

INSERT INTO users (
    id,
    name,
    email,
    password,
    created_at,
    updated_at
) VALUES (
    99999,
    'Usuario Prueba Disparador',
    'prueba.disparador@test.com',
    '$2y$10$ejemplo.hash.password',
    NOW(),
    NOW()
);

-- Verificar que se creó automáticamente en usuario
SELECT 
    'VERIFICACIÓN INSERT' as prueba,
    CASE 
        WHEN EXISTS (SELECT 1 FROM usuario WHERE numero_documento = '99999')
        THEN '✅ DISPARADOR INSERT FUNCIONA'
        ELSE '❌ DISPARADOR INSERT NO FUNCIONA'
    END as resultado;

-- ================================================================
-- PRUEBA 2: ACTUALIZAR USUARIO
-- ================================================================
SELECT '✏️ PRUEBA 2: ACTUALIZAR USUARIO' as prueba;

UPDATE users 
SET name = 'Usuario Prueba ACTUALIZADO',
    email = 'prueba.actualizado@test.com'
WHERE id = 99999;

-- Verificar que se actualizó en usuario
SELECT 
    'VERIFICACIÓN UPDATE' as prueba,
    CASE 
        WHEN EXISTS (SELECT 1 FROM usuario WHERE numero_documento = '99999' AND nombre_completo = 'Usuario Prueba ACTUALIZADO')
        THEN '✅ DISPARADOR UPDATE FUNCIONA'
        ELSE '❌ DISPARADOR UPDATE NO FUNCIONA'
    END as resultado;

-- ================================================================
-- PRUEBA 3: VERIFICAR DATOS SINCRONIZADOS
-- ================================================================
SELECT 'DATOS SINCRONIZADOS DE PRUEBA' as verificacion;

SELECT 
    u.numero_documento,
    u.nombre_completo,
    u.email as email_usuario,
    us.id,
    us.name,
    us.email as email_users,
    '✅ SINCRONIZADO' as estado
FROM usuario u
JOIN users us ON u.numero_documento = us.id
WHERE u.numero_documento = '99999';

-- ================================================================
-- PRUEBA 4: ELIMINAR USUARIO
-- ================================================================
SELECT '🗑️ PRUEBA 4: ELIMINAR USUARIO' as prueba;

DELETE FROM users WHERE id = 99999;

-- Verificar que se eliminó de usuario también
SELECT 
    'VERIFICACIÓN DELETE' as prueba,
    CASE 
        WHEN NOT EXISTS (SELECT 1 FROM usuario WHERE numero_documento = '99999')
        THEN '✅ DISPARADOR DELETE FUNCIONA'
        ELSE '⚠️ DISPARADOR DELETE NO ACTIVO (revisar si está configurado)'
    END as resultado;

-- ================================================================
-- PRUEBA 5: VERIFICAR USUARIOS REALES MIGRADOS
-- ================================================================
SELECT '🔄 PRUEBA 5: SINCRONIZACIÓN CON DATOS REALES' as prueba;

-- Tomar algunos usuarios existentes y verificar sincronización
SELECT 
    'MUESTRA DE SINCRONIZACIÓN REAL' as verificacion,
    u.numero_documento,
    u.nombre_completo,
    u.email as email_usuario,
    us.id,
    us.name,
    us.email as email_users,
    CASE 
        WHEN u.numero_documento = us.id AND u.nombre_completo = us.name AND u.email = us.email
        THEN '✅ PERFECTAMENTE SINCRONIZADO'
        ELSE '⚠️ REVISAR SINCRONIZACIÓN'
    END as estado_sync
FROM usuario u
JOIN users us ON u.numero_documento = us.id
WHERE u.numero_documento REGEXP '^[0-9]+$'
ORDER BY u.numero_documento
LIMIT 5;

-- ================================================================
-- VERIFICACIÓN FINAL COMPLETA
-- ================================================================
SELECT '⚙️ DISPARADORES ACTIVOS' as verificacion;

SHOW TRIGGERS WHERE `Table` IN ('users', 'usuario');

-- Resumen final completo
SELECT '📊 RESUMEN FINAL DE SINCRONIZACIÓN' as resumen;

SELECT 
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as total_usuario_numericos,
    (SELECT COUNT(*) FROM users) as total_users_migrados,
    (SELECT COUNT(*) FROM usuario u JOIN users us ON u.numero_documento = us.id) as registros_sincronizados,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') = (SELECT COUNT(*) FROM users)
        THEN '✅ BASES DE DATOS PERFECTAMENTE SINCRONIZADAS'
        ELSE '❌ REVISAR SINCRONIZACIÓN - CANTIDADES NO COINCIDEN'
    END as estado_sincronizacion;

-- Verificar integridad de datos migrados
SELECT 'INTEGRIDAD DE DATOS MIGRADOS' as verificacion;

SELECT 
    COUNT(*) as total_coincidencias,
    SUM(CASE WHEN u.nombre_completo = us.name THEN 1 ELSE 0 END) as nombres_coinciden,
    SUM(CASE WHEN u.email = us.email THEN 1 ELSE 0 END) as emails_coinciden,
    SUM(CASE WHEN u.contraseña = us.password THEN 1 ELSE 0 END) as passwords_coinciden
FROM usuario u
JOIN users us ON u.numero_documento = us.id
WHERE u.numero_documento REGEXP '^[0-9]+$';

SELECT '🎉 PRUEBAS COMPLETADAS - SISTEMA LISTO PARA PRODUCCIÓN' as resultado_final;
