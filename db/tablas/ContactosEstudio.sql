CREATE TABLE `ContactosEstudio` (
  `IdContacto` int(11) NOT NULL AUTO_INCREMENT,
  `IdEstudio` int(11) NOT NULL,
  `Apellidos` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tipo` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdContacto`),
  KEY `fk_ContactosEstudio_1_idx` (`IdEstudio`),
  CONSTRAINT `fk_ContactosEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
