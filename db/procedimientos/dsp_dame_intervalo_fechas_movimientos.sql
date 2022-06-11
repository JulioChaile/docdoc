DROP PROCEDURE IF EXISTS `dsp_dame_intervalo_fechas_movimientos`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_intervalo_fechas_movimientos`(pIdEstudio int)
BEGIN
	/*
	Permite obtener el intervalo de fechas en las que se dieron de alta todos
    los movimientos de un estudio.
    */
    
    SELECT 		MIN(mc.FechaAlta) FechaInicio, MAX(mc.FechaAlta) FechaFin 
    FROM 		MovimientosCaso mc
    INNER JOIN	Casos c USING (IdCaso)
    INNER JOIN	UsuariosCaso uc USING (IdCaso)
    WHERE		uc.IdEstudio = pIdEstudio;
END $$
DELIMITER ;
