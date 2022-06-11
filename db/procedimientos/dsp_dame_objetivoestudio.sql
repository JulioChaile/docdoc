DROP PROCEDURE IF EXISTS `dsp_dame_objetivoestudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_objetivoestudio`(pIdObjetivoEstudio int)
BEGIN
	/*
    Permite instanciar un estado de caso de un estudio desde la base de datos.
    */
    SELECT 	*
    FROM	ObjetivosEstudio
    WHERE	IdObjetivoEstudio = pIdObjetivoEstudio;
END $$
DELIMITER ;
