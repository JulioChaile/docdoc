DROP PROCEDURE IF EXISTS `dsp_buscar_comisarias`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_comisarias`(pCadena varchar(50), pLimit int, pOffset int)
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    SET pCadena = COALESCE(pCadena,'');

	IF pOffset IS NULL OR pOffset = '' THEN
		SET pOffset = 0;
	END IF;
    IF pLimit IS NULL OR pLimit = '' OR pLimit = 0 THEN
        SET pLimit = (SELECT COUNT(*) FROM Comisarias);
    END IF;
    
    SELECT		*
    FROM		Comisarias
    WHERE		Comisaria LIKE CONCAT('%',pCadena,'%')
    ORDER BY    Comisaria
    LIMIT       pOffset, pLimit;
END $$
DELIMITER ;
