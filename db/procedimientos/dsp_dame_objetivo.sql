DROP PROCEDURE IF EXISTS `dsp_dame_objetivo`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_objetivo`(pIdObjetivo int)
BEGIN
	/*
    Permite instanciar un objetivo desde la base de datos.
    */
    DECLARE pMovimientosCaso json;
    
    SET pMovimientosCaso = (SELECT 		CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
																'IdMovimientoCaso', mc.IdMovimientoCaso,
																'IdTipoMov', mc.IdTipoMov,
																'TipoMovimiento', tm.TipoMovimiento,
																'IdUsuarioCaso', mc.IdUsuarioCaso,
																'IdResponsable', mc.IdResponsable,
																'Detalle', mc.Detalle,
																'FechaAlta', mc.FechaAlta,
																'FechaEsperada', mc.FechaEsperada,
																'FechaRealizado', mc.FechaRealizado,
																'Cuaderno', mc.Cuaderno,
                                                                'Color', mc.Color
                                                )),''),']')
							FROM		MovimientosCaso mc
                            INNER JOIN	MovimientosObjetivo mo USING (IdMovimientoCaso)
                            INNER JOIN	Objetivos o USING (IdObjetivo)
                            INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
                            WHERE		mo.IdObjetivo = pIdObjetivo
                            ORDER BY 	mc.FechaAlta DESC);
    
    SELECT		o.*, pMovimientosCaso MovimientosCaso
    FROM		Objetivos o
    WHERE		IdObjetivo = pIdObjetivo
    ORDER BY	FechaAlta;
    
END $$
DELIMITER ;
