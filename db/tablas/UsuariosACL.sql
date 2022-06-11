CREATE TABLE `UsuariosACL` (
  `IdACL` int(11) NOT NULL,
  `IdACLAPI` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdCalendario` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `Rol` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdACL`),
  UNIQUE KEY `IdACL_UNIQUE` (`IdACL`),
  KEY `fk_UsuariosACL_1_idx` (`IdCalendario`),
  KEY `fk_UsuariosACL_2_idx` (`IdUsuario`),
  CONSTRAINT `fk_UsuariosACL_1` FOREIGN KEY (`IdCalendario`) REFERENCES `CalendariosEstudio` (`IdCalendario`),
  CONSTRAINT `fk_UsuariosACL_2` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
