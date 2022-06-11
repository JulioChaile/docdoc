DROP PROCEDURE IF EXISTS `dsp_buscar_ubicaciones_similares`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_ubicaciones_similares`(pDomicilio varchar(200))
BEGIN
    SELECT	cp.*
    FROM    CasosPendientes cp
    WHERE   IdEstadoCasoPendiente = 36 AND
            MATCH(cp.Domicilio) AGAINST(pDomicilio);
END $$
DELIMITER ;
