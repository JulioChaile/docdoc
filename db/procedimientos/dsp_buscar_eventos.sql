DROP PROCEDURE IF EXISTS `dsp_buscar_eventos`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_eventos`(pIdCalendario int, pCadena varchar(50))
PROC: BEGIN
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		*
    FROM		Eventos
	WHERE		IdCalendario = pIdCalendario AND (
                    Titulo LIKE CONCAT('%',pCadena,'%') OR
                    Descripcion LIKE CONCAT('%',pCadena,'%')
                ) AND
                Comienzo > NOW();
END $$
DELIMITER ;
