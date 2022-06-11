CREATE TABLE `MensajesEstudio` (
  `IdMensajeEstudio` int(11) NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MensajeEstudio` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  PRIMARY KEY (`IdMensajeEstudio`),
  UNIQUE KEY `IdMensajeEstudio_UNIQUE` (`IdMensajeEstudio`),
  KEY `fk_MensajesEstudio_1_idx` (`IdEstudio`),
  CONSTRAINT `fk_MensajesEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
