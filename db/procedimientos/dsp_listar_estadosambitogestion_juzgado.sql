DROP PROCEDURE IF EXISTS `dsp_listar_estadosambitogestion_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_estadosambitogestion_juzgado`()
PROC: BEGIN
    SELECT		*
	FROM        Juzgados j
	LEFT JOIN   JuzgadosEstadosAmbitos jea USING(IdJuzgado)
	LEFT JOIN	EstadoAmbitoGestion eag USING(IdEstadoAmbitoGestion);
END $$
DELIMITER ;
