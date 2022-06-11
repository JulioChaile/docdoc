DROP PROCEDURE IF EXISTS `dsp_listar_mensajes_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_mensajes_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar los mensajes por defecto de un estudio.
    */
    
    SELECT	*
    FROM	MensajesEstudio
    WHERE	IdEstudio = pIdEstudio;
END $$
DELIMITER ;
