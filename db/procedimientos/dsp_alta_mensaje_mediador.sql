DROP PROCEDURE IF EXISTS `dsp_alta_mensaje_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mensaje_mediador`(pIdExternoMensaje varchar(64), pIdChatMediador bigint, pContenido text, pFechaEnviado timestamp, pFechaRecibido timestamp, pIdUsuario int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdChatMediador IS NULL THEN
        SELECT 'Debe indicar un chat.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pContenido IS NULL THEN
        SELECT 'No puede enviar un mensaje sin contenido.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM ChatsMediadores WHERE IdChatMediador = pIdChatMediador) THEN
		SELECT 'El chat indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM MensajesChatsMediadores WHERE IdExternoMensaje = pIdExternoMensaje) THEN
		SELECT 'El mensaje indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        INSERT INTO MensajesChatsMediadores SELECT 0, pIdExternoMensaje, pIdChatMediador, pContenido, pIdUsuario, pFechaEnviado, pFechaRecibido, null;

        SELECT CONCAT('OK', LAST_INSERT_ID());
    COMMIT;
END $$
DELIMITER ;
