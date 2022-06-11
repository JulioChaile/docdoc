CREATE TABLE `IdsCasosEstudio` (
  `IdCasoEstudio` bigint(20) NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  PRIMARY KEY (`IdCasoEstudio`),
  KEY `fk_IdsCasosEstudio_1_idx` (`IdCaso`),
  KEY `fk_IdsCasosEstudio_2_idx` (`IdEstudio`),
  CONSTRAINT `fk_IdsCasosEstudio_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `fk_IdsCasosEstudio_2` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
