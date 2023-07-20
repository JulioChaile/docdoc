CREATE TABLE `Comunicados` (
  `IdComunicado` int NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Contenido` text COLLATE utf8mb4_unicode_ci,
  `IdMultimedia` int DEFAULT NULL,
  `FechaAlta` date DEFAULT NULL,
  `FechaComunicado` date DEFAULT NULL,
  `IdEstudio` int NOT NULL,
  PRIMARY KEY (`IdComunicado`),
  KEY `IdEstudio_idx` (`IdEstudio`),
  CONSTRAINT `IdEstudio` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
