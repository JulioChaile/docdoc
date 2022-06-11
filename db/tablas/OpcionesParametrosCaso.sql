CREATE TABLE `OpcionesParametrosCaso` (
  `IdOpcionesParametrosCaso` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Variable` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Opciones` json NOT NULL,
  PRIMARY KEY (`IdOpcionesParametrosCaso`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
