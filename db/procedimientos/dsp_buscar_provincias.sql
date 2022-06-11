DROP PROCEDURE IF EXISTS `dsp_buscar_provincias`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_provincias`(pCadena varchar(50))
BEGIN
	/*
    Permite buscar provincias filtrándolas por una cadena de búsqueda.
    */
    SELECT		*	
    FROM		Provincias
    WHERE		Provincia LIKE CONCAT(pCadena,'%')
    ORDER BY	Provincia;
END $$
DELIMITER ;
