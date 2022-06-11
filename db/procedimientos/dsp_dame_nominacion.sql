DROP PROCEDURE IF EXISTS `dsp_dame_nominacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_nominacion`(pIdNominacion int)
BEGIN
	/*
    Permite instanciar una nominación desde la base de datos.
    */
    SELECT		n.*, j.Juzgado, ju.Jurisdiccion
    FROM		Nominaciones n
    INNER JOIN	Juzgados j USING (IdJuzgado)
    INNER JOIN	Jurisdicciones ju USING (IdJurisdiccion)
    WHERE		n.IdNominacion = pIdNominacion;
END $$
DELIMITER ;
