DROP PROCEDURE IF EXISTS `dsp_listar_carpetas_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_carpetas_caso`(pIdCaso bigint)
BEGIN
	/*
    Permite listar los mensajes por defecto de un estudio.
    */
    
    SELECT	*
    FROM	CarpetasCaso
    WHERE	IdCaso = pIdCaso;
END $$
DELIMITER ;
