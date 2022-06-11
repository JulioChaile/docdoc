CREATE TABLE `MultimediaPersonaCaso` (
  `IdMultimedia` int(11) NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `IdPersona` int(11) NOT NULL,
  `Tipo` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdMultimedia`,`IdCaso`,`IdPersona`),
  KEY `fk_MultimediaPersonaCaso_2_idx` (`IdCaso`),
  KEY `fk_MultimediaPersonaCaso_3_idx` (`IdPersona`),
  CONSTRAINT `fk_MultimediaPersonaCaso_1` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`),
  CONSTRAINT `fk_MultimediaPersonaCaso_2` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `fk_MultimediaPersonaCaso_3` FOREIGN KEY (`IdPersona`) REFERENCES `Personas` (`IdPersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
