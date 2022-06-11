CREATE TABLE `Comisarias` (
  `IdComisaria` int(11) NOT NULL AUTO_INCREMENT,
  `Comisaria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Domicilio` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdComisaria`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
