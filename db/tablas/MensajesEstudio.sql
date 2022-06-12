CREATE TABLE `MensajesEstudio` (
  `IdMensajeEstudio` int(11) NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MensajeEstudio` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `NombreTemplate` varchar(455) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`IdMensajeEstudio`),
  UNIQUE KEY `IdMensajeEstudio_UNIQUE` (`IdMensajeEstudio`),
  KEY `fk_MensajesEstudio_1_idx` (`IdEstudio`),
  CONSTRAINT `fk_MensajesEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
