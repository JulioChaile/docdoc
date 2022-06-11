DROP PROCEDURE IF EXISTS `dsp_buscar_contactos_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_contactos_estudio`(pCadena varchar(50), pIdEstudio int, pTipo char(1), pLimit int, pOffset int)
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    SET pCadena = COALESCE(pCadena,'');
    SET pTipo = COALESCE(pTipo,'');

	IF pOffset IS NULL OR pOffset = '' THEN
		SET pOffset = 0;
	END IF;
    IF pLimit IS NULL OR pLimit = '' OR pLimit = 0 THEN
        SET pLimit = (SELECT COUNT(*) FROM ContactosEstudio WHERE IdEstudio = pIdEstudio OR pIdEstudio = 0);
    END IF;
    
    SELECT		c.*, ch.IdChatContacto
    FROM		ContactosEstudio c
    LEFT JOIN   ChatsContactos ch USING(IdContacto)
    WHERE		(
                    c.Nombres LIKE CONCAT('%',pCadena,'%') OR
                    c.Apellidos LIKE CONCAT('%',pCadena,'%') OR
                    CONCAT(c.Nombres, ' ', c.Apellidos) LIKE CONCAT('%',pCadena,'%') OR
                    CONCAT(c.Apellidos, ' ', c.Nombres) LIKE CONCAT('%',pCadena,'%')
                ) AND
                (c.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                (c.Tipo = pTipo OR pTipo = '')
    ORDER BY    CONCAT(c.Apellidos, ', ', c.Nombres)
    LIMIT       pOffset, pLimit;
END $$
DELIMITER ;
