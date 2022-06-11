DROP PROCEDURE IF EXISTS `dsp_buscar_competencias`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_competencias`(pCadena varchar(50), pIncluyeBajas char(1))
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		c.*, JSON_REMOVE(COALESCE(JSON_OBJECTAGG(COALESCE(IdTipoCaso, 0), COALESCE(JSON_OBJECT('TipoCaso', t.TipoCaso, 'Estado', t.Estado), JSON_OBJECT())), JSON_OBJECT()), '$."0"') TiposCaso
    FROM		Competencias c
    LEFT JOIN   CompetenciasTiposCaso ct USING(IdCompetencia)
    LEFT JOIN   TiposCaso t USING(IdTipoCaso)
    WHERE		(pIncluyeBajas = 'S' OR (pIncluyeBajas = 'N' AND c.Estado = 'A')) AND
				c.Competencia LIKE CONCAT('%',pCadena,'%')
    GROUP BY    c.IdCompetencia;
END $$
DELIMITER ;
