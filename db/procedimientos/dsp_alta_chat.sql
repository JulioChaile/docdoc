set collation_connection=utf8mb4_unicode_ci;
DROP PROCEDURE IF EXISTS `dsp_alta_chat`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_chat`(pIdExternoChat varchar(500), pIdCaso bigint, pIdPersona int, pTelefono varchar(20))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
	DECLARE pCaratula varchar(100);
    DECLArE pIdChat bigint;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdCaso IS NULL THEN
        SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdPersona IS NULL THEN
        SELECT 'Debe indicar una persona.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pTelefono IS NULL THEN
        SELECT 'Debe indicar un telefono.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso AND (Estado = 'A' OR Estado = 'P')) THEN
		SELECT 'El caso indicado no existe en el sistema o está dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona) THEN
		SELECT 'La persona indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        IF EXISTS (SELECT 0 FROM Chats WHERE IdExternoChat = pIdExternoChat) THEN
            SET pCaratula = (SELECT      c.Caratula 
                            FROM        Chats ch
                            INNER JOIN  Casos c USING (IdCaso)
                            WHERE       IdExternoChat = pIdExternoChat);
            SELECT CONCAT('EX', pCaratula);
        ELSE
            INSERT INTO Chats (IdExternoChat, IdCaso, IdPersona, Telefono, IdUltimoMensajeLeido)
            VALUES (pIdExternoChat, pIdCaso, pIdPersona, pTelefono, NULL);

            SET pIdChat = LAST_INSERT_ID();

            INSERT INTO Mensajes
            SELECT 0, IdMensajeApi, pIdChat, Contenido, IdUsuario, FechaEnviado, FechaRecibido, FechaVisto
            FROM MensajesExterno
            WHERE IdChatApi = pIdExternoChat;

            SELECT CONCAT('OK', pIdChat);
        END IF;
    COMMIT;
END $$
DELIMITER ;
