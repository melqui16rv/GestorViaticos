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
  `Tipo_Doc_Soporte_Compromiso` VARCHAR(50) DEFAULT NULL,
  `Num_Doc_Soporte_Compromiso` VARCHAR(50) DEFAULT NULL,
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

COMMIT;

-- Datos para la tabla `usuario`
INSERT INTO `usuario` (`numero_documento`, `tipo_doc`, `nombre_completo`, `contraseña`, `email`, `telefono`, `id_rol`) VALUES
('1007695451', 'Cédula de ciudadanía', 'Julian Camilo Piñeros Zubieta', 'a706dd0e51e05aef936f18a09eea26d247bf57388a8b065265bd775d99474282', 'jcpinerosz@sena.edu.co', '3015325123', '3'),
('259232', 'Cédula de ciudadanía', 'JCentro Industrial Y De Desarrollo Empresarial de Soacha', '92b908219a61e23981d568b0a3208f57dee6166f49d1c58d7953f1468c810050', 'juliancamilo290700@gmail.com', '3015325123', '1'),
('1073672380', 'Cédula de ciudadanía', 'Melqui Alexander Romero', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'melquiveru@gmail.com', '3026074008', '1'),
('80062448', 'Cédula de ciudadanía', 'Fabian Medina', '0f21fd791cea14dc5af4d236541a0be4961ab82132821b3399b84b210f4a7c8e', 'medinab@sena.edu.co', '123445', '2');


