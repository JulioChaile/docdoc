CREATE TABLE `CarpetasCaso` (
  `IdCarpetaCaso` int(11) NOT NULL AUTO_INCREMENT,
  `IdCaso` bigint(20) NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdCarpetaCaso`),
  KEY `fk_CarpetasCaso_1_idx` (`IdCaso`),
  CONSTRAINT `fk_CarpetasCaso_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
