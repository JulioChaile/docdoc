CREATE TABLE `TiposCasosJuzgados` (
  `IdCompetencia` int(11) NOT NULL,
  `IdTipoCaso` smallint(6) NOT NULL,
  `IdJuzgado` int(11) NOT NULL,
  PRIMARY KEY (`IdCompetencia`,`IdTipoCaso`,`IdJuzgado`),
  KEY `fk_TiposCasosJuzgados_2_idx` (`IdJuzgado`),
  CONSTRAINT `fk_TiposCasosJuzgados_1` FOREIGN KEY (`IdCompetencia`, `IdTipoCaso`) REFERENCES `CompetenciasTiposCaso` (`IdCompetencia`, `IdTipoCaso`),
  CONSTRAINT `fk_TiposCasosJuzgados_2` FOREIGN KEY (`IdJuzgado`) REFERENCES `Juzgados` (`IdJuzgado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
