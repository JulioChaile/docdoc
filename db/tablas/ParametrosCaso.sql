CREATE TABLE `ParametrosCaso` (
  `IdCaso` bigint(20) NOT NULL,
  `Parametros` json DEFAULT NULL,
  PRIMARY KEY (`IdCaso`),
  CONSTRAINT `fk_ParametrosCaso_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
