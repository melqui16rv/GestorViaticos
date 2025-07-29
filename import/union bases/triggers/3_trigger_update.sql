DELIMITER $$

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

DELIMITER ;
