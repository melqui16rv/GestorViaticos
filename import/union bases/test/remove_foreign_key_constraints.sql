-- ========================================
-- ELIMINACIÓN DE RESTRICCIONES FK
-- Sistema de Viáticos - Actualización de Perfil
-- ========================================

-- ⚠️ IMPORTANTE: Ejecutar en este orden exacto
-- Este script elimina las restricciones de clave foránea que causan conflictos
-- al actualizar numero_documento en la tabla usuario

-- ========================================
-- PASO 1: VERIFICAR RESTRICCIONES EXISTENTES
-- ========================================

-- Ver todas las restricciones FK en solicitudes_rol
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'union_prueba' 
    AND TABLE_NAME = 'solicitudes_rol' 
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ========================================
-- PASO 2: ELIMINAR RESTRICCIÓN FK PRINCIPAL
-- ========================================

-- Eliminar la restricción que causa el error principal
-- Esta es la que impide actualizar numero_documento en usuario
ALTER TABLE `solicitudes_rol` 
DROP FOREIGN KEY `fk_solicitud_usuario`;

-- ========================================
-- PASO 3: ELIMINAR RESTRICCIÓN FK SECUNDARIA
-- ========================================

-- Eliminar la restricción del campo admin_respuesta si existe
-- (Esta referencia también puede causar problemas)
ALTER TABLE `solicitudes_rol` 
DROP FOREIGN KEY `fk_solicitud_admin`;

-- ========================================
-- PASO 4: VERIFICAR ELIMINACIÓN
-- ========================================

-- Confirmar que las restricciones fueron eliminadas
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'union_prueba' 
    AND TABLE_NAME = 'solicitudes_rol' 
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Si el resultado está vacío, las restricciones fueron eliminadas exitosamente

-- ========================================
-- PASO 5: OPCIONAL - MANTENER ÍNDICES (RECOMENDADO)
-- ========================================

-- Aunque eliminamos las FK, es bueno mantener índices para performance
-- Solo ejecutar si no existen ya

-- Crear índice en numero_documento si no existe
CREATE INDEX IF NOT EXISTS `idx_solicitudes_numero_documento` 
ON `solicitudes_rol` (`numero_documento`);

-- Crear índice en admin_respuesta si no existe
CREATE INDEX IF NOT EXISTS `idx_solicitudes_admin_respuesta` 
ON `solicitudes_rol` (`admin_respuesta`);

-- ========================================
-- PASO 6: VERIFICACIÓN FINAL
-- ========================================

-- Verificar que los índices están presentes
SHOW INDEX FROM `solicitudes_rol` 
WHERE Key_name IN ('idx_solicitudes_numero_documento', 'idx_solicitudes_admin_respuesta');

-- ========================================
-- NOTAS IMPORTANTES:
-- ========================================

/*
✅ BENEFICIOS:
- Permite actualizar numero_documento sin restricciones
- Mantiene la funcionalidad del sistema
- Performance conservada con índices

⚠️ CONSIDERACIONES:
- La integridad referencial ahora debe manejarse a nivel de aplicación
- Los datos "huérfanos" son posibles pero manejables
- El sistema actual ya valida datos en PHP

🔄 PRÓXIMOS PASOS:
1. Ejecutar este script en la base de datos
2. Probar actualización de perfil
3. Verificar funcionamiento normal del sistema
*/
