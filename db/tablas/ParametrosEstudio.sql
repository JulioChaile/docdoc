CREATE TABLE `ParametrosEstudio` (
  `IdEstudio` int(11) NOT NULL,
  `Parametro` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdEstudio`,`Parametro`),
  KEY `RefEmpresa77` (`Parametro`),
  CONSTRAINT `RefEmpresa77` FOREIGN KEY (`Parametro`) REFERENCES `Empresa` (`Parametro`),
  CONSTRAINT `RefEstudios78` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
