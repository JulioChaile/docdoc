DROP PROCEDURE IF EXISTS `dsp_listar_carpetas_plantillas_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_carpetas_plantillas_estudio`(pIdEstudio int)
BEGIN
    SELECT	*
    FROM	CarpetasPlantillasEstudio
    WHERE	IdEstudio = pIdEstudio OR pIdEstudio IS NULL OR pIdEstudio = 0;
END $$
DELIMITER ;
