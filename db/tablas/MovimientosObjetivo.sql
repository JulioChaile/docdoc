CREATE TABLE `MovimientosObjetivo` (
  `IdObjetivo` int(11) NOT NULL,
  `IdMovimientoCaso` bigint(20) NOT NULL,
  PRIMARY KEY (`IdObjetivo`,`IdMovimientoCaso`),
  KEY `RefMovimientosCaso81` (`IdMovimientoCaso`),
  CONSTRAINT `RefMovimientosCaso81` FOREIGN KEY (`IdMovimientoCaso`) REFERENCES `MovimientosCaso` (`IdMovimientoCaso`),
  CONSTRAINT `RefObjetivos82` FOREIGN KEY (`IdObjetivo`) REFERENCES `Objetivos` (`IdObjetivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
