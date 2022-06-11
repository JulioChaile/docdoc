CREATE TABLE `PlantillasEstudio` (
  `IdPlantilla` int(11) NOT NULL AUTO_INCREMENT,
  `IdEstudio` int(11) NOT NULL,
  `Nombre` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Plantilla` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `Actores` int(11) NOT NULL,
  `Demandados` int(11) NOT NULL,
  `IdCarpetaPadre` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdPlantilla`),
  UNIQUE KEY `IdPlantillas_UNIQUE` (`IdPlantilla`),
  KEY `fk_PlantillasEstudio_1_idx` (`IdEstudio`),
  KEY `fk_PlantillasEstudio_2_idx` (`IdCarpetaPadre`),
  CONSTRAINT `fk_PlantillasEstudio_1` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`),
  CONSTRAINT `fk_PlantillasEstudio_2` FOREIGN KEY (`IdCarpetaPadre`) REFERENCES `CarpetasPlantillasEstudio` (`IdCarpetaPlantilla`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
