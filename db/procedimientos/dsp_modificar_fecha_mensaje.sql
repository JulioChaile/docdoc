DROP PROCEDURE IF EXISTS `dsp_modificar_fecha_mensaje`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_fecha_mensaje`(pIdExternoMensaje varchar(64), pFechaRecibido timestamp, pFechaVisto timestamp)
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
    IF NOT EXISTS (SELECT 1 FROM Mensajes WHERE IdExternoMensaje = pIdExternoMensaje) AND NOT EXISTS (SELECT 1 FROM MensajesChatsMediadores WHERE IdExternoMensaje = pIdExternoMensaje) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pFechaRecibido IS NULL THEN
        IF EXISTS (SELECT 1 FROM Mensajes WHERE IdExternoMensaje = pIdExternoMensaje) THEN
            SET pFechaRecibido = (SELECT FechaRecibido FROM Mensajes WHERE IdExternoMensaje = pIdExternoMensaje);
        ELSEIF EXISTS (SELECT 1 FROM MensajesChatsMediadores WHERE IdExternoMensaje = pIdExternoMensaje) THEN
            SET pFechaRecibido = (SELECT FechaRecibido FROM MensajesChatsMediadores WHERE IdExternoMensaje = pIdExternoMensaje);
        ELSE
            SET pFechaRecibido = (SELECT FechaRecibido FROM MensajesChatsContactos WHERE IdExternoMensaje = pIdExternoMensaje);
        END IF;
    END IF;
    IF pFechaVisto IS NULL THEN
        IF EXISTS (SELECT 1 FROM Mensajes WHERE IdExternoMensaje = pIdExternoMensaje) THEN
            SET pFechaVisto = (SELECT FechaVisto FROM Mensajes WHERE IdExternoMensaje = pIdExternoMensaje);
        ELSEIF EXISTS (SELECT 1 FROM MensajesChatsMediadores WHERE IdExternoMensaje = pIdExternoMensaje) THEN
            SET pFechaVisto = (SELECT FechaVisto FROM MensajesChatsMediadores WHERE IdExternoMensaje = pIdExternoMensaje);
        ELSE
            SET pFechaVisto = (SELECT FechaVisto FROM MensajesChatsContactos WHERE IdExternoMensaje = pIdExternoMensaje);
        END IF;
    END IF;
    START TRANSACTION;
        UPDATE Mensajes
            SET FechaRecibido = pFechaRecibido,
                FechaVisto = pFechaVisto
            WHERE IdExternoMensaje = pIdExternoMensaje;

        UPDATE MensajesChatsMediadores
            SET FechaRecibido = pFechaRecibido,
                FechaVisto = pFechaVisto
            WHERE IdExternoMensaje = pIdExternoMensaje;

        UPDATE MensajesChatsContactos
            SET FechaRecibido = pFechaRecibido,
                FechaVisto = pFechaVisto
            WHERE IdExternoMensaje = pIdExternoMensaje;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
