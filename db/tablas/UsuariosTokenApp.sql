CREATE TABLE `UsuariosTokenApp` (
  `IdUsuario` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `TokenApp` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdUsuario`,`IdEstudio`),
  KEY `fk_UsuariosTokenApp_2_idx` (`IdEstudio`),
  KEY `index3` (`IdUsuario`),
  CONSTRAINT `fk_UsuariosTokenApp_1` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`),
  CONSTRAINT `fk_UsuariosTokenApp_2` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
