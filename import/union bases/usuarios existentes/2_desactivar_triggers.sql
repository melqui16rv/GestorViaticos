-- ================================================================
-- PASO 2: DESACTIVAR DISPARADORES TEMPORALMENTE
-- ================================================================
-- EJECUTAR SEGUNDO - Evitar que los disparadores interfieran durante la migración

-- Eliminar disparadores temporalmente (si existen)
DROP TRIGGER IF EXISTS sync_user_insert;
DROP TRIGGER IF EXISTS sync_user_update;
DROP TRIGGER IF EXISTS sync_user_delete;

-- Verificar que se eliminaron
SHOW TRIGGERS LIKE 'sync_%';

-- Debería mostrar: conjunto vacío (0 rows)
