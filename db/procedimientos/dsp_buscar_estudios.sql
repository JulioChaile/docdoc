DROP PROCEDURE IF EXISTS `dsp_buscar_estudios`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_estudios`(pCadena varchar(50), pIncluyeBajas char(1))
PROC: BEGIN
	/*
    Permite buscar Estudios filtrándolos por una cadena de búsqueda e 
    indicando si se incluyen o no los dados de baja. 
    Ordena por Estudio.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN ('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		e.*, c.Ciudad, p.Provincia
    FROM		Estudios e
    INNER JOIN	Ciudades c USING (IdCiudad)
    INNER JOIN 	Provincias p USING (IdProvincia)
	WHERE		Estudio LIKE CONCAT('%',pCadena,'%') AND
				(pIncluyeBajas = 'S' OR e.Estado = 'A')
	ORDER BY 	Estudio;
END $$
DELIMITER ;
