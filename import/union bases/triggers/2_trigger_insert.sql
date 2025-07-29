DELIMITER $$

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

DELIMITER ;
