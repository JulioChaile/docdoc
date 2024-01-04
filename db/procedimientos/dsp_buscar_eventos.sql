DROP PROCEDURE IF EXISTS `dsp_buscar_eventos`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_eventos`(pIdCalendario int, pCadena varchar(50), pFechaInicio date, pFechaFin date)
PROC: BEGIN
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		e.*, c.IdCaso, eag.EstadoAmbitoGestion
    FROM		Eventos e
    LEFT JOIN   EventosMovimientos em USING(IdEvento)
    LEFT JOIN   MovimientosCaso mc USING(IdMovimientoCaso)
    LEFT JOIN   Casos c USING(IdCaso)
    LEFT JOIN   EstadoAmbitoGestion eag USING(IdEstadoAmbitoGestion)
	WHERE		IdCalendario = pIdCalendario AND (
                    Titulo LIKE CONCAT('%',pCadena,'%') OR
                    Descripcion LIKE CONCAT('%',pCadena,'%')
                ) AND
                Comienzo BETWEEN pFechaInicio AND pFechaFin;
END $$
DELIMITER ;
