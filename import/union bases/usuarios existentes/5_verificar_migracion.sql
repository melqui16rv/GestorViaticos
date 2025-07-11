-- ================================================================
-- PASO 5: VERIFICAR MIGRACIÓN COMPLETA
-- ================================================================
-- EJECUTAR QUINTO - Verificar que todo funcionó correctamente

-- 1. Verificar disparadores activos
SELECT 
    'ESTADO DE DISPARADORES' as verificacion,
    COUNT(*) as disparadores_activos
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE()
AND TRIGGER_NAME LIKE 'sync_%';

-- 2. Verificar coincidencia de datos
SELECT 
    'VERIFICACIÓN DE DATOS' as verificacion,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as total_usuario_numericos,
    CASE 
        WHEN (SELECT COUNT(*) FROM users) = (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$')
        THEN '✅ DATOS SINCRONIZADOS'
        ELSE '⚠️ REVISAR SINCRONIZACIÓN'
    END as estado;

-- 3. Comparar usuarios específicos (mostrar algunos ejemplos)
SELECT 
    u.id as user_id,
    u.name as user_name,
    u.email as user_email,
    us.numero_documento,
    us.nombre_completo,
    us.email as usuario_email,
    CASE 
        WHEN u.email = us.email AND u.name = us.nombre_completo 
        THEN '✅ SINCRONIZADO'
        ELSE '⚠️ VERIFICAR'
    END as estado_sync
FROM users u
LEFT JOIN usuario us ON CAST(u.id AS CHAR) = us.numero_documento
LIMIT 5;

-- 4. Verificar usuarios sin migrar (documentos no numéricos)
SELECT 
    'USUARIOS NO MIGRADOS' as categoria,
    numero_documento,
    nombre_completo,
    email,
    'Documento no numérico' as razon
FROM usuario 
WHERE numero_documento NOT REGEXP '^[0-9]+$'
ORDER BY numero_documento;

-- 5. Resumen final
SELECT 
    '🎉 MIGRACIÓN COMPLETADA' as resultado,
    CONCAT(
        (SELECT COUNT(*) FROM users), 
        ' usuarios migrados a users'
    ) as usuarios_migrados,
    CONCAT(
        (SELECT COUNT(*) FROM usuario), 
        ' usuarios conservados en usuario'
    ) as usuarios_conservados,
    CONCAT(
        (SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE() AND TRIGGER_NAME LIKE 'sync_%'),
        ' disparadores activos'
    ) as sincronizacion;
