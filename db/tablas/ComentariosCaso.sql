CREATE TABLE `ComentariosCaso` (
  `IdComentarioCaso` int(11) NOT NULL AUTO_INCREMENT,
  `Comentario` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaEnviado` datetime NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  PRIMARY KEY (`IdComentarioCaso`),
  KEY `fk_ComentariosCaso_1_idx` (`IdCaso`),
  KEY `fk_ComentariosCaso_2_idx` (`IdUsuario`),
  CONSTRAINT `fk_ComentariosCaso_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `fk_ComentariosCaso_2` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
