# Modulo y Procesos del Sistema GestorViáticos

## 📋 Descripción General

El **GestorViáticos** es un sistema web desarrollado en PHP para la gestión integral de viáticos y presupuestos, desplegado en un servidor de hosting compartido. El sistema maneja certificados de disponibilidad presupuestal (CDP), certificados de registro presupuestal (CRP) y órdenes de pago (OP).

---

## 🏗️ Arquitectura del Sistema

### Estructura de Directorios
```
GestorViaticos/
├── app/                    # Módulos principales de la aplicación
│   ├── admin/             # Módulo de administración
│   ├── gestor/            # Módulo de gestión operativa
│   ├── presupuesto/       # Módulo de presupuestos
│   └── SENNOVA/           # Módulo SENNOVA (3 submódulos)
├── math/                  # Clases de lógica de negocio
├── conf/                  # Configuración y autenticación
├── sql/                   # Conexión a base de datos
├── assets/                # Recursos estáticos (CSS, JS, imágenes)
└── public/                # Componentes compartidos
```

### Base de Datos
- **Motor**: MySQL/MariaDB
- **Tablas principales**:
  - `cdp` - Certificados de Disponibilidad Presupuestal
  - `crp` - Certificados de Registro Presupuestal
  - `op` - Órdenes de Pago
  - `usuario` - Usuarios del sistema

---

## 🔧 Módulos Principales

### 1. Módulo Admin (`/app/admin/`)
**Propósito**: Administración general del sistema y gestión de usuarios.

**Controladores principales**:
- `buscar_usuarios.php` - Búsqueda de usuarios en el sistema
- `formAgregarUsuario.php` - Formulario para agregar nuevos usuarios
- `formEditarUsuario.php` - Formulario para editar usuarios existentes
- `eliminar.php` - Eliminación de registros
- `panelControl.php` - Panel de control administrativo
- `procesarCDP.php` - Procesamiento de Certificados de Disponibilidad Presupuestal
- `procesarCRP.php` - Procesamiento de Certificados de Registro Presupuestal
- `procesarOP.php` - Procesamiento de Órdenes de Pago

**Funcionalidades**:
- ✅ Gestión completa de usuarios (CRUD)
- ✅ Procesamiento de documentos financieros
- ✅ Panel de control administrativo
- ✅ Control de acceso por roles

### 2. Módulo Gestor (`/app/gestor/`)
**Propósito**: Gestión operativa de viáticos y asignaciones presupuestales.

**Controladores principales**:
- `ajaxGestor.php` - Controlador AJAX para operaciones dinámicas
- `asignacion.php` - Gestión de asignaciones presupuestales
- `insert_saldo_asiganado.php` - Inserción de saldos asignados
- `procesar_saldo.php` - Procesamiento de saldos

**Funcionalidades**:
- ✅ Asignación de presupuestos
- ✅ Gestión de saldos
- ✅ Operaciones AJAX para interfaz dinámica
- ✅ Procesamiento de transacciones financieras

### 3. Módulo Presupuesto (`/app/presupuesto/`)
**Propósito**: Gestión y control de presupuestos de viáticos.

**Controladores principales**:
- `CRP_asociado.php` - Gestión de CRP asociados
- `PresupuestoTotal.php` - Cálculo de presupuesto total
- `Presupuesto_viaticos.php` - Presupuesto específico de viáticos
- `Presupuseto_viaticos_consumidos.php` - Control de viáticos consumidos
- `ajaxGestor.php` - Controlador AJAX específico del módulo
- `historialOP.php` - Historial de órdenes de pago

**Funcionalidades**:
- ✅ Control de presupuesto total
- ✅ Seguimiento de viáticos consumidos
- ✅ Gestión de CRP asociados
- ✅ Historial de órdenes de pago

### 4. Módulo SENNOVA (`/app/SENNOVA/`)
**Propósito**: Gestión especializada para programas SENNOVA.

**Submódulos**:
- **General** (`/app/SENNOVA/General/`)
  - `Graficas.php` - Generación de gráficas
  - `ViaticosGraficas.php` - Gráficas específicas de viáticos
  - `IndicadoresViaticos.php` - Indicadores y métricas
  - `dashboard_content.php` - Contenido del dashboard
  
- **Tecnoacademia** (`/app/SENNOVA/Tecnoacademia/`)
- **Tecnoparque** (`/app/SENNOVA/Tecnoparque/`)

**Funcionalidades**:
- ✅ Dashboard con indicadores
- ✅ Gráficas y visualizaciones
- ✅ Gestión específica por programa SENNOVA

---

## 🧮 Clases de Lógica de Negocio (`/math/`)

### Organización por Módulos
- `admin/` - Lógica para administración
- `gen/` - Clases generales del sistema
- `gestor/` - Lógica para gestión operativa
- `general_sennova/` - Lógica para SENNOVA general
- `planeacion/` - Lógica para planeación
- `tecnoacademia/` - Lógica específica de Tecnoacademia
- `tecnoparque/` - Lógica específica de Tecnoparque

### Clases Principales en `/math/gen/`
- `graficas.php` - Generación de gráficas y visualizaciones
- `metodosUsoTrigger.php` - Métodos para interacción con base de datos
- `user.php` - Gestión de usuarios y autenticación

---

## 🔐 Sistema de Autenticación

### Configuración (`/conf/`)
- `config.php` - Configuración principal del sistema
- `auth.php` - Sistema de autenticación y control de roles

