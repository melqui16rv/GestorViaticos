# INSTRUCCIONES PARA IMPLEMENTAR DISPARADORES DE SINCRONIZACIÓN

## 📋 Resumen
Los disparadores sincronizarán automáticamente los datos entre:
- **Tabla `users`** (Sistema Laravel - Gestión de Cuentas)
- **Tabla `usuario`** (Sistema Viáticos)

## 🔄 Mapeo de Campos
| Campo en `users` | Campo en `usuario` | Descripción |
|------------------|-------------------|-------------|
| `id` | `numero_documento` | ID convertido a VARCHAR |
| `name` | `nombre_completo` | Nombre completo del usuario |
| `email` | `email` | Correo electrónico |
| `password` | `contraseña` | Contraseña encriptada |

## 🛠️ Pasos de Implementación

### 1. Preparación Previa
```sql
-- Asegúrate de que exista el rol por defecto
INSERT IGNORE INTO roles_app (id_rol, nombre_rol) 
VALUES ('001', 'Usuario Básico');
```

### 2. Ejecutar los Disparadores
```bash
# Conectar a tu base de datos y ejecutar:
mysql -u tu_usuario -p tu_base_datos < disparadores.sql
```

### 3. Verificar Instalación
```sql
-- Ver disparadores instalados
SHOW TRIGGERS LIKE 'sync_%';

-- Debería mostrar:
-- sync_user_insert
-- sync_user_update
```

## 🧪 Testing

### Caso 1: Insertar Usuario Nuevo
```sql
-- En la tabla users
INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES ('Juan Pérez', 'juan@example.com', '$2y$10$hashedpassword', NOW(), NOW());

-- Verificar que se creó en usuario
SELECT * FROM usuario WHERE email = 'juan@example.com';
```

### Caso 2: Actualizar Usuario Existente
```sql
-- Actualizar en users
UPDATE users 
SET name = 'Juan Carlos Pérez', email = 'juancarlos@example.com' 
WHERE email = 'juan@example.com';

-- Verificar actualización en usuario
SELECT nombre_completo, email FROM usuario 
WHERE email = 'juancarlos@example.com';
```

## 🔧 Configuraciones Opcionales

### Activar Sincronización Bidireccional
Si necesitas que los cambios en `usuario` también se reflejen en `users`:

```sql
-- Descomenta las líneas 82-130 en disparadores.sql
-- CUIDADO: Puede causar bucles infinitos si no se maneja bien
```

### Activar Eliminación Sincronizada
```sql
-- Descomenta las líneas 58-66 en disparadores.sql
-- Eliminar usuario de users también lo eliminará de usuario
```

## ⚠️ Consideraciones Importantes

### Valores por Defecto
Los disparadores usan estos valores por defecto para campos requeridos:
- `tipo_doc`: 'CC'
- `telefono`: 'Sin teléfono'
- `id_rol`: '001'

### Campos No Afectados
Los disparadores **NO** modifican:
- Estructura existente de tablas
- Relaciones foráneas existentes
- Índices existentes
- Tu código backend PHP

### Manejo de Errores
- Si el rol '001' no existe, la inserción fallará
- Si el `users.id` no es numérico, se convierte a VARCHAR automáticamente
- Los campos opcionales pueden ser NULL sin problemas

## 🛑 Comandos de Emergencia

### Desactivar Disparadores
```sql
DROP TRIGGER IF EXISTS sync_user_insert;
DROP TRIGGER IF EXISTS sync_user_update;
DROP TRIGGER IF EXISTS sync_user_delete;
```

### Ver Estado de Disparadores
```sql
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    TRIGGER_BODY
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = 'tu_base_datos' 
AND TRIGGER_NAME LIKE 'sync_%';
```

## 📊 Monitoreo

### Verificar Sincronización
```sql
-- Contar registros en ambas tablas
SELECT 
    (SELECT COUNT(*) FROM users) as users_count,
    (SELECT COUNT(*) FROM usuario) as usuario_count;

-- Ver usuarios sin sincronizar
SELECT u.id, u.name, u.email 
FROM users u 
LEFT JOIN usuario us ON CAST(u.id AS CHAR) = us.numero_documento 
WHERE us.numero_documento IS NULL;
```

## 🔄 Proceso de Rollback

Si necesitas revertir los cambios:

1. **Desactivar disparadores** (comandos arriba)
2. **Opcional**: Eliminar usuarios sincronizados
```sql
-- CUIDADO: Esto eliminará usuarios que se crearon por sincronización
DELETE FROM usuario 
WHERE numero_documento IN (
    SELECT CAST(id AS CHAR) FROM users
) AND tipo_doc = 'CC';
```

## 📞 Soporte

Los disparadores están diseñados para:
- ✅ No interferir con tu backend existente
- ✅ Mantener la integridad de datos
- ✅ Ser fáciles de activar/desactivar
- ✅ Proporcionar sincronización automática
- ✅ Usar valores por defecto seguros
