DROP PROCEDURE IF EXISTS `dsp_buscar_cias_seguro`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_cias_seguro`(pCadena varchar(50), pLimit int, pOffset int)
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    SET pCadena = COALESCE(pCadena,'');

	IF pOffset IS NULL OR pOffset = '' THEN
		SET pOffset = 0;
	END IF;
    IF pLimit IS NULL OR pLimit = '' OR pLimit = 0 THEN
        SET pLimit = (SELECT COUNT(*) FROM CentrosMedicos);
    END IF;
    
    SELECT		*
    FROM		CiasSeguro
    WHERE		CiaSeguro LIKE CONCAT('%',pCadena,'%')
    ORDER BY    CiaSeguro
    LIMIT       pOffset, pLimit;
END $$
DELIMITER ;
