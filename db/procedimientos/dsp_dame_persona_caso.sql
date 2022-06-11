DROP PROCEDURE IF EXISTS `dsp_dame_persona_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_persona_caso`(pIdCaso bigint, pIdPersona int)
BEGIN
	/*
    Permite instanciar una persona del caso.
    */
    /*DECLARE pTelefonos json;
    SET @@group_concat_max_len = 1024 * 1024 * 1024;
    SET pTelefonos = (SELECT 	CONCAT('[', COALESCE(GROUP_CONCAT(Telefono), ''),']')
						FROM	TelefonosPersona
						WHERE	IdPersona = pIdPersona);
    */
    SELECT		p.*, rtc.Rol, pc.EsPrincipal, pc.Observaciones, CAST((
                    SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
                                'Telefono', tp.Telefono,
                                'FechaAlta', tp.FechaAlta,
                                'EsPrincipal', tp.EsPrincipal,
                                'Detalle', tp.Detalle
                            ))
                    FROM	TelefonosPersona tp
                    WHERE	IdPersona = p.IdPersona
                            AND tp.Telefono IS NOT NULL
                            AND TRIM(tp.Telefono) != ''
                            AND tp.Telefono != 'null') as json
                ) Telefonos
    FROM		Personas p 
    INNER JOIN	PersonasCaso pc USING (IdPersona)
    LEFT JOIN	RolesTipoCaso rtc USING (IdRTC)
    WHERE		pc.IdCaso = pIdCaso AND pc.IdPersona = pIdPersona;
END $$
DELIMITER ;
