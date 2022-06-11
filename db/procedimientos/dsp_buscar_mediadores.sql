DROP PROCEDURE IF EXISTS `dsp_buscar_mediadores`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_mediadores`(pCadena varchar(50), pLimit int, pOffset int)
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    SET pCadena = COALESCE(pCadena,'');

	IF pOffset IS NULL OR pOffset = '' THEN
		SET pOffset = 0;
	END IF;
    IF pLimit IS NULL OR pLimit = '' OR pLimit = 0 THEN
        SET pLimit = (SELECT COUNT(*) FROM Mediadores);
    END IF;
    
    SELECT		m.*, c.IdChatMediador
    FROM		Mediadores m
    LEFT JOIN   ChatsMediadores c USING(IdMediador)
    WHERE		m.Nombre LIKE CONCAT('%',pCadena,'%')
    ORDER BY    m.Nombre ASC
    LIMIT       pOffset, pLimit;
END $$
DELIMITER ;
