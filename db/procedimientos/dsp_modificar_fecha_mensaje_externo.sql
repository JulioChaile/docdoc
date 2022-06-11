DROP PROCEDURE IF EXISTS `dsp_modificar_fecha_mensaje_externo`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_fecha_mensaje_externo`(pIdExternoMensaje varchar(64), pFechaRecibido timestamp, pFechaVisto timestamp)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdExternoMensaje IS NULL THEN
        SELECT 'No se envio el Id externo del mensaje.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM MensajesExterno WHERE IdMensajeApi = pIdExternoMensaje) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pFechaRecibido IS NULL THEN
        SET pFechaRecibido = (SELECT FechaRecibido FROM MensajesExterno WHERE IdMensajeApi = pIdExternoMensaje);
    END IF;
    IF pFechaVisto IS NULL THEN
        SET pFechaVisto = (SELECT FechaVisto FROM Mensajes WHERE IdMensajeApi = pIdExternoMensaje);
    END IF;
    START TRANSACTION;
        UPDATE MensajesExterno
            SET FechaRecibido = pFechaRecibido,
                FechaVisto = pFechaVisto
            WHERE IdMensajeApi = pIdExternoMensaje;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
