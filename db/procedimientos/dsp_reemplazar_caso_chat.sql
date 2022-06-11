DROP PROCEDURE IF EXISTS `dsp_reemplazar_caso_chat`;
DELIMITER $$
CREATE PROCEDURE `dsp_reemplazar_caso_chat`(pIdCaso bigint, pIdExternoChat varchar(64), pIdPersona int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    DECLARE pIdChat int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parametros vacios
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdPersona IS NULL THEN
		SELECT 'Debe indicar una persona.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdExternoChat IS NULL THEN
		SELECT 'Debe indicar un Id externo del chat.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        UPDATE Chats
            SET IdCaso = pIdCaso,
                IdPersona = pIdPersona
            WHERE IdExternoChat = pIdExternoChat;

        SET pIdChat = (SELECT IdChat FROM Chats WHERE IdExternoChat = pIdExternoChat);

        SELECT CONCAT('OK', pIdChat);
    COMMIT;
END $$
DELIMITER ;
