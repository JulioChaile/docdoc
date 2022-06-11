DROP PROCEDURE IF EXISTS `dsp_listar_personas_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_personas_caso`(pIdCaso bigint)
BEGIN
	/*
    Permite listar todas las personas intervinientes en un caso.
    */
    SELECT		p.*, pc.EsPrincipal, pc.Observaciones, rtc.Rol, tp.*
    FROM		PersonasCaso pc 
    LEFT JOIN	RolesTipoCaso rtc USING (IdRTC)
    INNER JOIN	Personas p USING (IdPersona)
    LEFT JOIN   TelefonosPersona tp USING (IdPersona)
    WHERE		pc.IdCaso = pIdCaso
	ORDER BY	pc.EsPrincipal DESC, p.Apellidos, p.Nombres;
END $$
DELIMITER ;
