DROP PROCEDURE IF EXISTS `dsp_buscar_estadoambitogestion`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_estadoambitogestion`(pCadena varchar(50))
BEGIN
	/*
    Permite buscar estados de ambito de gestion filtrándolos por una cadena de búsqueda.
    */
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		e.*
    FROM		EstadoAmbitoGestion e
    WHERE		e.EstadoAmbitoGestion LIKE CONCAT('%',pCadena,'%')
    ORDER BY    e.EstadoAmbitoGestion ASC;
END $$
DELIMITER ;
