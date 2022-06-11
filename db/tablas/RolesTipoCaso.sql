CREATE TABLE `RolesTipoCaso` (
  `IdRTC` smallint(6) NOT NULL,
  `IdTipoCaso` smallint(6) NOT NULL,
  `Rol` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Parametros` json DEFAULT NULL,
  PRIMARY KEY (`IdRTC`,`IdTipoCaso`),
  UNIQUE KEY `UI_IdRTC` (`IdRTC`),
  KEY `RefTiposCaso21` (`IdTipoCaso`),
  CONSTRAINT `RefTiposCaso21` FOREIGN KEY (`IdTipoCaso`) REFERENCES `TiposCaso` (`IdTipoCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
