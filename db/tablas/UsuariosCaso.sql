CREATE TABLE `UsuariosCaso` (
  `IdUsuarioCaso` int(11) NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `IdUsuario` int(11) DEFAULT NULL,
  `Permiso` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `EsCreador` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdUsuarioCaso`,`IdCaso`,`IdEstudio`),
  UNIQUE KEY `UI_IdUsuarioCaso` (`IdUsuarioCaso`),
  UNIQUE KEY `UI_IdCasoIdEstudioIdUsuario` (`IdCaso`,`IdEstudio`,`IdUsuario`),
  KEY `RefEstudios50` (`IdEstudio`),
  KEY `RefUsuarios51` (`IdUsuario`),
  CONSTRAINT `RefCasos37` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `RefEstudios50` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`),
  CONSTRAINT `RefUsuarios51` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
