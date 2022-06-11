CREATE TABLE `HistoriaClinicaPersonaCaso` (
  `IdHistoriaClinica` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdCaso` bigint(20) NOT NULL,
  `IdPersona` int(11) NOT NULL,
  `Estado` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Numero` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CentroMedico` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FechaEstadoHC` datetime DEFAULT NULL,
  PRIMARY KEY (`IdHistoriaClinica`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
