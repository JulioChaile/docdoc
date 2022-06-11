CREATE TABLE `EventosMovimientos` (
  `IdEvento` int(11) NOT NULL,
  `IdMovimientoCaso` bigint(20) NOT NULL,
  PRIMARY KEY (`IdEvento`,`IdMovimientoCaso`),
  KEY `fk_EventosMovimientos_2_idx` (`IdMovimientoCaso`),
  CONSTRAINT `fk_EventosMovimientos_1` FOREIGN KEY (`IdEvento`) REFERENCES `Eventos` (`IdEvento`),
  CONSTRAINT `fk_EventosMovimientos_2` FOREIGN KEY (`IdMovimientoCaso`) REFERENCES `MovimientosCaso` (`IdMovimientoCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
