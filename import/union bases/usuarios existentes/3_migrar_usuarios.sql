-- ================================================================
-- PASO 3: MIGRAR USUARIOS EXISTENTES A LA TABLA USERS
-- ================================================================
-- EJECUTAR TERCERO - Migrar datos de usuario → users

-- Limpiar tabla users antes de migrar (por si tiene datos previos)
DELETE FROM users;

-- Reiniciar auto_increment si es necesario
ALTER TABLE users AUTO_INCREMENT = 1;

-- Insertar usuarios existentes en la tabla users
-- Convertir numero_documento (VARCHAR) a id (INT) para users
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
    COALESCE(contraseña, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') as password, -- Hash por defecto si contraseña es NULL
    NULL as email_verified_at,
    NULL as remember_token,
    NOW() as created_at,
    NOW() as updated_at
FROM usuario 
WHERE numero_documento REGEXP '^[0-9]+$'  -- Solo documentos numéricos
  AND numero_documento IS NOT NULL
  AND numero_documento != ''
  AND email IS NOT NULL
  AND email != ''
  AND nombre_completo IS NOT NULL
  AND nombre_completo != ''
ORDER BY CAST(numero_documento AS UNSIGNED);

-- Verificar la migración
SELECT 
    'MIGRACIÓN COMPLETADA' as resultado,
    COUNT(*) as usuarios_migrados
FROM users;

-- Mostrar los usuarios migrados
SELECT 
    id,
    name,
    email,
    'Migrado desde usuario' as estado
FROM users 
ORDER BY id;

-- Verificar que los datos coinciden
SELECT 
    'VERIFICACIÓN DE COINCIDENCIA' as verificacion,
    (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') as total_usuario_numericos,
    (SELECT COUNT(*) FROM users) as total_users_migrados,
    CASE 
        WHEN (SELECT COUNT(*) FROM usuario WHERE numero_documento REGEXP '^[0-9]+$') = (SELECT COUNT(*) FROM users)
        THEN '✅ COINCIDEN'
        ELSE '❌ NO COINCIDEN'
    END as estado_verificacion;
