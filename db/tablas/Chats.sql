CREATE TABLE `Chats` (
  `IdChat` bigint AUTO_INCREMENT not null,
  `IdExternoChat` varchar(64) not null,
  `IdCaso` bigint(20) NOT NULL,
  `IdPersona` int(11) NOT NULL,
  -- Almaceno el telefono al que se envio el mensaje (la persona puede tener mas de 1 telefono)
  `Telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  -- Ultimo mensaje que el frontend confirmó haber recibido
  `IdUltimoMensajeLeido` bigint NULL,
  PRIMARY KEY (`IdChat`),
  UNIQUE KEY `UI_IdExternoChat` (`IdExternoChat`),
  KEY `RefPersonasCaso101` (`IdCaso`, `IdPersona`),
  KEY `IX_TelefonoChat` (`Telefono`),
  CONSTRAINT `RefPersonasCaso101` FOREIGN KEY (`IdCaso`,`IdPersona`) REFERENCES `PersonasCaso` (`IdCaso`,`IdPersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
