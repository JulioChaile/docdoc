DROP PROCEDURE IF EXISTS `dsp_duplicar_movimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_duplicar_movimiento`(pIdMovimientoCaso bigint, pIdObjetivo int)
PROC: BEGIN
	/*
    Permite dar de alta una consulta desde la web. 
    Devuelve OK + id de la consulta creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdMovimientoCasoNew bigint;
    START TRANSACTION;
        INSERT INTO MovimientosCaso SELECT 0, IdCaso, IdTipoMov, IdUsuarioCaso, IdResponsable, Detalle, NOW(), NOW(), FechaEsperada, FechaRealizado, Cuaderno, Escrito, Color FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;

        SET pIdMovimientoCasoNew = LAST_INSERT_ID();

        IF pIdObjetivo IS NOT NULL AND pIdObjetivo != 0 AND pIdObjetivo != '' THEN
            INSERT INTO MovimientosObjetivo SELECT pIdObjetivo, pIdMovimientoCasoNew;
        END IF;
        
        SELECT CONCAT('OK', pIdMovimientoCasoNew);
	COMMIT;
END $$
DELIMITER ;
