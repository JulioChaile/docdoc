DROP PROCEDURE IF EXISTS `dsp_buscar_ciudades`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_ciudades`(pCadena varchar(50), pIdProvincia int)
BEGIN
	/*
    Permite buscar ciudades filtrándolas por una cadena de búsqueda. Ordena por Ciudad.
    */
    
    SELECT		c.*
    FROM		Ciudades c
    INNER JOIN	Provincias p USING (IdProvincia)
    WHERE		(pIdProvincia = 0 OR c.IdProvincia = pIdProvincia) AND
				c.Ciudad LIKE CONCAT('%', pCadena,'%') AND
                TRIM(c.Ciudad) != '' 
	ORDER BY	c.Ciudad;
END $$
DELIMITER ;
