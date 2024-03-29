DROP PROCEDURE IF EXISTS `dsp_alta_mensaje_externo`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mensaje_externo`(pIdMensajeApi varchar(500), pIdChatApi varchar(500), pContenido text, pFechaEnviado datetime, pFechaRecibido datetime, pIdUsuario int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdChatApi IS NULL THEN
        SELECT 'Debe indicar un chat.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pContenido IS NULL THEN
        SELECT 'No puede enviar un mensaje sin contenido.' Mensaje;
        LEAVE PROC;
    END IF;
    START TRANSACTION;
        IF EXISTS (SELECT 1 FROM ChatsMediadores WHERE IdExternoChat = pIdChatApi) THEN
            INSERT INTO MensajesChatsMediadores SELECT 0, pIdMensajeApi, (SELECT IdChatMediador FROM ChatsMediadores WHERE IdExternoChat = pIdChatApi LIMIT 1), pContenido, NULL, pFechaEnviado, pFechaRecibido, null;
        ELSE
            INSERT INTO MensajesExterno (IdMensajeApi, IdChatApi, Contenido, FechaEnviado, FechaRecibido, IdUsuario)
            VALUES (pIdMensajeApi, pIdChatApi, pContenido, pFechaEnviado, pFechaRecibido, pIdUsuario);
        END IF;

        SELECT CONCAT('OK', LAST_INSERT_ID());
    COMMIT;
END $$
DELIMITER ;
