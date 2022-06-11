DROP PROCEDURE IF EXISTS `dsp_dame_calendario_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_calendario_estudio`(pIdCalendario int)
BEGIN
    SELECT 	*
    FROM	CalendariosEstudio
    WHERE	pIdCalendario = pIdCalendario;
END $$
DELIMITER ;
