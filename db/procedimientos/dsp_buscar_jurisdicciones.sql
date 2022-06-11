DROP PROCEDURE IF EXISTS `dsp_buscar_jurisdicciones`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_jurisdicciones`(pCadena varchar(100), pIncluyeBajas char(1))
PROC: BEGIN
	/*
    Permite buscar jurisdicciones filtrándolas por una cadena de búsqueda e indicando 
    si se incluyen o no las dadas de baja. Ordena por Jurisdiccion.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN ('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT 		*
    FROM		Jurisdicciones
    WHERE		(pIncluyeBajas = 'S' OR Estado = 'A') AND
				Jurisdiccion LIKE CONCAT('%',pCadena,'%')
	ORDER BY 	Jurisdiccion;

END $$
DELIMITER ;
