CREATE TABLE `Contactos` (
  `IdContacto` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `IdDocDoc` int(11) DEFAULT NULL,
  `Apynom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefonos` longblob NOT NULL,
  `Observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdContacto`,`IdUsuario`),
  UNIQUE KEY `UI_IdContacto` (`IdContacto`),
  KEY `RefUsuarios1` (`IdUsuario`),
  KEY `RefUsuarios3` (`IdDocDoc`),
  CONSTRAINT `RefUsuarios1` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`),
  CONSTRAINT `RefUsuarios3` FOREIGN KEY (`IdDocDoc`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
