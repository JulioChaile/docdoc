DROP PROCEDURE IF EXISTS `dsp_dame_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_juzgado`(pIdJuzgado int)
PROC: BEGIN
	/*
    Permite instanciar un juzgado des de la base de datos.
    */
    SELECT		j.*, ju.Jurisdiccion,
                (
                    SELECT  JSON_REMOVE(COALESCE(JSON_OBJECTAGG(
                                COALESCE(IdEstadoAmbitoGestion, 0), COALESCE(JSON_OBJECT(
                                    'EstadoAmbitoGestion', eag.EstadoAmbitoGestion,
                                    'IdEstadoAmbitoGestion', eag.IdEstadoAmbitoGestion,
                                    'Mensaje', eag.Mensaje,
                                    'Orden', jea.Orden), JSON_OBJECT()
                                )),
                            JSON_OBJECT()), '$."0"')
                    FROM        EstadoAmbitoGestion eag
                    LEFT JOIN   JuzgadosEstadosAmbitos jea USING(IdEstadoAmbitoGestion)
                    WHERE       jea.IdJuzgado = pIdJuzgado
                    ORDER BY    jea.Orden
                ) EstadoAmbitoGestion
    FROM		Juzgados j
    INNER JOIN	Jurisdicciones ju USING (IdJurisdiccion)
    WHERE	    j.IdJuzgado = pIdJuzgado
    ;
END $$
DELIMITER ;
