DROP PROCEDURE IF EXISTS `dsp_listar_calendarios_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_calendarios_estudio`(pIdEstudio int)
BEGIN    
    SELECT	*
    FROM	CalendariosEstudio
    WHERE	IdEstudio = pIdEstudio;
END $$
DELIMITER ;
