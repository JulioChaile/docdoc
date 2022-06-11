CREATE TABLE `Estudios` (
  `IdEstudio` int(11) NOT NULL,
  `IdCiudad` int(11) NOT NULL,
  `Estudio` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Domicilio` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Especialidades` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdEstudio`),
  UNIQUE KEY `UI_Estudio` (`Estudio`),
  KEY `RefCiudades9` (`IdCiudad`),
  CONSTRAINT `RefCiudades9` FOREIGN KEY (`IdCiudad`) REFERENCES `Ciudades` (`IdCiudad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
