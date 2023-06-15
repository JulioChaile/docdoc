CREATE TABLE `TiposCaso` (
  `IdTipoCaso` smallint NOT NULL,
  `TipoCaso` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Color` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT '000000',
  PRIMARY KEY (`IdTipoCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;