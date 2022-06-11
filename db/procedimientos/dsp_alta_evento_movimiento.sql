DROP PROCEDURE IF EXISTS `dsp_alta_evento_movimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_evento_movimiento`(pJWT varchar(500), pIdEvento int, pIdMovimientoCaso bigint)
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros vacíos
    IF pIdEvento IS NULL THEN
		SELECT 'Se debe indicar un evento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdMovimientoCaso IS NULL THEN
		SELECT 'Se debe indicar un movimiento.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Eventos WHERE IdEvento = pIdEvento) THEN
		SELECT 'El evento indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
		SELECT 'El movimiento indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		INSERT INTO EventosMovimientos VALUES (pIdEvento, pIdMovimientoCaso);
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
