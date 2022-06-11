CREATE TABLE `EstadosCaso` (
  `IdEstadoCaso` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `EstadoCaso` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdEstadoCaso`),
  UNIQUE KEY `UI_IdEstudioEstadoCaso` (`IdEstudio`,`EstadoCaso`),
  CONSTRAINT `RefEstudios40` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
