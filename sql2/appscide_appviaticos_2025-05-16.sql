# ************************************************************
# Sequel Ace SQL dump
# Versión 20090
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Equipo: appscide.com (MySQL 5.5.5-10.11.11-MariaDB-cll-lve)
# Base de datos: appscide_appviaticos
# Tiempo de generación: 2025-05-16 21:25:29 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Volcado de tabla usuario
# ------------------------------------------------------------

DROP TABLE IF EXISTS `usuario`;

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

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;

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

/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
