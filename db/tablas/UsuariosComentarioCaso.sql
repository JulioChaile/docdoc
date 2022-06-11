CREATE TABLE `UsuariosComentarioCaso` (
  `IdCaso` bigint(20) NOT NULL,
  `IdComentarioCaso` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `FechaVisto` datetime DEFAULT NULL,
  KEY `fk_new_table_2_idx` (`IdComentarioCaso`),
  KEY `fk_new_table_1_idx` (`IdCaso`),
  KEY `fk_new_table_3_idx` (`IdUsuario`),
  CONSTRAINT `fk_new_table_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `fk_new_table_2` FOREIGN KEY (`IdComentarioCaso`) REFERENCES `ComentariosCaso` (`IdComentarioCaso`),
  CONSTRAINT `fk_new_table_3` FOREIGN KEY (`IdUsuario`) REFERENCES `Usuarios` (`IdUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
