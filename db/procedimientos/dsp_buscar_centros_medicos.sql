DROP PROCEDURE IF EXISTS `dsp_buscar_centros_medicos`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_centros_medicos`(pCadena varchar(50), pLimit int, pOffset int)
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
    FROM		CentrosMedicos
    WHERE		CentroMedico LIKE CONCAT('%',pCadena,'%')
    ORDER BY    CentroMedico
    LIMIT       pOffset, pLimit;
END $$
DELIMITER ;
