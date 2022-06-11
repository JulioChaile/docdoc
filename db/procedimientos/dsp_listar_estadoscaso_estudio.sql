DROP PROCEDURE IF EXISTS `dsp_listar_estadoscaso_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_estadoscaso_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar los Estados de Caso de un estudio. Ordena por EstadoCaso.
    */
    
    SELECT	*
    FROM	EstadosCaso
    WHERE	IdEstudio = pIdEstudio;
END $$
DELIMITER ;
