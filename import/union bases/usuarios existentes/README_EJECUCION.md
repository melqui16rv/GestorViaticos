# 🚀 GUÍA DE EJECUCIÓN DE MIGRACIÓN - BASES DE DATOS VIÁTICOS

## ⚠️ IMPORTANTE - LEER ANTES DE EJECUTAR

Esta migración sincronizará las tablas `usuario` (sistema viáticos) y `users` (Laravel) de forma bidireccional.

### 📋 ORDEN DE EJECUCIÓN

**EJECUTAR EN ESTE ORDEN EXACTO:**

1. **0_verificar_estado_inicial.sql** - Verificar estado actual
2. **MIGRACION_COMPLETA.sql** - Ejecutar migración completa (paso a paso)
3. **6_prueba_funcionamiento.sql** - Probar sincronización

### 🎯 PROCESO PASO A PASO

#### PASO 1: Verificación Inicial
```sql
-- Ejecutar primero para ver el estado actual
SOURCE 0_verificar_estado_inicial.sql;
```

#### PASO 2: Migración Completa
```sql
-- Ejecutar sección por sección (NO todo junto)
-- Copiar y pegar cada sección individualmente
```

#### PASO 3: Prueba Final
```sql
-- Verificar que todo funciona correctamente
SOURCE 6_prueba_funcionamiento.sql;
```

### 🛡️ SEGURIDAD

- ✅ Se crean respaldos automáticos antes de cualquier cambio
- ✅ Los disparadores se desactivan durante la migración
- ✅ Se valida cada paso antes de continuar
- ✅ Rollback disponible en caso de problemas

### 📊 QUÉ HACE LA MIGRACIÓN

1. **Respaldo**: Crea tablas `usuario_backup` y `users_backup`
2. **Limpieza**: Desactiva disparadores temporalmente
3. **Migración**: Copia usuarios de `usuario` → `users`
4. **Sincronización**: Reactiva disparadores para sync automático
5. **Verificación**: Confirma que todo está sincronizado

### 🔄 SINCRONIZACIÓN FUTURA

Después de la migración, cualquier cambio en `users` se reflejará automáticamente en `usuario`:
- Nuevo usuario en `users` → Se crea en `usuario`
- Actualización en `users` → Se actualiza en `usuario`
- Eliminación en `users` → Se elimina en `usuario`

### 🆘 EN CASO DE PROBLEMAS

Si algo sale mal, puedes restaurar:
```sql
-- Restaurar tabla usuario
DROP TABLE usuario;
CREATE TABLE usuario AS SELECT * FROM usuario_backup;

-- Restaurar tabla users  
DROP TABLE users;
CREATE TABLE users AS SELECT * FROM users_backup;
```

### 📞 SIGUIENTE PASO

**Ejecutar ahora: 0_verificar_estado_inicial.sql**
