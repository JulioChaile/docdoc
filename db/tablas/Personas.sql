CREATE TABLE `Personas` (
  `IdPersona` int NOT NULL,
  `IdEstudio` int NOT NULL,
  `Tipo` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nombres` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Apellidos` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Documento` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Cuit` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Domicilio` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FechaAlta` datetime NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  PRIMARY KEY (`IdPersona`,`IdEstudio`),
  UNIQUE KEY `UI_IdPersona` (`IdPersona`),
  KEY `_idx` (`IdEstudio`),
  FULLTEXT KEY `index4` (`Domicilio`),
  CONSTRAINT `RefEstudios27` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
