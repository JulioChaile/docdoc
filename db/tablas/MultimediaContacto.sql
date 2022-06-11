CREATE TABLE `MultimediaContacto` (
  `IdMultimedia` int(11) NOT NULL,
  `IdContacto` int(11) NOT NULL,
  PRIMARY KEY (`IdMultimedia`,`IdContacto`),
  KEY `fk_MultimediaContacto_2_idx` (`IdContacto`),
  CONSTRAINT `fk_MultimediaContacto_1` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`),
  CONSTRAINT `fk_MultimediaContacto_2` FOREIGN KEY (`IdContacto`) REFERENCES `ContactosEstudio` (`IdContacto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
