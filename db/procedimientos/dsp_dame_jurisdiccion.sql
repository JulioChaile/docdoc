DROP PROCEDURE IF EXISTS `dsp_dame_jurisdiccion`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_jurisdiccion`(pIdJurisdiccion int)
PROC: BEGIN
	/*
    Permite instanciar una jurisdiccón desde la base de datos.
    */
    SELECT	*
    FROM	Jurisdicciones
    WHERE	IdJurisdiccion = pIdJurisdiccion;
END $$
DELIMITER ;
