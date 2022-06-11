CREATE TABLE `MultimediaCaso` (
  `IdMultimedia` int(11) NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `OrigenMultimedia` char(1) COLLATE utf8mb4_unicode_ci NOT NULL, -- "M" Movimientos - "C" Caso - "R" Recibido por cliente / Chat
  PRIMARY KEY (`IdMultimedia`,`IdCaso`),
  KEY `fk_MultimediaCaso_1_idx` (`IdCaso`),
  CONSTRAINT `fk_MultimediaCaso_1` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`),
  CONSTRAINT `fk_MultimediaCaso_2` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
