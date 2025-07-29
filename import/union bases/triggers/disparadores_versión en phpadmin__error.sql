
-- ================================================================
-- DISPARADORES PARA PhpMyAdmin - VERSIÓN COMPATIBLE
-- ================================================================
-- 
-- INSTRUCCIONES PARA PhpMyAdmin:
-- 1. Ejecuta CADA disparador POR SEPARADO
-- 2. NO ejecutes todo el archivo de una vez
-- 3. Copia y pega cada CREATE TRIGGER individualmente
-- 4. Asegúrate de que el rol '001' exista antes de ejecutar
--
-- Mapeo de campos:
-- users.name          -> usuario.nombre_completo
-- users.email         -> usuario.email  
-- users.password      -> usuario.contraseña
-- users.id            -> usuario.numero_documento (como VARCHAR)
--
-- ================================================================

-- PASO 1: Crear el rol por defecto (ejecutar primero)
INSERT IGNORE INTO roles_app (id_rol, nombre_rol) VALUES ('001', 'Usuario Básico');

-- ================================================================
-- PASO 2: DISPARADOR PARA INSERCIÓN (users -> usuario)
-- Copia y pega SOLO este bloque en PhpMyAdmin
-- ================================================================

CREATE TRIGGER sync_user_insert 
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE numero_documento = CAST(NEW.id AS CHAR)) THEN
        INSERT INTO usuario (
            numero_documento,
            tipo_doc,
            nombre_completo,
            contraseña,
            email,
            telefono,
            id_rol
        ) VALUES (
            CAST(NEW.id AS CHAR),
            'CC',
            COALESCE(NEW.name, 'Sin nombre'),
            NEW.password,
            NEW.email,
            'Sin teléfono',
            '001'
        );
    END IF;
END;

-- ================================================================
-- PASO 3: DISPARADOR PARA ACTUALIZACIÓN (users -> usuario)
-- Copia y pega SOLO este bloque en PhpMyAdmin
-- ================================================================

CREATE TRIGGER sync_user_update 
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF (OLD.name != NEW.name) OR (OLD.email != NEW.email) OR (OLD.password != NEW.password) THEN
        UPDATE usuario 
        SET 
            nombre_completo = COALESCE(NEW.name, 'Sin nombre'),
            email = NEW.email,
            contraseña = NEW.password
        WHERE numero_documento = CAST(NEW.id AS CHAR);
        
        IF ROW_COUNT() = 0 THEN
            INSERT INTO usuario (
                numero_documento,
                tipo_doc,
                nombre_completo,
                contraseña,
                email,
                telefono,
                id_rol
            ) VALUES (
                CAST(NEW.id AS CHAR),
                'CC',
                COALESCE(NEW.name, 'Sin nombre'),
                NEW.password,
                NEW.email,
                'Sin teléfono',
                '001'
            );
        END IF;
    END IF;
END;

-- ================================================================
-- PASO 4: DISPARADOR PARA ELIMINACIÓN (users -> usuario) - OPCIONAL
-- Copia y pega SOLO este bloque si quieres sincronizar eliminaciones
-- ================================================================

CREATE TRIGGER sync_user_delete 
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    DELETE FROM usuario 
    WHERE numero_documento = CAST(OLD.id AS CHAR);
END;

-- ================================================================
-- COMANDOS DE VERIFICACIÓN Y MANTENIMIENTO
-- ================================================================

-- Ver todos los disparadores instalados:
-- SHOW TRIGGERS LIKE 'sync_%';

-- Eliminar disparadores si es necesario:
-- DROP TRIGGER IF EXISTS sync_user_insert;
-- DROP TRIGGER IF EXISTS sync_user_update;
-- DROP TRIGGER IF EXISTS sync_user_delete;

-- Verificar que el rol existe:
-- SELECT * FROM roles_app WHERE id_rol = '001';

-- ================================================================
-- INSTRUCCIONES DE USO EN PhpMyAdmin:
-- ================================================================
-- 
-- 1. Ve a tu base de datos en PhpMyAdmin
-- 2. Haz clic en la pestaña "SQL"
-- 3. Ejecuta PRIMERO el INSERT del rol (PASO 1)
-- 4. Ejecuta cada CREATE TRIGGER por separado (PASOS 2, 3, 4)
-- 5. NO uses DELIMITER en PhpMyAdmin
-- 6. NO pegues todo el archivo de una vez
-- 
-- ORDEN DE EJECUCIÓN:
-- 1º) INSERT IGNORE INTO roles_app...
-- 2º) CREATE TRIGGER sync_user_insert...
-- 3º) CREATE TRIGGER sync_user_update...
-- 4º) CREATE TRIGGER sync_user_delete... (opcional)
--
-- ================================================================ 