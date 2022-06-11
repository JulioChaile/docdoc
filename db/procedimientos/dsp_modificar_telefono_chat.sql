DROP PROCEDURE IF EXISTS `dsp_modificar_telefono_chat`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_telefono_chat`(pIdChat bigint, pTelefono varchar(20), pIdExternoChat varchar(64), pIdPersona int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            show errors;
            -- SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parametros vacios
    IF pIdChat IS NULL THEN
		SELECT 'Debe indicar un chat.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTelefono IS NULL THEN
		SELECT 'Debe indicar un telefono.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdExternoChat IS NULL THEN
		SELECT 'Debe indicar un Id externo del chat.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parametros incorrectos
    IF NOT EXISTS (SELECT * FROM Chats WHERE IdChat = pIdChat) THEN
        SELECT 'Error, el chat no existe en la base de datos.' Mensaje;
        LEAVE PROC;
    END IF;
    START TRANSACTION;
        UPDATE Chats
            SET Telefono = pTelefono,
                IdExternoChat = pIdExternoChat,
                IdPersona = pIdPersona
            WHERE IdChat = pIdChat;

        SELECT CONCAT('OK', pTelefono);
    COMMIT;
END $$
DELIMITER ;
