CREATE TABLE `CarpetasPlantillasEstudio` (
  `IdCarpetaPlantilla` int(11) NOT NULL AUTO_INCREMENT,
  `IdEstudio` int(11) NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdCarpetaPadre` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdCarpetaPlantilla`),
  KEY `fk_CarpetasPlantillasEstudio_1_idx` (`IdEstudio`),
  KEY `fk_CarpetasPlantillasEstudio_2_idx` (`IdCarpetaPadre`),
  CONSTRAINT `fk_CarpetasPlantillasEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`),
  CONSTRAINT `fk_CarpetasPlantillasEstudio_2` FOREIGN KEY (`IdCarpetaPadre`) REFERENCES `CarpetasPlantillasEstudio` (`IdCarpetaPlantilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
