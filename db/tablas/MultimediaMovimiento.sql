CREATE TABLE `MultimediaMovimiento` (
  `IdMovimientoCaso` bigint(20) NOT NULL,
  `IdMultimedia` int(11) NOT NULL,
  PRIMARY KEY (`IdMovimientoCaso`,`IdMultimedia`),
  KEY `RefMultimedia65` (`IdMultimedia`),
  CONSTRAINT `RefMovimientosCaso66` FOREIGN KEY (`IdMovimientoCaso`) REFERENCES `MovimientosCaso` (`IdMovimientoCaso`),
  CONSTRAINT `RefMultimedia65` FOREIGN KEY (`IdMultimedia`) REFERENCES `Multimedia` (`IdMultimedia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
