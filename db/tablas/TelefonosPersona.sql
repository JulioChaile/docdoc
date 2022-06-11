CREATE TABLE `TelefonosPersona` (
  `IdPersona` int(11) NOT NULL,
  `Telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaAlta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Detalle` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `EsPrincipal` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdPersona`,`Telefono`),
  CONSTRAINT `RefPersonas7` FOREIGN KEY (`IdPersona`) REFERENCES `Personas` (`IdPersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
