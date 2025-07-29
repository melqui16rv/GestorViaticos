-- Ver TODOS los disparadores de la base de datos
SHOW TRIGGERS;

-- Ver disparadores específicos si existen
SHOW TRIGGERS WHERE `Trigger` LIKE '%sync%';
SHOW TRIGGERS WHERE `Trigger` LIKE '%user%';

-- Ver información de las tablas
SHOW TABLES LIKE 'users';
SHOW TABLES LIKE 'usuario';

-- Ver estructura de las tablas (para verificar que existen)
DESCRIBE users;
DESCRIBE usuario;

