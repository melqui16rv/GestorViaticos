# Junie Guidelines for this project

## Language Preference
All interactions, explanations, and generated content from Junie should be in Spanish.
Please think and respond in Spanish for all tasks.

---
## **Explicación de como está funcionando el archivo** *config.php*:

### Como el aplicativo está desplegado en un hosting

El proyecto **GestorViaticos** está desplegado en un servidor de hosting compartido, por lo que tiene una configuración específica que **NO DEBE SER MODIFICADA**:

#### 🔧 Configuración actual en `/conf/config.php`:
```php
<?php
$_SERVER['DOCUMENT_ROOT'] = '/home/appscide/public_html/viaticosApp';
define('BASE_URL', '/viaticosApp/');
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/auth.php';
// ... resto de configuración
?>
```

#### ⚠️ **IMPORTANTE - No modificar rutas ni configuraciones**:
1. **DOCUMENT_ROOT**: Está configurado para el hosting en `/home/appscide/public_html/viaticosApp`
2. **BASE_URL**: Configurado como `/viaticosApp/` para el hosting
3. **Rutas absolutas**: Todos los `require_once` usan `$_SERVER['DOCUMENT_ROOT']` como base

#### 🚫 **Lo que NO se debe hacer**:
- ❌ Cambiar rutas a localhost o configuraciones locales
- ❌ Modificar `$_SERVER['DOCUMENT_ROOT']`
- ❌ Cambiar `BASE_URL`
- ❌ Sugerir ejecutar en local (el proyecto solo funciona en el hosting)

#### ✅ **Lo que SÍ se puede hacer**:
- ✅ Modificar lógica de negocio en archivos PHP
- ✅ Ajustar consultas SQL
- ✅ Corregir errores en el código
- ✅ Agregar nuevas funcionalidades
- ✅ Optimizar consultas y rendimiento

---

## **Estructura del proyecto**:

### 📁 Carpetas principales:
- `/app/` - Módulos principales (admin, gestor, presupuesto, SENNOVA)
- `/math/` - Clases de lógica de negocio y consultas
- `/conf/` - Configuración y autenticación
- `/sql/` - Conexión a base de datos
- `/assets/` - Recursos estáticos (CSS, JS, imágenes)
- `/public/` - Componentes compartidos (header, nav, footer)

### 🗄️ Base de datos:
El proyecto usa **MySQL/MariaDB** con tablas principales:
- `cdp` - Certificados de Disponibilidad Presupuestal
- `crp` - Certificados de Registro Presupuestal  
- `op` - Órdenes de Pago
- `usuario` - Usuarios del sistema

### 🔐 Sistema de autenticación:
- Manejo de roles mediante `requireRole(['3'])` 
- Sesiones PHP para control de acceso
- Roles específicos para diferentes módulos

---

## **Patrones comunes en el código**:

### 📊 Consultas AJAX:
```javascript
$.ajax({
    url: './control/ajaxGestor.php',
    method: 'GET',
    data: filtros,
    dataType: 'json',
    success: function(response) { /* ... */ }
});
```

### 🔍 Filtros dinámicos:
- Uso de cookies para persistir filtros
- Filtros por fecha, estado, beneficiario, etc.
- Paginación con LIMIT y OFFSET

### 📈 Gráficas:
- Uso de Chart.js para visualizaciones
- Datos calculados desde PHP y pasados a JavaScript
- Gráficas de tipo pie/donut para presupuestos

---

## **Errores comunes a evitar**:

1. **Precedencia de operadores SQL**: Siempre agrupar condiciones OR en paréntesis
2. **Índices de arrays**: Verificar que existan antes de acceder (`$array['key'] ?? ''`)
3. **Constantes no definidas**: Asegurar que BASE_URL esté definida
4. **Tipos de datos**: Usar `intval()` y `floatval()` para conversiones seguras
5. **Escape de datos**: Usar `htmlspecialchars()` para mostrar datos en HTML

---

## **Debugging en producción**:
- Usar `error_log()` para debug (los logs aparecen en `error_log` del hosting)
- **NO** usar `var_dump()` o `print_r()` directamente en respuestas AJAX
- Usar console.log en JavaScript para debug del frontend