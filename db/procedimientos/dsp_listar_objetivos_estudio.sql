DROP PROCEDURE IF EXISTS `dsp_listar_objetivos_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_objetivos_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar los objetivos por defecto de un estudio. Ordena por EstadoCaso.
    */
    
    SELECT	*
    FROM	ObjetivosEstudio
    WHERE	IdEstudio = pIdEstudio;
END $$
DELIMITER ;