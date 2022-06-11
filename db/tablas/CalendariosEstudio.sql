CREATE TABLE `CalendariosEstudio` (
  `IdCalendario` int(11) NOT NULL AUTO_INCREMENT,
  `IdCalendarioAPI` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `Titulo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `IdColor` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdCalendario`),
  UNIQUE KEY `IdCalendario_UNIQUE` (`IdCalendario`),
  KEY `fk_CalendariosEstudio_1_idx` (`IdEstudio`),
  CONSTRAINT `fk_CalendariosEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
