DROP PROCEDURE IF EXISTS `dsp_mover_multimedia_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_mover_multimedia_caso`(pIdMultimedia int, pIdCarpetaCaso int)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
        DELETE FROM MultimediaCarpeta
        WHERE       IdMultimedia = pIdMultimedia;

        IF COALESCE(pIdCarpetaCaso, 0) != 0 THEN
            INSERT INTO MultimediaCarpeta VALUES (pIdMultimedia, pIdCarpetaCaso);
        END IF;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
