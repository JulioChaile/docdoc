DROP PROCEDURE IF EXISTS `dsp_modificar_ulmsjleido_contacto`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_ulmsjleido_contacto`(pIdChat bigint, pIdMensaje bigint)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdMensaje IS NULL THEN
        SELECT 'No se envio el Id del mensaje.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdChat IS NULL THEN
        SELECT 'No se envio el Id del chat.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM MensajesChatsContactos WHERE IdMensaje = pIdMensaje) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM ChatsContactos WHERE IdChatContacto = pIdChat) THEN
		SELECT 'El chat indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        UPDATE ChatsContactos
            SET IdUltimoMensajeLeido = pIdMensaje
            WHERE IdChatContacto = pIdChat;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
