# 🔄 PROCESO DE MIGRACIÓN DE USUARIOS EXISTENTES

## 📋 RESUMEN
Este proceso migra los usuarios existentes de la tabla `usuario` hacia la tabla `users` para que funcionen con los disparadores de sincronización.

## ⚠️ IMPORTANTE - ORDEN DE EJECUCIÓN
**EJECUTAR EN ESTE ORDEN EXACTO:**

1. ✅ **Respaldo de seguridad**
2. ✅ **Desactivar disparadores temporalmente** 
3. ✅ **Migrar usuarios existentes a `users`**
4. ✅ **Reactivar disparadores**
5. ✅ **Verificar sincronización**
6. ✅ **Prueba de funcionamiento**

---

## 🔄 FLUJO DE MIGRACIÓN

### ANTES (Estado actual):
```
Usuarios existentes → [usuario] (registros manuales)
                     [users] (vacía)
```

### DESPUÉS (Estado final):
```
Nuevos usuarios → [users] → [disparadores] → [usuario] (sincronizado)
Usuarios existentes → [users] (migrados) → [usuario] (conservados)
```

---

## 📁 ARCHIVOS A EJECUTAR

### Archivo 1: `1_respaldo_seguridad.sql`
### Archivo 2: `2_desactivar_triggers.sql` 
### Archivo 3: `3_migrar_usuarios.sql`
### Archivo 4: `4_reactivar_triggers.sql`
### Archivo 5: `5_verificar_migracion.sql`
### Archivo 6: `6_prueba_funcionamiento.sql`

---

## 🚨 NOTAS IMPORTANTES

- ⚡ **No saltarse pasos** - El orden es crítico
- 🔒 **Hacer respaldo antes** de comenzar
- 🔄 **Los disparadores se desactivan temporalmente** para evitar duplicados
- 📊 **Verificar cada paso** antes de continuar
- 🔙 **Proceso reversible** si algo sale mal

---

## 🆘 PLAN DE CONTINGENCIA

Si algo sale mal:
1. Restaurar respaldo
2. Contactar soporte técnico
3. Revisar logs de errores

## 📞 SOPORTE

- Verificar que las tablas `users` y `usuario` existan
- Confirmar que los disparadores estén instalados
- Revisar permisos de base de datos
