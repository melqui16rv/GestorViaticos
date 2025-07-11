# 🔧 GUÍA PASO A PASO PARA PhpMyAdmin

## 🚨 IMPORTANTE: Ejecutar UNO POR UNO

**❌ NO ejecutes todo el archivo de una vez**  
**✅ Copia y pega cada bloque por separado**

---

## 📋 PASO A PASO

### 🟢 PASO 1: Crear el rol por defecto
```sql
INSERT IGNORE INTO roles_app (id_rol, nombre_rol) VALUES ('001', 'Usuario Básico');
```

### 🟢 PASO 2: Disparador de inserción
```sql
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
```

### 🟢 PASO 3: Disparador de actualización
```sql
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
```

### 🟡 PASO 4: Disparador de eliminación (OPCIONAL)
```sql
CREATE TRIGGER sync_user_delete 
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    DELETE FROM usuario 
    WHERE numero_documento = CAST(OLD.id AS CHAR);
END;
```

---

## 🔍 VERIFICACIÓN

### Verificar que los disparadores se crearon:
```sql
SHOW TRIGGERS LIKE 'sync_%';
```

### Verificar que el rol existe:
```sql
SELECT * FROM roles_app WHERE id_rol = '001';
```

---

## ⚠️ SOLUCIÓN DE PROBLEMAS

### Si sale error de sintaxis:
1. **Verifica** que no hay espacios extra
2. **Copia EXACTAMENTE** cada bloque
3. **NO uses** comillas invertidas `` ` `` en PhpMyAdmin
4. **NO uses** DELIMITER en PhpMyAdmin

### Si no funciona la sincronización:
```sql
-- Ver estructura de la tabla users
DESCRIBE users;

-- Ver estructura de la tabla usuario  
DESCRIBE usuario;

-- Verificar datos de prueba
SELECT * FROM users LIMIT 5;
SELECT * FROM usuario LIMIT 5;
```

---

## 🧪 PRUEBA RÁPIDA

### 1. Insertar usuario de prueba:
```sql
INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES ('Test Usuario', 'test@example.com', 'password123', NOW(), NOW());
```

### 2. Verificar que se sincronizó:
```sql
SELECT * FROM usuario WHERE email = 'test@example.com';
```

### 3. Limpiar prueba:
```sql
DELETE FROM users WHERE email = 'test@example.com';
DELETE FROM usuario WHERE email = 'test@example.com';
```

---

## 🛑 ELIMINAR DISPARADORES (si es necesario)

```sql
DROP TRIGGER IF EXISTS sync_user_insert;
DROP TRIGGER IF EXISTS sync_user_update;
DROP TRIGGER IF EXISTS sync_user_delete;
```

---

## 📞 Contacto

Si tienes problemas, verifica:
1. ✅ Ejecutaste cada bloque por separado
2. ✅ El rol '001' existe en roles_app
3. ✅ Las tablas users y usuario existen
4. ✅ No hay errores de sintaxis en la copia
