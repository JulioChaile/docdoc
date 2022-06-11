CREATE TABLE `EstadoAmbitoGestion` (
  `IdEstadoAmbitoGestion` int(11) NOT NULL AUTO_INCREMENT,
  `EstadoAmbitoGestion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mensaje` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`IdEstadoAmbitoGestion`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
