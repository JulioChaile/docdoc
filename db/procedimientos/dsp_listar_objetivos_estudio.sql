DROP PROCEDURE IF EXISTS `dsp_listar_objetivos_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_objetivos_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar los objetivos por defecto de un estudio. Ordena por EstadoCaso.
    */
    
    SELECT	oe.*, tm.TipoMovimiento,
        CASE oe.ColorMov
            WHEN 'negative' THEN 'Perentorios'
            WHEN 'primary' THEN 'Gestion Estudio'
            WHEN 'warning' THEN 'Gestion Externa'
            WHEN 'positive' THEN 'Finalizados'
            ELSE oe.ColorMov
        END AS EstadoGestion
    FROM	ObjetivosEstudio oe
    LEFT JOIN TiposMovimiento tm ON oe.IdTipoMov =  tm.IdTipoMov
    WHERE	oe.IdEstudio = pIdEstudio;
END $$
DELIMITER ;