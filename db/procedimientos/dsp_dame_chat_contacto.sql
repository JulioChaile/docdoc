DROP PROCEDURE IF EXISTS `dsp_dame_chat_contacto`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_chat_contacto`(pIdChat bigint, pIdExternoChat varchar(64))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parametros
    IF NOT EXISTS (SELECT * FROM ChatsContactos WHERE IdExternoChat = COALESCE(pIdExternoChat, 0) OR IdChatContacto = COALESCE(pIdChat, 0)) THEN
        SELECT 'Error, el chat no existe en la base de datos.' Mensaje;
        LEAVE PROC;
    END IF;
    SELECT *
    FROM ChatsContactos
    WHERE IdExternoChat = COALESCE(pIdExternoChat, 0) OR IdChatContacto = COALESCE(pIdChat, 0);
END $$
DELIMITER ;
