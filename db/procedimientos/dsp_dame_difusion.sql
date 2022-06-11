DROP PROCEDURE IF EXISTS `dsp_dame_difusion`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_difusion`(pIdDifusion smallint)
BEGIN
	/*
    Permite instanciar una campaña de difusión desde la base de datos.
    */
    SELECT	IdDifusion, Difusion, FechaInicio, FechaFin
	FROM	Difusiones
    WHERE	IdDifusion = pIdDifusion;
END $$
DELIMITER ;
