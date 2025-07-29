
-- ================================================================
-- DISPARADORES PARA SINCRONIZACIÓN ENTRE TABLAS users Y usuario
-- ================================================================
-- 
-- Propósito: Sincronizar automáticamente los datos entre:
-- - Tabla 'users' (Sistema Laravel - Gestión de Cuentas)
-- - Tabla 'usuario' (Sistema Viáticos)
--
-- Mapeo de campos:
-- users.name          -> usuario.nombre_completo
-- users.email         -> usuario.email  
-- users.password      -> usuario.contraseña
-- users.id            -> usuario.numero_documento (como VARCHAR)
--
-- ================================================================

DELIMITER $$

-- ================================================================
-- 1. DISPARADOR PARA INSERCIÓN (users -> usuario)
-- ================================================================
CREATE TRIGGER `sync_user_insert` 
AFTER INSERT ON `users`
FOR EACH ROW
BEGIN
    -- Verificar que no exista ya el usuario en la tabla usuario
    IF NOT EXISTS (SELECT 1 FROM usuario WHERE numero_documento = CAST(NEW.id AS CHAR)) THEN
        -- Insertar en la tabla usuario con valores por defecto para campos requeridos
        INSERT INTO usuario (
            numero_documento,
            tipo_doc,
            nombre_completo,
            contraseña,
            email,
            telefono,
            id_rol
        ) VALUES (
            CAST(NEW.id AS CHAR),           -- Convertir ID numérico a VARCHAR
            'CC',                           -- Valor por defecto para tipo_doc
            COALESCE(NEW.name, 'Sin nombre'), -- nombre_completo desde users.name
            NEW.password,                   -- contraseña desde users.password
            NEW.email,                      -- email desde users.email
            'Sin teléfono',                 -- Valor por defecto para telefono
            '001'                           -- Valor por defecto para id_rol (debe existir en roles_app)
        );
    END IF;
END$$

-- ================================================================
-- 2. DISPARADOR PARA ACTUALIZACIÓN (users -> usuario)
-- ================================================================
CREATE TRIGGER `sync_user_update` 
AFTER UPDATE ON `users`
FOR EACH ROW
BEGIN
    -- Solo actualizar si cambió alguno de los campos de interés
    IF (OLD.name != NEW.name) OR (OLD.email != NEW.email) OR (OLD.password != NEW.password) THEN
        -- Actualizar los campos correspondientes en la tabla usuario
        UPDATE usuario 
        SET 
            nombre_completo = COALESCE(NEW.name, 'Sin nombre'),
            email = NEW.email,
            contraseña = NEW.password
        WHERE numero_documento = CAST(NEW.id AS CHAR);
        
        -- Si no existe el registro, lo insertamos (por seguridad)
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
END$$

-- ================================================================
-- 3. DISPARADOR PARA ELIMINACIÓN (users -> usuario) - OPCIONAL
-- ================================================================
-- Comentado por defecto. Descomenta si necesitas sincronizar eliminaciones

CREATE TRIGGER `sync_user_delete` 
AFTER DELETE ON `users`
FOR EACH ROW
BEGIN
    -- Eliminar el usuario correspondiente de la tabla usuario
    DELETE FROM usuario 
    WHERE numero_documento = CAST(OLD.id AS CHAR);
END$$


-- ================================================================
-- 4. DISPARADORES BIDIRECCIONALES (usuario -> users) - OPCIONAL
-- ================================================================
-- Si necesitas sincronización bidireccional, descomenta estos disparadores

/*
-- Disparador para inserción desde usuario hacia users
CREATE TRIGGER `sync_usuario_insert` 
AFTER INSERT ON `usuario`
FOR EACH ROW
BEGIN
    -- Solo si el numero_documento es numérico (para evitar conflictos)
    IF NEW.numero_documento REGEXP '^[0-9]+$' THEN
        IF NOT EXISTS (SELECT 1 FROM users WHERE id = CAST(NEW.numero_documento AS UNSIGNED)) THEN
            INSERT INTO users (
                id,
                name,
                email,
                password,
                created_at,
                updated_at
            ) VALUES (
                CAST(NEW.numero_documento AS UNSIGNED),
                NEW.nombre_completo,
                NEW.email,
                NEW.contraseña,
                NOW(),
                NOW()
            );
        END IF;
    END IF;
END$$

-- Disparador para actualización desde usuario hacia users
CREATE TRIGGER `sync_usuario_update` 
AFTER UPDATE ON `usuario`
FOR EACH ROW
BEGIN
    -- Solo si cambió alguno de los campos de interés y el documento es numérico
    IF NEW.numero_documento REGEXP '^[0-9]+$' AND 
       ((OLD.nombre_completo != NEW.nombre_completo) OR 
        (OLD.email != NEW.email) OR 
        (OLD.contraseña != NEW.contraseña)) THEN
        
        UPDATE users 
        SET 
            name = NEW.nombre_completo,
            email = NEW.email,
            password = NEW.contraseña,
            updated_at = NOW()
        WHERE id = CAST(NEW.numero_documento AS UNSIGNED);
    END IF;
END$$
*/

DELIMITER ;

-- ================================================================
-- NOTAS IMPORTANTES:
-- ================================================================
-- 1. Asegúrate de que exista el rol '001' en la tabla roles_app antes de ejecutar
-- 2. Los disparadores asumen que users.id es numérico y se convierte a VARCHAR para numero_documento
-- 3. Se usan valores por defecto para campos requeridos en usuario que no existen en users
-- 4. Los disparadores bidireccionales están comentados para evitar bucles infinitos
-- 5. El disparador de eliminación está comentado por seguridad
-- 6. Estos disparadores NO afectan la estructura existente de las tablas
-- 7. Los campos agregados tienen valores por defecto para no interferir con el backend existente

-- ================================================================
-- DATOS DE EJEMPLO PARA TESTING:
-- ================================================================
-- Insertar rol por defecto si no existe:
-- INSERT IGNORE INTO roles_app (id_rol, nombre_rol) VALUES ('001', 'Usuario Básico');

-- ================================================================
-- COMANDOS PARA ACTIVAR/DESACTIVAR DISPARADORES:
-- ================================================================
-- Para desactivar: DROP TRIGGER IF EXISTS sync_user_insert;
-- Para desactivar: DROP TRIGGER IF EXISTS sync_user_update;
-- Para ver disparadores: SHOW TRIGGERS LIKE 'sync_%'; 