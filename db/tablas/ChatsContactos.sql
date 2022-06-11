CREATE TABLE `ChatsContactos` (
  `IdChatContacto` int(11) NOT NULL AUTO_INCREMENT,
  `IdExternoChat` varchar(65) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdContacto` int(11) NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdUltimoMensajeLeido` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`IdChatContacto`),
  KEY `fk_ChatsContacto_1_idx` (`IdContacto`),
  CONSTRAINT `fk_ChatsContacto_1` FOREIGN KEY (`IdContacto`) REFERENCES `ContactosEstudio` (`IdContacto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
