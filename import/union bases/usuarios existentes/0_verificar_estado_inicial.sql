-- ================================================================
-- VERIFICACIÓN DEL ESTADO INICIAL ANTES DE LA MIGRACIÓN
-- ================================================================
-- EJECUTAR PRIMERO - Para verificar el estado actual

-- 1. Verificar si existen las tablas
SELECT 'VERIFICANDO TABLAS' as paso;

SHOW TABLES LIKE 'users';
SHOW TABLES LIKE 'usuario';

-- 2. Ver estructura de las tablas
SELECT 'ESTRUCTURA DE TABLAS' as paso;

DESCRIBE users;
DESCRIBE usuario;

-- 3. Contar registros actuales
SELECT 'CONTEO INICIAL' as paso;

SELECT 
    'usuario' as tabla,
    COUNT(*) as total_registros,
    COUNT(CASE WHEN numero_documento REGEXP '^[0-9]+$' THEN 1 END) as documentos_numericos
FROM usuario;

SELECT 
    'users' as tabla,
    COUNT(*) as total_registros
FROM users;

-- 4. Ver algunos registros de usuario
SELECT 'MUESTRA DE DATOS USUARIO' as paso;

SELECT 
    numero_documento,
    nombre_completo,
    email,
    contraseña,
    CASE 
        WHEN numero_documento REGEXP '^[0-9]+$' THEN 'NUMÉRICO ✅'
        ELSE 'NO NUMÉRICO ❌'
    END as tipo_documento
FROM usuario 
LIMIT 10;

-- 5. Verificar disparadores existentes
SELECT 'DISPARADORES ACTUALES' as paso;

SHOW TRIGGERS WHERE `Table` IN ('users', 'usuario');

-- 6. Ver si hay duplicados en numero_documento
SELECT 'VERIFICACIÓN DE DUPLICADOS' as paso;

SELECT 
    numero_documento,
    COUNT(*) as cantidad
FROM usuario 
WHERE numero_documento REGEXP '^[0-9]+$'
GROUP BY numero_documento
HAVING COUNT(*) > 1;
