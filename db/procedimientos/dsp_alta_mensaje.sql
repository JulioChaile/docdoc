DROP PROCEDURE IF EXISTS `dsp_alta_mensaje`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mensaje`(pIdExternoMensaje varchar(64), pIdChat bigint, pContenido text, pFechaEnviado timestamp, pFechaRecibido timestamp, pIdUsuario int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdChat IS NULL THEN
        SELECT 'Debe indicar un chat.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pContenido IS NULL THEN
        SELECT 'No puede enviar un mensaje sin contenido.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdChat FROM Chats WHERE IdChat = pIdChat) THEN
		SELECT 'El chat indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM Mensajes WHERE IdExternoMensaje = pIdExternoMensaje) THEN
		SELECT 'El mensaje indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        INSERT INTO Mensajes (IdExternoMensaje, IdChat, Contenido, IdUsuario, FechaEnviado, FechaRecibido)
        VALUES (pIdExternoMensaje, pIdChat, pContenido, pIdUsuario, pFechaEnviado, pFechaRecibido);

        SELECT CONCAT('OK', LAST_INSERT_ID());
    COMMIT;
END $$
DELIMITER ;
