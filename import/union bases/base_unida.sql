SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ================================================================
-- 1. CREACIÓN DE TABLAS - ESTRUCTURA SIN LLAVES
-- ================================================================

-- Tabla: roles_app
CREATE TABLE `roles_app` (
  `id_rol` VARCHAR(10) NOT NULL,
  `nombre_rol` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: usuario
CREATE TABLE `usuario` (
  `numero_documento` VARCHAR(79) NOT NULL,
  `tipo_doc` VARCHAR(100) NOT NULL,
  `nombre_completo` VARCHAR(300) DEFAULT NULL,
  `contraseña` VARCHAR(200) DEFAULT NULL,
  `email` VARCHAR(200) NOT NULL,
  `telefono` VARCHAR(50) NOT NULL,
  `id_rol` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cdp
CREATE TABLE `cdp` (
  `cdp_id` VARCHAR(255) NOT NULL,
  `CODIGO_CDP` VARCHAR(55) NOT NULL,
  `Numero_Documento` VARCHAR(55) DEFAULT NULL,
  `Fecha_de_Registro` DATE DEFAULT NULL,
  `Fecha_de_Creacion` DATETIME DEFAULT NULL,
  `Estado` VARCHAR(255) DEFAULT NULL,
  `Dependencia` VARCHAR(255) DEFAULT NULL,
  `Rubro` TEXT DEFAULT NULL,
  `Fuente` VARCHAR(100) DEFAULT NULL,
  `Recurso` VARCHAR(255) DEFAULT NULL,
  `Valor_Inicial` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Operaciones` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Actual` DECIMAL(15,2) DEFAULT NULL,
  `Saldo_por_Comprometer` DECIMAL(15,2) DEFAULT NULL,
  `Objeto` TEXT DEFAULT NULL,
  `Compromisos` TEXT DEFAULT NULL,
  `Cuentas_por_Pagar` TEXT DEFAULT NULL,
  `Obligaciones` TEXT DEFAULT NULL,
  `Ordenes_de_Pago` TEXT DEFAULT NULL,
  `Reintegros` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: crp
CREATE TABLE `crp` (
  `rp_id` VARCHAR(55) NOT NULL,
  `cdp_id` VARCHAR(55) NOT NULL,
  `CODIGO_CRP` VARCHAR(55) NOT NULL,
  `CODIGO_CDP` VARCHAR(55) NOT NULL,
  `Numero_Documento` VARCHAR(55) DEFAULT NULL,
  `Fecha_de_Registro` DATE DEFAULT NULL,
  `Fecha_de_Creacion` DATETIME DEFAULT NULL,
  `Estado` VARCHAR(255) DEFAULT NULL,
  `Dependencia` VARCHAR(255) DEFAULT NULL,
  `Rubro` TEXT DEFAULT NULL,
  `Descripcion` TEXT DEFAULT NULL,
  `Fuente` VARCHAR(100) DEFAULT NULL,
  `Valor_Inicial` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Operaciones` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Actual` DECIMAL(15,2) DEFAULT NULL,
  `Saldo_por_Utilizar` DECIMAL(15,2) DEFAULT NULL,
  `Tipo_Identificacion` VARCHAR(255) DEFAULT NULL,
  `Identificacion` VARCHAR(255) DEFAULT NULL,
  `Nombre_Razon_Social` VARCHAR(255) DEFAULT NULL,
  `Medio_de_Pago` VARCHAR(255) DEFAULT NULL,
  `Tipo_Cuenta` VARCHAR(255) DEFAULT NULL,
  `Numero_Cuenta` VARCHAR(255) DEFAULT NULL,
  `Estado_Cuenta` VARCHAR(255) DEFAULT NULL,
  `Entidad_Nit` VARCHAR(255) DEFAULT NULL,
  `Entidad_Descripcion` TEXT DEFAULT NULL,
  `Solicitud_CDP` VARCHAR(55) DEFAULT NULL,
  `CDP` VARCHAR(55) DEFAULT NULL,
  `Compromisos` VARCHAR(55) DEFAULT NULL,
  `Cuentas_por_Pagar` TEXT DEFAULT NULL,
  `Obligaciones` TEXT DEFAULT NULL,
  `Ordenes_de_Pago` TEXT DEFAULT NULL,
  `Reintegros` TEXT DEFAULT NULL,
  `Fecha_Documento_Soporte` DATETIME DEFAULT NULL,
  `Tipo_Documento_Soporte` VARCHAR(255) DEFAULT NULL,
  `Numero_Documento_Soporte` VARCHAR(255) DEFAULT NULL,
  `Observaciones` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: op
CREATE TABLE `op` (
  `op_id` VARCHAR(55) NOT NULL,
  `rp_id` VARCHAR(55) NOT NULL,
  `cdp_id` VARCHAR(55) NOT NULL,
  `CODIGO_OP` VARCHAR(55) NOT NULL,
  `CODIGO_CRP` VARCHAR(55) NOT NULL,
  `CODIGO_CDP` VARCHAR(55) NOT NULL,
  `Numero_Documento` VARCHAR(55) DEFAULT NULL,
  `Fecha_de_Registro` DATE DEFAULT NULL,
  `Fecha_de_Pago` DATETIME DEFAULT NULL,  
  `Estado` VARCHAR(255) DEFAULT NULL,
  `Valor_Bruto` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Deducciones` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Neto` DECIMAL(15,2) DEFAULT NULL,
  `Tipo Beneficiario` VARCHAR(255) DEFAULT NULL,
  `Vigencia Presupuestal` VARCHAR(155) DEFAULT NULL,
  `Tipo_Identificacion` VARCHAR(255) DEFAULT NULL,
  `Identificacion` VARCHAR(255) DEFAULT NULL,
  `Nombre_Razon_Social` VARCHAR(255) DEFAULT NULL,
  `Medio_de_Pago` VARCHAR(255) DEFAULT NULL,
  `Tipo_Cuenta` VARCHAR(255) DEFAULT NULL,
  `Numero_Cuenta` VARCHAR(255) DEFAULT NULL,
  `Estado_Cuenta` VARCHAR(255) DEFAULT NULL,
  `Entidad_Nit` VARCHAR(255) DEFAULT NULL,
  `Entidad_Descripcion` TEXT DEFAULT NULL,
  `Dependencia` VARCHAR(255) DEFAULT NULL,
  `Dependencia_Descripcion` TEXT(255) DEFAULT NULL,
  `Rubro` TEXT DEFAULT NULL,
  `Descripcion` TEXT DEFAULT NULL,
  `Fuente` VARCHAR(100) DEFAULT NULL,
  `Recurso` VARCHAR(255) DEFAULT NULL,
  `Sit` VARCHAR(155) DEFAULT NULL,
  `Valor_Pesos` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Moneda` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Reintegrado_Pesos` DECIMAL(15,2) DEFAULT NULL,
  `Valor_Reintegrado_Moneda` DECIMAL(15,2) DEFAULT NULL,
  `Tesoreria_Pagadora` VARCHAR(100) DEFAULT NULL,
  `Identificacion_Pagaduria` VARCHAR(255) DEFAULT NULL,
  `Cuenta_Pagaduria` VARCHAR(255) DEFAULT NULL,
  `Endosada` VARCHAR(55) DEFAULT NULL,
  `Tipo_Identificacion2` VARCHAR(255) DEFAULT NULL,
  `Identificacion3` VARCHAR(255) DEFAULT NULL,
  `Razon_social` VARCHAR(255) DEFAULT NULL,
  `Numero_Cuenta4` VARCHAR(255) DEFAULT NULL,
  `Concepto_Pago` TEXT DEFAULT NULL,
  `Solicitud_CDP` VARCHAR(55) DEFAULT NULL,
  `CDP` VARCHAR(55) DEFAULT NULL,
  `Compromisos` VARCHAR(55) DEFAULT NULL,
  `Cuentas_por_Pagar` TEXT DEFAULT NULL,
  `Fecha_Cuentas_por_Pagar` DATE DEFAULT NULL,
  `Obligaciones` TEXT DEFAULT NULL,
  `Ordenes_de_Pago` TEXT DEFAULT NULL,
  `Reintegros` TEXT DEFAULT NULL,
  `Fecha_Doc_Soporte_Compromiso` DATE DEFAULT NULL,
  `Tipo_Doc_Soporte_Compromiso` VARCHAR(100) DEFAULT NULL,
  `Num_Doc_Soporte_Compromiso` VARCHAR(100) DEFAULT NULL,
  `Objeto_del_Compromiso` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: saldos_asignados
CREATE TABLE `saldos_asignados` (
  `ID_SALDO` INT NOT NULL,
  `NOMBRE_PERSONA` VARCHAR(255) NOT NULL,
  `DOCUMENTO_PERSONA` VARCHAR(55) NOT NULL,
  `FECHA_REGISTRO` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FECHA_INICIO` DATE NOT NULL,
  `FECHA_FIN` DATE NOT NULL,
  `FECHA_PAGO` DATE DEFAULT NULL,
  `SALDO_ASIGNADO` DECIMAL(15,2) NOT NULL,
  `rp_id` VARCHAR(55) NOT NULL,
  `cdp_id` VARCHAR(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: imagenes_saldos_asignados
CREATE TABLE `imagenes_saldos_asignados` (
  `ID_IMAGEN` INT NOT NULL,
  `ID_SALDO` INT NOT NULL,
  `NOMBRE_ORIGINAL` VARCHAR(255) NOT NULL,
  `RUTA_IMAGEN` VARCHAR(255) NOT NULL,
  `FECHA_SUBIDA` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: registros_actualizaciones
CREATE TABLE `registros_actualizaciones` (
  `id` INT NOT NULL,
  `tipo_tabla` ENUM('CDP', 'CRP', 'OP') NOT NULL,
  `nombre_archivo` VARCHAR(255) NOT NULL,
  `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registros_actualizados` INT NOT NULL DEFAULT 0,
  `registros_nuevos` INT NOT NULL DEFAULT 0,
  `usuario_id` VARCHAR(79) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: proyectos_tecnoparque
CREATE TABLE `proyectos_tecnoparque` (
  `id_PBT` INT NOT NULL,
  `tipo` ENUM('Tecnológico', 'Extensionismo') NOT NULL,
  `nombre_linea` VARCHAR(55) NOT NULL,
  `terminados` INT,
  `en_proceso` INT,
  `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: listadosvisitasApre
CREATE TABLE `listadosvisitasApre` (
  `id_visita` INT NOT NULL,
  `nodo` VARCHAR(100) DEFAULT 'Cundinamarca',
  `encargado` VARCHAR(155) NOT NULL,
  `numAsistentes` INT NOT NULL,
  `fechaCharla` DATETIME NOT NULL,
  `fecha_insert` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: asesoramiento
CREATE TABLE `asesoramiento` (
  `id_asesoramiendo` INT NOT NULL,
  `tipo` ENUM('Asociaciones', 'Cooperativa') NOT NULL,
  `encargadoAsesoramiento` VARCHAR(155) NOT NULL,
  `nombreEntidadImpacto` VARCHAR(155) NOT NULL,
  `fechaAsesoramiento` DATETIME NOT NULL,
  `fecha_insert` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: solicitudes_rol
CREATE TABLE `solicitudes_rol` (
  `id_solicitud` INT NOT NULL,
  `numero_documento` VARCHAR(79) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `id_rol_solicitado` VARCHAR(10) NOT NULL,
  `motivo` TEXT,
  `fecha_solicitud` DATETIME,
  `estado` ENUM('enviada', 'aceptada', 'rechazada') NOT NULL DEFAULT 'enviada',
  `fecha_respuesta` DATETIME DEFAULT NULL,
  `admin_respuesta` VARCHAR(79) NULL,
  `observaciones_admin` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 2. LLAVES PRIMARIAS Y FORÁNEAS
-- ================================================================

-- Llaves Primarias
ALTER TABLE `roles_app` ADD PRIMARY KEY (`id_rol`);
ALTER TABLE `usuario` ADD PRIMARY KEY (`numero_documento`);
ALTER TABLE `cdp` ADD PRIMARY KEY (`cdp_id`);
ALTER TABLE `crp` ADD PRIMARY KEY (`rp_id`);
ALTER TABLE `op` ADD PRIMARY KEY (`op_id`);
ALTER TABLE `saldos_asignados` ADD PRIMARY KEY (`ID_SALDO`);
ALTER TABLE `imagenes_saldos_asignados` ADD PRIMARY KEY (`ID_IMAGEN`);
ALTER TABLE `registros_actualizaciones` ADD PRIMARY KEY (`id`);
ALTER TABLE `proyectos_tecnoparque` ADD PRIMARY KEY (`id_PBT`);
ALTER TABLE `listadosvisitasApre` ADD PRIMARY KEY (`id_visita`);
ALTER TABLE `asesoramiento` ADD PRIMARY KEY (`id_asesoramiendo`);
ALTER TABLE `solicitudes_rol` ADD PRIMARY KEY (`id_solicitud`);

-- Configuración de AUTO_INCREMENT para las tablas que lo necesitan
ALTER TABLE `saldos_asignados` MODIFY `ID_SALDO` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `imagenes_saldos_asignados` MODIFY `ID_IMAGEN` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `registros_actualizaciones` MODIFY `id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `proyectos_tecnoparque` MODIFY `id_PBT` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `listadosvisitasApre` MODIFY `id_visita` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `asesoramiento` MODIFY `id_asesoramiendo` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `solicitudes_rol` MODIFY `id_solicitud` INT NOT NULL AUTO_INCREMENT;

-- Llaves Foráneas
ALTER TABLE `usuario` 
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles_app` (`id_rol`);

ALTER TABLE `saldos_asignados` 
  ADD CONSTRAINT `fk_saldos_crp` FOREIGN KEY (`rp_id`) REFERENCES `crp` (`rp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_saldos_cdp` FOREIGN KEY (`cdp_id`) REFERENCES `cdp` (`cdp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `imagenes_saldos_asignados` 
  ADD CONSTRAINT `fk_imagen_saldo` FOREIGN KEY (`ID_SALDO`) REFERENCES `saldos_asignados` (`ID_SALDO`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `registros_actualizaciones` 
  ADD CONSTRAINT `fk_registros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`numero_documento`);

ALTER TABLE `solicitudes_rol` 
  ADD CONSTRAINT `fk_solicitud_usuario` FOREIGN KEY (`numero_documento`) REFERENCES `usuario` (`numero_documento`),
  ADD CONSTRAINT `fk_solicitud_rol` FOREIGN KEY (`id_rol_solicitado`) REFERENCES `roles_app` (`id_rol`),
  ADD CONSTRAINT `fk_solicitud_admin` FOREIGN KEY (`admin_respuesta`) REFERENCES `usuario` (`numero_documento`);

-- ================================================================
-- 3. DISPARADORES (TRIGGERS)
-- ================================================================

-- Aquí se pueden agregar disparadores si son necesarios en el futuro
-- Ejemplo:
-- DELIMITER ;;
-- CREATE TRIGGER trigger_name
-- BEFORE/AFTER INSERT/UPDATE/DELETE ON table_name
-- FOR EACH ROW
-- BEGIN
--   -- Lógica del trigger
-- END;;
-- DELIMITER ;

-- ================================================================
-- 4. INSERTS DE DATOS
-- ================================================================

-- Datos para la tabla roles_app
INSERT INTO `roles_app` (`id_rol`, `nombre_rol`) VALUES
  ('1', 'Admin'),
  ('2', 'Gestor'),
  ('3', 'Presupuesto'),
  ('4', 'SENNOVA'),
  ('5', 'Tecnoparque'),
  ('6', 'Tecnoacademia'),
  ('7', 'Acceso'),
  ('otro', 'Otro');

-- Datos para la tabla usuario
INSERT INTO `usuario` (`numero_documento`, `tipo_doc`, `nombre_completo`, `contraseña`, `email`, `telefono`, `id_rol`)
VALUES
	('1010244141','Cédula de ciudadanía','Laura Lopez Rodriguez','$2y$10$pDbPQMkUEjzzkfTd4yPi5O812xUQyLyhZuXyH040eijTQ98oS2jte','laulopezr@sena.edu.co','3164101647','3'),
	('1030685664','Cédula de ciudadanía','Natalia Gonzalez','$2y$10$x8lvR7ryAQenFGTR6ICBneg7N.ybOMK0Qol6la4ikDSMQ/VycbpJa','nrgonzalez@sena.edu.co','3005675202','5'),
	('1073672380','Cédula de ciudadanía','Melqui Alexander Romero','$2y$10$u2iloUCRe9Bahko.YETDz.vHr/kOfdRWEZ6iIO5t/4923X8/r0fH6','melquiveru@gmail.com','3026074008','1'),
	('1111','Cédula de ciudadanía','SENNOVA ADMINISTRADOR','$2y$10$jNoWJl67VSkVt.avkTJtKu.LRB1A3xiZcgjcv4g8JZlxcAu.YRbBa','SENNOVA@ADMINISTRADOR.COM','3000','4'),
	('2222','Cédula de ciudadanía','Tecnoparque ADMINISTRADOR','$2y$10$zOlah79nlXDP95M7jFzv7.3nJh0qH9gfm86frtCzEakHV1edvRDZS','Tecnoparque@ADMINISTRADOR.com','2222','5'),
	('3333','Cédula de ciudadanía','Tecnoacademia ADMINISTRADOR','$2y$10$ONIqKEj/3gj7bSYQmVIlR.LdRIW0XBgzK.dna8QB4taR.4IMX.qry','Tecnoacademia@ADMINISTRADOR.com','3333','6'),
	('52366315','Cédula de ciudadanía','Carolina Cárdenas Herrera','$2y$10$TvAj3BiRQ6t62K7EKUZJ0OrfMY7Jb8br5EXCzmAgjLyxx2rXTwA0m','ccardenash@sena.edu.co','3168975203','4'),
	('80062448','Cédula de ciudadanía','Fabian Medina','$2y$10$0kJAeSsQH9h2FQ23A1ge1uduPyQA2ss7PsbeL10.8tRVWWJ2IGIi2','medinab@sena.edu.co','123445','3'),
	('80075242','Cédula de ciudadanía','Jonathan Cortazar-Camelo','$2y$10$XLyuJwgJ9W1KZBCJQdgJa.R.U/CtmKZgmkddslVb1J9igmaVM4gxi','jcortazar@sena.edu.co','6015461500','5'),
	('80153856','Cédula de ciudadanía','Juan Carlos Arias Chavarro','$2y$10$fNkr/T7MA2JW3x/bfF5z3./HdyBU0HRjV.xa8w/opNVsWlk35Q3aq','jariasc@sena.edu.co','3045766105','6');

-- Datos para la tabla proyectos_tecnoparque
INSERT INTO `proyectos_tecnoparque` (`tipo`, `nombre_linea`, `terminados`, `en_proceso`) VALUES
('Tecnológico','Diseño', 4, 21),
('Tecnológico','Producción',3, 14),
('Tecnológico','TI', 5, 4),
('Tecnológico','UCL', 0, 0),
('Extensionismo','Extensionismo', 0, 0);

-- Datos para la tabla listadosvisitasApre
INSERT INTO `listadosvisitasApre` (`encargado`, `numAsistentes`, `fechaCharla`) VALUES
('Juan Pérez', 25, '2024-05-15 10:00:00'),
('María Rodríguez', 30, '2024-05-16 14:30:00'),
('Carlos Gómez', 15, '2024-05-17 09:00:00');

-- Datos para la tabla asesoramiento
INSERT INTO `asesoramiento` (`tipo`, `encargadoAsesoramiento`, `nombreEntidadImpacto`, `fechaAsesoramiento`) VALUES
('Asociaciones','Melqui Romero', 'HolTecth', '2024-05-15 10:00:00'),
('Asociaciones','Angely Patiño', 'Markect Medios', '2024-05-15 10:00:00'),
('Cooperativa','Alan Patiño', 'Julio Cesar Turbay Ayala', '2024-05-15 10:00:00');

COMMIT;


-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 04-06-2025 a las 10:20:48
-- Versión del servidor: 9.2.0
-- Versión de PHP: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `laravel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint UNSIGNED NOT NULL,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint UNSIGNED NOT NULL,
  `area` enum('frontend','backend') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('info','danger','warning','success') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_histories`
--

CREATE TABLE `password_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `sort` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `two_factor_authentications`
--

CREATE TABLE `two_factor_authentications` (
  `id` bigint UNSIGNED NOT NULL,
  `authenticatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authenticatable_id` bigint UNSIGNED NOT NULL,
  `shared_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled_at` timestamp NULL DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `digits` tinyint UNSIGNED NOT NULL DEFAULT '6',
  `seconds` tinyint UNSIGNED NOT NULL DEFAULT '30',
  `window` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `algorithm` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sha1',
  `recovery_codes` json DEFAULT NULL,
  `recovery_codes_generated_at` timestamp NULL DEFAULT NULL,
  `safe_devices` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `active` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_be_logged_out` tinyint(1) NOT NULL DEFAULT '0',
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_log_name_index` (`log_name`),
  ADD KEY `subject` (`subject_id`,`subject_type`),
  ADD KEY `causer` (`causer_id`,`causer_type`);

--
-- Indices de la tabla `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `password_histories`
--
ALTER TABLE `password_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_parent_id_foreign` (`parent_id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `two_factor_authentications`
--
ALTER TABLE `two_factor_authentications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `2fa_auth_type_auth_id_index` (`authenticatable_type`,`authenticatable_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_histories`
--
ALTER TABLE `password_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `two_factor_authentications`
--
ALTER TABLE `two_factor_authentications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
