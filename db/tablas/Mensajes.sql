CREATE TABLE `Mensajes` (
  `IdMensaje` bigint AUTO_INCREMENT NOT NULL,
  `IdExternoMensaje` varchar(64) null,
  `IdChat` bigint not null,
  `Contenido` TEXT NOT NULL,
  -- IdUsuario not null indica que se mandó por un usuario de docdoc
  -- IdUsuario null indica que fue un mensaje enviado por el interlocutor
  `IdUsuario` INT NULL,
  `FechaEnviado` timestamp not null,
  `FechaRecibido` timestamp null,
  `FechaVisto` timestamp null,
  PRIMARY KEY (`IdMensaje`),
  UNIQUE KEY `UI_IdExternoMensaje` (`IdExternoMensaje`),
  KEY `RefChats100` (`IdChat`),
  KEY `RefUsuarios102` (`IdUsuario`),
  CONSTRAINT `RefChats100` FOREIGN KEY (`IdChat`) REFERENCES `Chats` (`IdChat`),
  CONSTRAINT `RefUsuarios102` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
