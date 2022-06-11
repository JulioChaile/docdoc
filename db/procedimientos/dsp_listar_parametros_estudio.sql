DROP PROCEDURE IF EXISTS `dsp_listar_parametros_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_parametros_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar las variables de empresa configuradas para un estudio.
    */
    SELECT	*
    FROM	Empresa
    WHERE	IdEstudio = pIdEstudio AND EsEditable = 'S';
END $$
DELIMITER ;
