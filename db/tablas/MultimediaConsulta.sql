CREATE TABLE `MultimediaConsulta` (
  `IdMultimedia` int(11) NOT NULL,
  `IdConsulta` int(11) NOT NULL,
  PRIMARY KEY (`IdMultimedia`,`IdConsulta`),
  KEY `RefConsultas61` (`IdConsulta`),
  CONSTRAINT `RefConsultas61` FOREIGN KEY (`IdConsulta`) REFERENCES `Consultas` (`IdConsulta`),
  CONSTRAINT `RefMultimedia62` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
