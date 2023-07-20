DROP PROCEDURE IF EXISTS `dsp_listar_comunicados`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_comunicados`(pIdEstudio int, pOffset int, pFechaHoy int)
BEGIN
	/*
    Permite listar los mensajes por defecto de un estudio.
    */
    
    SELECT	c.*, m.*
    FROM	Comunicados c
    LEFT JOIN Multimedia m ON c.IdMultimedia = m.IdMultimedia
    WHERE	IdEstudio = pIdEstudio AND (DATE(c.FechaComunicado) = DATE(NOW()) OR pFechaHoy != 1)
    ORDER BY c.IdComunicado DESC
    LIMIT   pOffset, 30;
END $$
DELIMITER ;
