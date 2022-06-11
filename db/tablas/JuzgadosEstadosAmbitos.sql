CREATE TABLE `JuzgadosEstadosAmbitos` (
  `IdEstadoAmbitoGestion` int(11) NOT NULL,
  `IdJuzgado` int(11) NOT NULL,
  `Orden` int(2) NOT NULL,
  PRIMARY KEY (`IdEstadoAmbitoGestion`,`IdJuzgado`),
  CONSTRAINT `fk_JuzgadosEstadosAmbitos_1` FOREIGN KEY (`IdEstadoAmbitoGestion`) REFERENCES `EstadoAmbitoGestion` (`IdEstadoAmbitoGestion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
