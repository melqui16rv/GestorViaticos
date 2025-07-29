
-- ================================================================
-- SCRIPT DE VALIDACIÓN PARA DISPARADORES DE SINCRONIZACIÓN
-- ================================================================
-- Este script valida que los disparadores funcionen correctamente
-- Ejecutar después de instalar los disparadores

-- ================================================================
-- 1. VERIFICAR QUE LOS DISPARADORES ESTÉN INSTALADOS
-- ================================================================
SELECT 
    TRIGGER_NAME as 'Disparador',
    EVENT_MANIPULATION as 'Evento',
    EVENT_OBJECT_TABLE as 'Tabla',
    ACTION_TIMING as 'Momento'
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE()
AND TRIGGER_NAME LIKE 'sync_%'
ORDER BY TRIGGER_NAME;

-- ================================================================
-- 2. CREAR ROL POR DEFECTO SI NO EXISTE
-- ================================================================
INSERT IGNORE INTO roles_app (id_rol, nombre_rol) 
VALUES ('001', 'Usuario Básico');

-- Verificar que el rol se creó
SELECT * FROM roles_app WHERE id_rol = '001';

-- ================================================================
-- 3. TESTING DE INSERCIÓN
-- ================================================================
-- Paso 1: Insertar usuario de prueba en users
INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES (
    'Usuario Prueba Disparador', 
    'prueba.disparador@test.com', 
    '$2y$10$test.hash.password.example', 
    NOW(), 
    NOW()
);

-- Paso 2: Verificar que se creó en la tabla usuario
SELECT 
    'TEST INSERCIÓN' as test_tipo,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM usuario 
            WHERE email = 'prueba.disparador@test.com'
        ) THEN '✅ PASÓ' 
        ELSE '❌ FALLÓ' 
    END as resultado;

-- Mostrar los datos sincronizados
SELECT 
    numero_documento,
    nombre_completo,
    email,
    id_rol,
    tipo_doc
FROM usuario 
WHERE email = 'prueba.disparador@test.com';

-- ================================================================
-- 4. TESTING DE ACTUALIZACIÓN
-- ================================================================
-- Paso 1: Actualizar el usuario en users
UPDATE users 
SET 
    name = 'Usuario Actualizado Disparador',
    email = 'actualizado.disparador@test.com'
WHERE email = 'prueba.disparador@test.com';

-- Paso 2: Verificar que se actualizó en usuario
SELECT 
    'TEST ACTUALIZACIÓN' as test_tipo,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM usuario 
            WHERE email = 'actualizado.disparador@test.com'
            AND nombre_completo = 'Usuario Actualizado Disparador'
        ) THEN '✅ PASÓ' 
        ELSE '❌ FALLÓ' 
    END as resultado;

-- Mostrar los datos actualizados
SELECT 
    numero_documento,
    nombre_completo,
    email,
    'Después de UPDATE' as estado
FROM usuario 
WHERE email = 'actualizado.disparador@test.com';

-- ================================================================
-- 5. VERIFICAR INTEGRIDAD DE DATOS
-- ================================================================
-- Contar registros para verificar consistencia
SELECT 
    'CONTEO DE REGISTROS' as verificacion,
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM usuario) as total_usuario,
    CASE 
        WHEN (SELECT COUNT(*) FROM users) <= (SELECT COUNT(*) FROM usuario) 
        THEN '✅ CONSISTENTE' 
        ELSE '⚠️ REVISAR' 
    END as estado_consistencia;

-- ================================================================
-- 6. BUSCAR USUARIOS SIN SINCRONIZAR
-- ================================================================
SELECT 
    u.id,
    u.name,
    u.email,
    'Sin sincronizar' as estado
FROM users u 
LEFT JOIN usuario us ON CAST(u.id AS CHAR) = us.numero_documento 
WHERE us.numero_documento IS NULL
LIMIT 5;

-- ================================================================
-- 7. VERIFICAR CAMPOS POR DEFECTO
-- ================================================================
SELECT 
    numero_documento,
    tipo_doc,
    telefono,
    id_rol,
    'Campos por defecto' as verificacion
FROM usuario 
WHERE email = 'actualizado.disparador@test.com';

-- ================================================================
-- 8. LIMPIEZA DE DATOS DE PRUEBA
-- ================================================================
-- Eliminar los datos de prueba creados
-- NOTA: Esto también probará el disparador de eliminación si está activo

-- Primero eliminar de usuario (para evitar errores de llave foránea)
DELETE FROM usuario WHERE email = 'actualizado.disparador@test.com';

-- Luego eliminar de users
DELETE FROM users WHERE email = 'actualizado.disparador@test.com';

-- Verificar limpieza
SELECT 
    'LIMPIEZA' as test_tipo,
    CASE 
        WHEN NOT EXISTS (
            SELECT 1 FROM users 
            WHERE email = 'actualizado.disparador@test.com'
        ) AND NOT EXISTS (
            SELECT 1 FROM usuario 
            WHERE email = 'actualizado.disparador@test.com'
        ) THEN '✅ COMPLETADA' 
        ELSE '⚠️ PENDIENTE' 
    END as resultado;

-- ================================================================
-- 9. RESUMEN FINAL
-- ================================================================
SELECT 
    '=== RESUMEN DE VALIDACIÓN ===' as titulo,
    '' as separador;

SELECT 
    CASE 
        WHEN COUNT(*) >= 2 THEN '✅ DISPARADORES INSTALADOS'
        ELSE '❌ FALTAN DISPARADORES'
    END as estado_disparadores
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE()
AND TRIGGER_NAME LIKE 'sync_%';

SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM roles_app WHERE id_rol = '001') 
        THEN '✅ ROL POR DEFECTO EXISTE'
        ELSE '❌ FALTA ROL POR DEFECTO'
    END as estado_rol;

-- ================================================================
-- NOTAS PARA EL ADMINISTRADOR:
-- ================================================================
/*
Si todos los tests muestran ✅, los disparadores están funcionando correctamente.

Si algún test muestra ❌:
1. Verificar que los disparadores estén instalados
2. Verificar que exista el rol '001' en roles_app
3. Revisar permisos de la base de datos
4. Consultar logs de MySQL para errores específicos

Para ver logs de errores:
SHOW ENGINE INNODB STATUS;
*/
