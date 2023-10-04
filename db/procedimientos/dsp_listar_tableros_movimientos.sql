DROP PROCEDURE IF EXISTS `dsp_listar_tableros_movimientos`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_tableros_movimientos`( pIdEstudio int )
BEGIN
    
    SELECT
        tmt.*,
        tm.TipoMovimiento,
        SUM(CASE WHEN mc.Color = 'negative' AND DATE(mc.FechaEsperada) = DATE(NOW()) THEN 1 ELSE 0 END) AS CantHoyPerentorios,
        SUM(CASE WHEN mc.Color = 'primary' AND DATE(mc.FechaEsperada) = DATE(NOW()) THEN 1 ELSE 0 END) AS CantHoyGestionEstudio,
        SUM(CASE WHEN mc.Color = 'warning' AND DATE(mc.FechaEsperada) = DATE(NOW()) THEN 1 ELSE 0 END) AS CantHoyGestionExterna,
        SUM(CASE WHEN mc.Color = 'negative' AND DATE(mc.FechaEsperada) > DATE(NOW()) THEN 1 ELSE 0 END) AS CantFuturosPerentorios,
        SUM(CASE WHEN mc.Color = 'primary' AND DATE(mc.FechaEsperada) > DATE(NOW()) THEN 1 ELSE 0 END) AS CantFuturosGestionEstudio,
        SUM(CASE WHEN mc.Color = 'warning' AND DATE(mc.FechaEsperada) > DATE(NOW()) THEN 1 ELSE 0 END) AS CantFuturosGestionExterna,
        SUM(CASE WHEN mc.Color = 'negative' AND DATE(mc.FechaEsperada) < DATE(NOW()) THEN 1 ELSE 0 END) AS CantVencidosPerentorios,
        SUM(CASE WHEN mc.Color = 'primary' AND DATE(mc.FechaEsperada) < DATE(NOW()) THEN 1 ELSE 0 END) AS CantVencidosGestionEstudio,
        SUM(CASE WHEN mc.Color = 'warning' AND DATE(mc.FechaEsperada) < DATE(NOW()) THEN 1 ELSE 0 END) AS CantVencidosGestionExterna
    FROM TiposMovimientoTableros tmt
    LEFT JOIN TiposMovimiento tm USING(IdTipoMov)
    LEFT JOIN MovimientosCaso mc USING(IdTipoMov)
    LEFT JOIN Casos c USING(IdCaso)
    WHERE   tmt.IdEstudio = pIdEstudio AND
            mc.FechaEsperada IS NOT NULL AND
            mc.FechaRealizado IS NULL AND
            c.Estado NOT IN ('B', 'P', 'F', 'R', 'E')
    GROUP BY tmt.IdTipoMovimientoTablero, tmt.IdTipoMov
    ORDER BY tmt.Orden ASC;
END $$
DELIMITER ;
