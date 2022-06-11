DROP PROCEDURE IF EXISTS `dsp_contar_mediadores`;
DELIMITER $$
CREATE PROCEDURE `dsp_contar_mediadores`(pCadena varchar(50))
BEGIN
	/*
    Permite contar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		COUNT(*)
    FROM		Mediadores
    WHERE		Nombre LIKE CONCAT('%',pCadena,'%');
END $$
DELIMITER ;
