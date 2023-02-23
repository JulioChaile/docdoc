DROP PROCEDURE IF EXISTS `dsp_posicion_movimiento_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_posicion_movimiento_caso`(pPosicion char(1), pIdMovimientoCaso bigint)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            -- SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            SHOW ERRORS;
            ROLLBACK;
		END;
	-- Control de parámetros vacíos
	IF pIdMovimientoCaso IS NULL THEN
		SELECT 'Debe indicar un movimiento de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT IdMovimientoCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
		SELECT 'El movimiento de caso no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		IF EXISTS (SELECT IdMovimientoCaso FROM OrdenMovs WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
            UPDATE OrdenMovs
            SET Posicion = pPosicion
            WHERE IdMovimientoCaso = pIdMovimientoCaso;
        END IF;

		IF NOT EXISTS (SELECT IdMovimientoCaso FROM OrdenMovs WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
            INSERT INTO OrdenMovs (IdMovimientoCaso, Posicion) VALUES (pIdMovimientoCaso, pPosicion);
        END IF;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
