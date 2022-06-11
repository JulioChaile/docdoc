CREATE TABLE `EtiquetasCaso` (
  `IdEtiquetaCaso` int(11) NOT NULL AUTO_INCREMENT,
  `IdCaso` bigint(20) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `Etiqueta` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdEtiquetaCaso`),
  KEY `fk_Etiquetas_1_idx` (`IdCaso`),
  KEY `fk_Etiquetas_2_idx` (`IdEstudio`),
  CONSTRAINT `fk_Etiquetas_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `fk_Etiquetas_2` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
