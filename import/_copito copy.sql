SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `cdp` (
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
  `Reintegros` DECIMAL(15,2) DEFAULT NULL,
  PRIMARY KEY (`CODIGO_CDP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `crp` (
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
  `Reintegros` DECIMAL(15,2) DEFAULT NULL,
  `Fecha_Documento_Soporte` DATETIME DEFAULT NULL,
  `Tipo_Documento_Soporte` VARCHAR(255) DEFAULT NULL,
  `Numero_Documento_Soporte` VARCHAR(255) DEFAULT NULL,
  `Observaciones` TEXT DEFAULT NULL,
  PRIMARY KEY (`CODIGO_CRP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `op` (
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
  `Reintegros` DECIMAL(15,2) DEFAULT NULL,
  `Fecha_Doc_Soporte_Compromiso` DATE DEFAULT NULL,
  `Tipo_Doc_Soporte_Compromiso` VARCHAR(100) DEFAULT NULL,
  `Num_Doc_Soporte_Compromiso` VARCHAR(100) DEFAULT NULL,
  `Objeto_del_Compromiso` TEXT DEFAULT NULL,

  PRIMARY KEY (`CODIGO_OP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario` (
  `numero_documento` varchar(79) NOT NULL,
  `tipo_doc` varchar(100) NOT NULL,
  `nombre_completo` varchar(300) DEFAULT NULL,
  `contraseña` varchar(200) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `id_rol` varchar(10) NOT NULL,
  PRIMARY KEY (`numero_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `saldos_asignados` (
  `ID_SALDO` INT NOT NULL AUTO_INCREMENT,
  `NOMBRE_PERSONA` VARCHAR(255) NOT NULL,
  `DOCUMENTO_PERSONA` VARCHAR(55) NOT NULL,
  `FECHA_REGISTRO` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FECHA_INICIO` DATE NOT NULL,
  `FECHA_FIN` DATE NOT NULL,
  `FECHA_PAGO` DATE DEFAULT NULL,
  `SALDO_ASIGNADO` DECIMAL(15,2) NOT NULL,
  `CODIGO_CRP` VARCHAR(55) NOT NULL,
  `CODIGO_CDP` VARCHAR(55) NOT NULL,

  PRIMARY KEY (`ID_SALDO`),
  CONSTRAINT `fk_saldos_crp` FOREIGN KEY (`CODIGO_CRP`) REFERENCES `crp` (`CODIGO_CRP`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_saldos_cdp` FOREIGN KEY (`CODIGO_CDP`) REFERENCES `cdp` (`CODIGO_CDP`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `imagenes_saldos_asignados` (
  `ID_IMAGEN` INT NOT NULL AUTO_INCREMENT,
  `ID_SALDO` INT NOT NULL,
  `NOMBRE_ORIGINAL` VARCHAR(255) NOT NULL,
  `RUTA_IMAGEN` VARCHAR(255) NOT NULL,
  `FECHA_SUBIDA` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`ID_IMAGEN`),
  CONSTRAINT `fk_imagen_saldo` FOREIGN KEY (`ID_SALDO`) REFERENCES `saldos_asignados` (`ID_SALDO`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `registros_actualizaciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo_tabla` ENUM('CDP', 'CRP', 'OP') NOT NULL,
  `nombre_archivo` VARCHAR(255) NOT NULL,
  `fecha_actualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registros_actualizados` INT NOT NULL DEFAULT 0,
  `registros_nuevos` INT NOT NULL DEFAULT 0,
  `usuario_id` VARCHAR(79) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_registros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`numero_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `proyectos_tecnologicos` (
  `id_PBT` INT NOT NULL AUTO_INCREMENT,
  `nombre_linea` varchar(55) NOT NULL,
  `terminados` int NOT NULL,
  `en_proceso` int NOT NULL,
  PRIMARY KEY (`id_PBT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `listadosvisitasApre` (
  `id_visita` INT NOT NULL AUTO_INCREMENT,
  `nodo` VARCHAR(100) DEFAULT 'Cundinamarca',
  `encargado` VARCHAR(155) NOT NULL,
  `numAsistentes` INT NOT NULL,
  `fechaCharla` DATETIME NOT NULL,
  `fecha_insert` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_visita`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- Datos para la tabla `usuario`
INSERT INTO `usuario` (`numero_documento`, `tipo_doc`, `nombre_completo`, `contraseña`, `email`, `telefono`, `id_rol`) VALUES
('1007695451', 'Cédula de ciudadanía', 'Julian Camilo Piñeros Zubieta', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'jcpinerosz@sena.edu.co', '3015325123', '3'),
('1010244141', 'Cédula de ciudadanía', 'Laura Lopez Rodriguez', '$2y$10$pDbPQMkUEjzzkfTd4yPi5O812xUQyLyhZuXyH040eijTQ98oS2jte', 'laulopezr@sena.edu.co', '3164101647', '3'),
('1073672380', 'Cédula de ciudadanía', 'Melqui Alexander Romero', '$2y$10$u2iloUCRe9Bahko.YETDz.vHr/kOfdRWEZ6iIO5t/4923X8/r0fH6', 'melquiveru@gmail.com', '3026074008', '1'),
('259232', 'Cédula de ciudadanía', 'JCentro Industrial Y De Desarrollo Empresarial de Soacha', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'juliancamilo290700@gmail.com', '3015325123', '1'),
('80062448', 'Cédula de ciudadanía', 'Fabian Medina', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'medinab@sena.edu.co', '123445', '2');

INSERT INTO `proyectos_tecnologicos` (`nombre_linea`, `terminados`, `en_proceso`) VALUES
('Diseño', 4, 21),
('Producción',3, 14),
('TI', 5, 4),
('UCL', 0, 0);

-- Datos para la tabla listadosvisitasApre
INSERT INTO `listadosvisitasApre` (`encargado`, `numAsistentes`, `fechaCharla`) VALUES
('Juan Pérez', 25, '2024-05-15 10:00:00'),
('María Rodríguez', 30, '2024-05-16 14:30:00'),
('Carlos Gómez', 15, '2024-05-17 09:00:00');