CREATE TABLE `DerivacionesConsultas` (
  `IdDerivacionConsulta` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `IdConsulta` int(11) NOT NULL,
  `FechaDerivacion` datetime NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdDerivacionConsulta`),
  KEY `RefConsultas15` (`IdConsulta`),
  KEY `RefEstudios16` (`IdEstudio`),
  CONSTRAINT `RefConsultas15` FOREIGN KEY (`IdConsulta`) REFERENCES `Consultas` (`IdConsulta`),
  CONSTRAINT `RefEstudios16` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
