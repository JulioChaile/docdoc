DROP PROCEDURE IF EXISTS `dsp_buscar_estadoscaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_estadoscaso`(pCadena varchar(100), pIncluyeBajas char(1))
PROC: BEGIN
	/*
    Permite buscar estados de caso filtrándolos por una cadena de búsqueda e 
    indicando si se incluyen o no los dados de baja en pIncluyeBajas = [S|N]. 
    Ordena por EstadoCaso.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN ('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		*
    FROM		EstadosCaso
    WHERE		(pIncluyeBajas = 'S' OR Estado = 'A') AND
				EstadoCaso LIKE CONCAT('%',pCadena,'%')
	ORDER BY	EstadoCaso;
END $$
DELIMITER ;
