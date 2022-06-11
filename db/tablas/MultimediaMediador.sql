CREATE TABLE `MultimediaMediador` (
  `IdMultimedia` int(11) NOT NULL,
  `IdMediador` int(11) NOT NULL,
  PRIMARY KEY (`IdMultimedia`,`IdMediador`),
  KEY `fk_MultimediaMediador_2_idx` (`IdMediador`),
  CONSTRAINT `fk_MultimediaMediador_1` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`),
  CONSTRAINT `fk_MultimediaMediador_2` FOREIGN KEY (`IdMediador`) REFERENCES `Mediadores` (`IdMediador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
