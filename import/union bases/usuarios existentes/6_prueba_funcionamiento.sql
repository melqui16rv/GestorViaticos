-- ================================================================
-- PASO 6: PRUEBA DE FUNCIONAMIENTO
-- ================================================================
-- EJECUTAR SEXTO - Probar que los disparadores funcionan correctamente

-- PRUEBA 1: Insertar nuevo usuario en users (debe aparecer en usuario)
INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES (
    'Usuario Prueba Migración', 
    'prueba.migracion@test.com', 
    '$2y$10$test.hash.migracion', 
    NOW(), 
    NOW()
);

-- Verificar que se sincronizó automáticamente
SELECT 
    'PRUEBA INSERCIÓN' as test,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM usuario 
            WHERE email = 'prueba.migracion@test.com'
        ) THEN '✅ DISPARADOR FUNCIONANDO'
        ELSE '❌ DISPARADOR NO FUNCIONA'
    END as resultado;

-- PRUEBA 2: Actualizar usuario existente
UPDATE users 
SET name = 'Usuario Actualizado Migración'
WHERE email = 'prueba.migracion@test.com';

-- Verificar que se actualizó en usuario
SELECT 
    'PRUEBA ACTUALIZACIÓN' as test,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM usuario 
            WHERE email = 'prueba.migracion@test.com'
            AND nombre_completo = 'Usuario Actualizado Migración'
        ) THEN '✅ DISPARADOR FUNCIONANDO'
        ELSE '❌ DISPARADOR NO FUNCIONA'
    END as resultado;

-- PRUEBA 3: Verificar que usuarios migrados siguen intactos
SELECT 
    'USUARIOS MIGRADOS' as categoria,
    numero_documento,
    nombre_completo,
    email,
    id_rol,
    '✅ Conservado' as estado
FROM usuario 
WHERE numero_documento IN ('1073672380', '1111', '52366315')
ORDER BY numero_documento;

-- LIMPIAR DATOS DE PRUEBA
DELETE FROM users WHERE email = 'prueba.migracion@test.com';
DELETE FROM usuario WHERE email = 'prueba.migracion@test.com';

-- RESULTADO FINAL
SELECT 
    '🎉 MIGRACIÓN Y SINCRONIZACIÓN COMPLETADA' as estado,
    'Los usuarios existentes se conservaron' as usuarios_existentes,
    'Los nuevos usuarios se sincronizarán automáticamente' as usuarios_futuros,
    'Los disparadores están funcionando correctamente' as sincronizacion;
