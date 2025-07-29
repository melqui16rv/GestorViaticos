-- ================================================================
-- VALIDACIÓN RÁPIDA - ESTADO ACTUAL DEL SISTEMA
-- ================================================================
-- Ejecutar en cualquier momento para verificar el estado de sincronización

SELECT '🔍 VALIDACIÓN RÁPIDA DEL SISTEMA DE SINCRONIZACIÓN' as titulo;

-- 1. Verificar existencia de tablas
SELECT '📋 VERIFICACIÓN DE TABLAS' as seccion;

SELECT 
    CASE 
        WHEN (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'users') > 0
        THEN '✅ Tabla users existe'
        ELSE '❌ Tabla users NO existe'
    END as tabla_users,
    CASE 
        WHEN (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'usuario') > 0
        THEN '✅ Tabla usuario existe'
        ELSE '❌ Tabla usuario NO existe'
    END as tabla_usuario;

-- 2. Contar registros
SELECT '📊 CONTEO DE REGISTROS' as seccion;

SELECT 
    (SELECT COUNT(*) FROM usuario) as total_usuario,
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as usuario_numericos,
    (SELECT COUNT(*) FROM users) as total_users,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') = (SELECT COUNT(*) FROM users)
        THEN '✅ CANTIDADES COINCIDEN'
        ELSE '⚠️ CANTIDADES NO COINCIDEN'
    END as estado_cantidades;

-- 3. Verificar disparadores
SELECT '⚙️ DISPARADORES ACTIVOS' as seccion;

SELECT 
    TRIGGER_NAME as disparador,
    EVENT_MANIPULATION as evento,
    EVENT_OBJECT_TABLE as tabla,
    CASE 
        WHEN ACTION_STATEMENT LIKE '%INSERT INTO usuario%' OR ACTION_STATEMENT LIKE '%UPDATE usuario%'
        THEN '✅ SINCRONIZACIÓN ACTIVA'
        ELSE '⚠️ REVISAR CONFIGURACIÓN'
    END as estado
FROM information_schema.TRIGGERS 
WHERE EVENT_OBJECT_TABLE IN ('users', 'usuario')
ORDER BY TRIGGER_NAME;

-- 4. Muestra de datos sincronizados
SELECT '🔄 MUESTRA DE SINCRONIZACIÓN' as seccion;

SELECT 
    u.numero_documento as doc_usuario,
    u.nombre_completo as nombre_usuario,
    us.id as id_users,
    us.name as nombre_users,
    CASE 
        WHEN u.numero_documento = us.id AND u.nombre_completo = us.name
        THEN '✅ SYNC'
        ELSE '❌ DESYNC'
    END as estado
FROM usuario u
LEFT JOIN users us ON u.numero_documento = us.id
WHERE u.numero_documento REGEXP '^[0-9]+$'
ORDER BY u.numero_documento
LIMIT 5;

-- 5. Detectar problemas comunes
SELECT '🚨 DETECCIÓN DE PROBLEMAS' as seccion;

-- Usuarios en usuario sin equivalente en users
SELECT 
    'Usuarios en usuario sin equivalente en users' as problema,
    COUNT(*) as cantidad
FROM usuario u
LEFT JOIN users us ON u.numero_documento = us.id
WHERE u.numero_documento REGEXP '^[0-9]+$' 
  AND us.id IS NULL;

-- Usuarios en users sin equivalente en usuario
SELECT 
    'Usuarios en users sin equivalente en usuario' as problema,
    COUNT(*) as cantidad
FROM users us
LEFT JOIN usuario u ON us.id = u.numero_documento
WHERE u.numero_documento IS NULL;

-- 6. Estado general del sistema
SELECT '✅ ESTADO GENERAL DEL SISTEMA' as seccion;

SELECT 
    CASE 
        WHEN (SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE EVENT_OBJECT_TABLE = 'users') >= 2
        THEN '✅ DISPARADORES CONFIGURADOS'
        ELSE '❌ FALTAN DISPARADORES'
    END as disparadores,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') = (SELECT COUNT(*) FROM users)
        THEN '✅ DATOS SINCRONIZADOS'
        ELSE '⚠️ DATOS DESINCRONIZADOS'
    END as sincronizacion,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuario u LEFT JOIN users us ON u.numero_documento = us.id WHERE u.numero_documento REGEXP '^[0-9]+$' AND us.id IS NULL) = 0
        THEN '✅ MIGRACIÓN COMPLETA'
        ELSE '⚠️ MIGRACIÓN INCOMPLETA'
    END as migracion;

SELECT '🏁 VALIDACIÓN COMPLETADA' as fin;
