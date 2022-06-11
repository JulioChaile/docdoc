CREATE TABLE `UsuariosEstudio` (
  `IdEstudio` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `IdEstudioPadre` int(11) DEFAULT NULL,
  `IdUsuarioPadre` int(11) DEFAULT NULL,
  `IdRolEstudio` int(11) NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdEstudio`,`IdUsuario`),
  KEY `RefUsuarios10` (`IdUsuario`),
  KEY `RefUsuariosEstudio11` (`IdEstudioPadre`,`IdUsuarioPadre`),
  KEY `RefRolesEstudio56` (`IdRolEstudio`),
  CONSTRAINT `RefEstudios12` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`),
  CONSTRAINT `RefRolesEstudio56` FOREIGN KEY (`IdRolEstudio`) REFERENCES `RolesEstudio` (`IdRolEstudio`),
  CONSTRAINT `RefUsuarios10` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`),
  CONSTRAINT `RefUsuariosEstudio11` FOREIGN KEY (`IdEstudioPadre`, `IdUsuarioPadre`) REFERENCES `UsuariosEstudio` (`IdEstudio`, `IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
