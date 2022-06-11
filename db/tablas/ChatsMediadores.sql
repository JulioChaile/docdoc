CREATE TABLE `ChatsMediadores` (
  `IdChatMediador` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdExternoChat` varchar(65) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdMediador` int(11) NOT NULL,
  `Telefono` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdUltimoMensajeLeido` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`IdChatMediador`),
  KEY `fk_ChatsMediadores_1_idx` (`IdMediador`),
  CONSTRAINT `fk_ChatsMediadores_1` FOREIGN KEY (`IdMediador`) REFERENCES `Mediadores` (`IdMediador`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
