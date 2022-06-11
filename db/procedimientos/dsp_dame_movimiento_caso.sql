DROP PROCEDURE IF EXISTS `dsp_dame_movimiento_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_movimiento_caso`(pIdMovimientoCaso bigint)
BEGIN
	/*
    Permite instanciar un movimiento de caso desde la base de datos.
    */
    DECLARE pMultimedia json;
    
    SET pMultimedia = (SELECT CONCAT('[', COALESCE(GROUP_CONCAT(JSON_OBJECT(
												'URL', m.URL,
												'Tipo', m.Tipo,
                                                'FechaAlta', m.FechaAlta
                                    )), ''), ']')
						FROM		MultimediaMovimiento mm
                        INNER JOIN	Multimedia m USING (IdMultimedia)
                        WHERE		IdMovimientoCaso = pIdMovimientoCaso);
                        
                        
    SELECT 		mc.*, pMultimedia Multimedia, o.IdObjetivo, o.Objetivo, mc.Color
    FROM		MovimientosCaso mc
    LEFT JOIN	MovimientosObjetivo mo USING (IdMovimientoCaso)
    LEFT JOIN	Objetivos o USING (IdObjetivo)
    WHERE		mc.IdMovimientoCaso = pIdMovimientoCaso;
END $$
DELIMITER ;
