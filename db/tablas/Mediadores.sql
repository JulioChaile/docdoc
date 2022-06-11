CREATE TABLE `Mediadores` (
  `IdMediador` int(11) NOT NULL AUTO_INCREMENT,
  `Registro` tinyint(4) NOT NULL,
  `Nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MP` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Domicilio` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Telefono` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdMediador`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
