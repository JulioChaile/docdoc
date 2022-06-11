CREATE TABLE `ObjetivosEstudio` (
  `IdObjetivoEstudio` int(11) NOT NULL,
  `ObjetivoEstudio` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  PRIMARY KEY (`IdObjetivoEstudio`),
  UNIQUE KEY `IdObjetivoEstudio_UNIQUE` (`IdObjetivoEstudio`),
  KEY `fk_ObjetivosEstudio_1_idx` (`IdEstudio`),
  CONSTRAINT `fk_ObjetivosEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
