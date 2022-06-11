CREATE TABLE `Eventos` (
  `IdEvento` int(11) NOT NULL AUTO_INCREMENT,
  `IdEventoAPI` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdCalendario` int(11) NOT NULL,
  `Titulo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `Comienzo` datetime NOT NULL,
  `Fin` datetime NOT NULL,
  `IdColor` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdEvento`),
  UNIQUE KEY `IdEvento_UNIQUE` (`IdEvento`),
  KEY `fk_Eventos_1_idx` (`IdCalendario`),
  CONSTRAINT `fk_Eventos_1` FOREIGN KEY (`IdCalendario`) REFERENCES `CalendariosEstudio` (`IdCalendario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