### Funciones de Autenticación
- `isLoggedIn()` - Verificar si el usuario está autenticado
- `requireLogin()` - Requerir autenticación para acceder
- `requireRole($roles)` - Control de acceso por roles
- `requireNotRole($roles)` - Restricción de acceso por roles

### Manejo de Sesiones
- Variables de sesión: `$_SESSION['id_rol']`, `$_SESSION['role']`
- Redirección automática a login si no está autenticado
- Control granular de permisos por módulo

---

## 🎨 Frontend y Recursos

### Estructura de Assets (`/assets/`)
- `css/` - Estilos organizados por módulo
  - `admin/` - Estilos para administración
  - `gestor/` - Estilos para gestión
  - `presupuesto/` - Estilos para presupuestos
  - `sennova/` - Estilos para SENNOVA
  - `share/` - Estilos compartidos
- `js/` - JavaScript organizado por funcionalidad
- `img/` - Imágenes y recursos gráficos

### Tecnologías Frontend
- **jQuery** - Manipulación DOM y AJAX
- **Chart.js** - Gráficas y visualizaciones
- **Bootstrap** - Framework CSS (inferido por la estructura)
- **CSS personalizado** - Estilos específicos por módulo

---

## 📊 Patrones de Desarrollo

### Consultas AJAX
```javascript
$.ajax({
    url: './control/ajaxGestor.php',
    method: 'GET',
    data: filtros,
    dataType: 'json',
    success: function(response) {
        // Procesamiento de respuesta
    }
});
```

### Filtros Dinámicos
- Uso de cookies para persistir filtros
- Filtros por fecha, estado, beneficiario
- Paginación con LIMIT y OFFSET
- Búsqueda en tiempo real

### Visualización de Datos
- Gráficas tipo pie/donut para presupuestos
- Indicadores en tiempo real
- Dashboards interactivos
- Reportes exportables

---

## 🚀 Procesos para Implementar

### 1. Mejoras en Autenticación
**Prioridad**: Alta
- [ ] Unificar variables de sesión (`id_rol` vs `role`)
- [ ] Implementar logout seguro
- [ ] Agregar validación de sesiones expiradas
- [ ] Implementar recuperación de contraseñas

### 2. Optimización de Base de Datos
**Prioridad**: Alta
- [ ] Crear índices para consultas frecuentes
- [ ] Optimizar consultas con múltiples JOINs
- [ ] Implementar cache para consultas pesadas
- [ ] Agregar logs de auditoría

### 3. Mejoras en la Interfaz
**Prioridad**: Media
- [ ] Implementar diseño responsive completo
- [ ] Agregar notificaciones en tiempo real
- [ ] Mejorar UX en formularios largos
- [ ] Implementar modo oscuro

### 4. Funcionalidades Nuevas
**Prioridad**: Media
- [ ] Sistema de notificaciones por email
- [ ] Exportación de reportes a PDF/Excel
- [ ] API REST para integraciones
- [ ] Sistema de aprobaciones workflow

### 5. Seguridad y Rendimiento
**Prioridad**: Alta
- [ ] Implementar validación CSRF
- [ ] Sanitización de inputs SQL injection
- [ ] Compresión de assets CSS/JS
- [ ] Implementar rate limiting

### 6. Monitoreo y Logs
**Prioridad**: Media
- [ ] Sistema de logs estructurado
- [ ] Monitoreo de errores en tiempo real
- [ ] Métricas de uso del sistema
- [ ] Alertas automáticas por errores

---

## 🔧 Configuración de Desarrollo

### Requisitos del Sistema
- **PHP**: 7.4+
- **MySQL/MariaDB**: 5.7+
- **Servidor Web**: Apache/Nginx
- **Extensiones PHP**: mysqli, session, json

### Variables de Entorno
```php
$_SERVER['DOCUMENT_ROOT'] = '/home/appscide/public_html/viaticosApp';
define('BASE_URL', '/viaticosApp/');
```

### Debugging
- Usar `error_log()` para logs en producción
- Evitar `var_dump()` en respuestas AJAX
- Console.log para debugging frontend
- Logs disponibles en `error_log` de cada módulo

---

## 📝 Buenas Prácticas Implementadas

### Código PHP
- ✅ Separación de lógica en clases (`/math/`)
- ✅ Controladores organizados por módulo
- ✅ Sistema de autenticación centralizado
- ✅ Manejo de errores con logs

### Base de Datos
- ✅ Uso de prepared statements (inferido)
- ✅ Separación de consultas en clases específicas
- ✅ Nomenclatura consistente de tablas

### Frontend
- ✅ Organización de assets por módulo
- ✅ Uso de AJAX para operaciones dinámicas
- ✅ Separación de estilos y scripts
- ✅ Componentes reutilizables en `/public/`

---

## 🎯 Conclusiones

El sistema **GestorViáticos** presenta una arquitectura bien estructurada con separación clara de responsabilidades. Los módulos están organizados de manera lógica y cada uno maneja aspectos específicos del negocio. 

**Fortalezas**:
- Arquitectura modular bien definida
- Sistema de autenticación robusto
- Separación de lógica de negocio
- Interfaz dinámica con AJAX

**Áreas de Mejora**:
- Unificación de variables de sesión
- Optimización de consultas de base de datos
- Implementación de medidas de seguridad adicionales
- Mejoras en la experiencia de usuario

El sistema está listo para implementar las mejoras sugeridas y escalar según las necesidades del negocio.