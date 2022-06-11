DROP PROCEDURE IF EXISTS `dsp_listar_fechas_mediacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_fechas_mediacion`(pIdEstudio int)
PROC: BEGIN
    SELECT DISTINCT	m.FechaProximaAudiencia
    FROM			Mediaciones m
    LEFT JOIN		Casos c USING(IdCaso)
    LEFT JOIN		UsuariosCaso uc USING(IdCaso)
    WHERE			uc.IdEstudio = pIdEstudio AND
                    FechaProximaAudiencia > NOW()
    ORDER BY		FechaProximaAudiencia ASC;
END $$
DELIMITER ;
