DROP PROCEDURE IF EXISTS `dsp_dame_competencia`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_competencia`(pIdCompetencia smallint)
BEGIN
	/*
    Permite instanciar una competencia desde la base de datos.
    */
    SELECT	    c.*, JSON_REMOVE(COALESCE(JSON_OBJECTAGG(COALESCE(IdTipoCaso, 0), COALESCE(JSON_OBJECT('TipoCaso', t.TipoCaso, 'Estado', t.Estado), JSON_OBJECT())), JSON_OBJECT()), '$."0"') TiposCaso
    FROM	    Competencias c
    LEFT JOIN   CompetenciasTiposCaso ct USING(IdCompetencia)
    LEFT JOIN   TiposCaso t USING(IdTipoCaso)
    WHERE	    c.IdCompetencia = pIdCompetencia
    GROUP BY    c.IdCompetencia;
END $$
DELIMITER ;
