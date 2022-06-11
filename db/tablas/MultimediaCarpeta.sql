CREATE TABLE `MultimediaCarpeta` (
  `IdMultimedia` int(11) NOT NULL,
  `IdCarpetaCaso` int(11) NOT NULL,
  PRIMARY KEY (`IdMultimedia`,`IdCarpetaCaso`),
  KEY `fk_MultimediaCarpeta_2_idx` (`IdCarpetaCaso`),
  CONSTRAINT `fk_MultimediaCarpeta_1` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`),
  CONSTRAINT `fk_MultimediaCarpeta_2` FOREIGN KEY (`IdCarpetaCaso`) REFERENCES `CarpetasCaso` (`IdCarpetaCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
