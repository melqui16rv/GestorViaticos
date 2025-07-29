DELIMITER $$

CREATE TRIGGER sync_user_delete 
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    DELETE FROM usuario 
    WHERE numero_documento = CAST(OLD.id AS CHAR);
END$$

DELIMITER ;
