CREATE TABLE `MensajesChatsMediadores` (
  `IdMensaje` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdExternoMensaje` varchar(65) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IdChatMediador` bigint(20) NOT NULL,
  `Contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `IdUsuario` int(11) DEFAULT NULL,
  `FechaEnviado` timestamp NOT NULL,
  `FechaRecibido` timestamp NULL DEFAULT NULL,
  `FechaVisto` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`IdMensaje`),
  KEY `fk_MensajesChatsMediadores_2_idx` (`IdUsuario`),
  KEY `fk_MensajesChatsMediadores_1_idx` (`IdChatMediador`),
  CONSTRAINT `fk_MensajesChatsMediadores_1` FOREIGN KEY (`IdChatMediador`) REFERENCES `ChatsMediadores` (`IdChatMediador`),
  CONSTRAINT `fk_MensajesChatsMediadores_2` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=461 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
