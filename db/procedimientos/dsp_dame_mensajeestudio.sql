DROP PROCEDURE IF EXISTS `dsp_dame_mensajeestudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_mensajeestudio`(pIdMensajeEstudio int)
BEGIN
	/*
    Permite instanciar un mensaje de un estudio desde la base de datos.
    */
    SELECT 	*
    FROM	MensajesEstudio
    WHERE	IdMensajeEstudio = pIdMensajeEstudio;
END $$
DELIMITER ;
