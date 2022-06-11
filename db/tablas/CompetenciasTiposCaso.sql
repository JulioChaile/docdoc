CREATE TABLE `CompetenciasTiposCaso` (
  `IdCompetencia` int(11) NOT NULL,
  `IdTipoCaso` smallint(6) NOT NULL,
  PRIMARY KEY (`IdCompetencia`,`IdTipoCaso`),
  KEY `fk_CompetenciasTiposCaso_2_idx` (`IdTipoCaso`),
  CONSTRAINT `fk_CompetenciasTiposCaso_1` FOREIGN KEY (`IdCompetencia`) REFERENCES `Competencias` (`IdCompetencia`),
  CONSTRAINT `fk_CompetenciasTiposCaso_2` FOREIGN KEY (`IdTipoCaso`) REFERENCES `TiposCaso` (`IdTipoCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
