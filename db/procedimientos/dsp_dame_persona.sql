DROP PROCEDURE IF EXISTS `dsp_dame_persona`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_persona`(pIdPersona int)
BEGIN
	/*
    Permite instanciar una persona de un estudio.
	*/
    SELECT 		*
    FROM		Personas
    WHERE		IdPersona = pIdPersona;
END $$
DELIMITER ;
