-- ================================================================
-- PASO 4: REACTIVAR DISPARADORES
-- ================================================================
-- EJECUTAR CUARTO - Reinstalar los disparadores para sincronización futura

DELIMITER $$

-- Disparador para inserción
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
            '7'
        );
    END IF;
END$$

-- Disparador para actualización
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
                '7'
            );
        END IF;
    END IF;
END$$

-- Disparador para eliminación (opcional)
CREATE TRIGGER sync_user_delete 
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    DELETE FROM usuario 
    WHERE numero_documento = CAST(OLD.id AS CHAR);
END$$

DELIMITER ;

-- Verificar que se reactivaron
SHOW TRIGGERS LIKE 'sync_%';
