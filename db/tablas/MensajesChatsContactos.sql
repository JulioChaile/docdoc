CREATE TABLE `MensajesChatsContactos` (
  `IdMensaje` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdExternoMensaje` varchar(65) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IdChatContacto` int(11) NOT NULL,
  `Contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdUsuario` int(11) DEFAULT NULL,
  `FechaEnviado` timestamp NOT NULL,
  `FechaRecibido` timestamp NULL DEFAULT NULL,
  `FechaVisto` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`IdMensaje`),
  KEY `fk_MensajesChatsContactos_1_idx` (`IdChatContacto`),
  CONSTRAINT `fk_MensajesChatsContactos_1` FOREIGN KEY (`IdChatContacto`) REFERENCES `ChatsContactos` (`IdChatContacto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
