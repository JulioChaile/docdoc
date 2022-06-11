CREATE TABLE `MovimientosClientes` (
  `IdMovimientoCaso` bigint(20) NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  PRIMARY KEY (`IdMovimientoCaso`,`IdCaso`),
  KEY `fk_MovimientosClientes_2_idx` (`IdCaso`),
  CONSTRAINT `fk_MovimientosClientes_1` FOREIGN KEY (`IdMovimientoCaso`) REFERENCES `MovimientosCaso` (`IdMovimientoCaso`),
  CONSTRAINT `fk_MovimientosClientes_2` FOREIGN KEY (`IdCaso`) REFERENCES `MovimientosCaso` (`IdCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
