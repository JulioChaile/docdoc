DROP PROCEDURE IF EXISTS `dsp_buscar_juzgados`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_juzgados`(pIdJurisdiccion int, pCadena varchar(100), pIncluyeBajas char(1))
PROC: BEGIN
	/*
    Permite buscar juzgados indicando la jurisdicción (pIdJurisdiccion = 0 para todos), filtrándolos por una cadena de búsqueda 
    e indicando si se incluyen o no los dados de baja. 
    Ordena por Juzgado.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN ('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		j.*, ju.Jurisdiccion
    FROM		Juzgados j
    INNER JOIN	Jurisdicciones ju USING (IdJurisdiccion)
    WHERE		(pIdJurisdiccion = 0 OR j.IdJurisdiccion = pIdJurisdiccion) AND
				(pIncluyeBajas = 'S' OR j.Estado = 'A') AND
				(Juzgado LIKE CONCAT('%',pCadena,'%'))
	ORDER BY	ju.Jurisdiccion, j.Juzgado;
END $$
DELIMITER ;
