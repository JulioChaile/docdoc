DROP PROCEDURE IF EXISTS `dsp_modificar_ulmsjleido`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_ulmsjleido`(pIdChat bigint, pIdMensaje bigint)
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
    IF NOT EXISTS (SELECT IdMensaje FROM Mensajes WHERE IdMensaje = pIdMensaje) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdChat FROM Chats WHERE IdChat = pIdChat) THEN
		SELECT 'El chat indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        UPDATE Chats
            SET IdUltimoMensajeLeido = pIdMensaje
            WHERE IdChat = pIdChat;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
