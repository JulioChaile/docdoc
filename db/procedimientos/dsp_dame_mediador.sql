DROP PROCEDURE IF EXISTS `dsp_dame_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_mediador`(pIdMediador int)
BEGIN
	/*
    Permite instanciar una mediador desde la base de datos.
    */
    SELECT	    m.*, cm.IdChatMediador
    FROM	    Mediadores m
    LEFT JOIN   ChatsMediadores cm USING(IdMediador)
    WHERE	    m.IdMediador = pIdMediador;
END $$
DELIMITER ;
