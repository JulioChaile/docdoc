DROP PROCEDURE IF EXISTS `dsp_buscar_nominaciones`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_nominaciones`(pIdJuzgado int, pCadena varchar(100), pIncluyeBajas char(1))
PROC: BEGIN
	/*
    Permite buscar nominaciones filtrándolas por juzgado (pJuzgado = 0 para todas), 
    una cadena de búsqueda e indicando si se incluyen o no las dadas de baja. 
    Ordena por Nominacion.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN ('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		n.*, j.Juzgado, ju.Jurisdiccion
    FROM		Nominaciones n
    INNER JOIN	Juzgados j USING (IdJuzgado)
    INNER JOIN	Jurisdicciones ju USING (IdJurisdiccion)
    WHERE		(j.IdJuzgado = pIdJuzgado OR pIdJuzgado = 0) AND
				(pIncluyeBajas = 'S' OR n.Estado = 'A') AND
				Nominacion LIKE CONCAT('%',pCadena,'%')
	ORDER BY	ju.Jurisdiccion, j.Juzgado, n.Nominacion;
    
END $$
DELIMITER ;
