CREATE TABLE `UsuariosTokenAppCliente` (
  `IdUsuario` int(11) NOT NULL,
  `Usuario` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TokenApp` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdUsuario`),
  CONSTRAINT `fk_UsuariosTokenAppCliente_1` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
