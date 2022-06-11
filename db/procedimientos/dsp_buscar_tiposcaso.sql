DROP PROCEDURE IF EXISTS `dsp_buscar_tiposcaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_tiposcaso`(pCadena varchar(50), pIncluyeBajas char(1))
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		*
    FROM		TiposCaso
    WHERE		(pIncluyeBajas = 'S' OR (pIncluyeBajas = 'N' AND Estado = 'A')) AND
				TipoCaso LIKE CONCAT('%',pCadena,'%')
    ORDER BY 	TipoCaso;
END $$
DELIMITER ;
