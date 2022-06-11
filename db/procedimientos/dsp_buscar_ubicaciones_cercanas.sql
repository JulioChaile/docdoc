DROP PROCEDURE IF EXISTS `dsp_buscar_ubicaciones_cercanas`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_ubicaciones_cercanas`(pLatitud varchar(100), pLongitud varchar(100), pDistancia float)
BEGIN
    SELECT	cp.*,   (acos(sin(radians(pLatitud)) * sin(radians(Latitud)) + 
                    cos(radians(pLatitud)) * cos(radians(Latitud)) * 
                    cos(radians(pLongitud) - radians(Longitud))) * 6378) Distancia
    FROM    CasosPendientes cp
    WHERE   IdEstadoCasoPendiente = 36 AND
            (acos(sin(radians(pLatitud)) * sin(radians(Latitud)) + 
                    cos(radians(pLatitud)) * cos(radians(Latitud)) * 
                    cos(radians(pLongitud) - radians(Longitud))) * 6378) < pDistancia;
END $$
DELIMITER ;
