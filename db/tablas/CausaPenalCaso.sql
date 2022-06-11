CREATE TABLE `CausaPenalCaso` (
  `IdCausaPenalCaso` int(11) NOT NULL AUTO_INCREMENT,
  `IdCaso` bigint(20) NOT NULL,
  `EstadoCausaPenal` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NroExpedienteCausaPenal` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RadicacionCausaPenal` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Comisaria` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FechaEstadoCausaPenal` datetime DEFAULT NULL,
  PRIMARY KEY (`IdCausaPenalCaso`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
