DROP PROCEDURE IF EXISTS `dsp_dame_intervalo_fechas_casos`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_intervalo_fechas_casos`(pIdEstudio int)
BEGIN
	/*
	Permite obtener el intervalo de fechas en las que se dieron de alta todos
    los casos de un estudio.
    */
    SELECT 		MIN(FechaAlta) FechaInicio, MAX(FechaAlta) FechaFin 
    FROM 		Casos
    INNER JOIN	UsuariosCaso uc USING (IdCaso)
    WHERE		IdEstudio = pIdEstudio;
END $$
DELIMITER ;
