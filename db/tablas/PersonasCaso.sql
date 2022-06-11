CREATE TABLE `PersonasCaso` (
  `IdCaso` bigint(20) NOT NULL,
  `IdPersona` int(11) NOT NULL,
  `IdRTC` smallint(6) DEFAULT NULL,
  `EsPrincipal` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ValoresParametros` json DEFAULT NULL,
  `DocumentacionSolicitada` json DEFAULT NULL,
  PRIMARY KEY (`IdCaso`,`IdPersona`),
  KEY `RefPersonas47` (`IdPersona`),
  KEY `RefRolesTipoCaso48` (`IdRTC`),
  CONSTRAINT `RefCasos49` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `RefPersonas47` FOREIGN KEY (`IdPersona`) REFERENCES `Personas` (`IdPersona`),
  CONSTRAINT `RefRolesTipoCaso48` FOREIGN KEY (`IdRTC`) REFERENCES `RolesTipoCaso` (`IdRTC`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
