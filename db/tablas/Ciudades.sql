CREATE TABLE `Ciudades` (
  `IdCiudad` int(11) NOT NULL,
  `IdProvincia` int(11) NOT NULL,
  `Ciudad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `CodPostal` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdCiudad`,`IdProvincia`),
  UNIQUE KEY `UI_IdCiudad` (`IdCiudad`),
  KEY `RefProvincias8` (`IdProvincia`),
  CONSTRAINT `RefProvincias8` FOREIGN KEY (`IdProvincia`) REFERENCES `Provincias` (`IdProvincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
