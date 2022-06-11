CREATE TABLE `aud_PersonasCaso` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `FechaAud` datetime NOT NULL,
  `UsuarioAud` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `IP` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `UserAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Aplicacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Motivo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TipoAud` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `IdPersona` int(11) NOT NULL,
  `IdRTC` smallint(6) DEFAULT NULL,
  `EsPrincipal` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ValoresParametros` json DEFAULT NULL,
  `DocumentacionSolicitada` json DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IX_FechaAud` (`FechaAud`),
  KEY `IX_Usuario` (`UsuarioAud`),
  KEY `IX_IP` (`IP`),
  KEY `IX_Aplicacion` (`Aplicacion`)
) ENGINE=InnoDB AUTO_INCREMENT=2076 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
