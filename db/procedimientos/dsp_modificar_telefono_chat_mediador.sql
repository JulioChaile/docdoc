DROP PROCEDURE IF EXISTS `dsp_modificar_telefono_chat_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_telefono_chat_mediador`(pIdChatMediador bigint, pTelefono varchar(20), pIdExternoChat varchar(64))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
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
    IF NOT EXISTS (SELECT * FROM ChatsMediadores WHERE IdChatMediador = pIdChatMediador) THEN
        SELECT 'Error, el chat no existe en la base de datos.' Mensaje;
        LEAVE PROC;
    END IF;
    START TRANSACTION;
        UPDATE ChatsMediadores
            SET Telefono = pTelefono,
                IdExternoChat = pIdExternoChat
            WHERE IdChatMediador = pIdChatMediador;

        SELECT CONCAT('OK', pTelefono);
    COMMIT;
END $$
DELIMITER ;
