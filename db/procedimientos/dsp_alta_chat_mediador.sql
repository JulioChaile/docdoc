DROP PROCEDURE IF EXISTS `dsp_alta_chat_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_chat_mediador`(pIdExternoChat varchar(64), pIdMediador int, pTelefono varchar(20))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
	DECLARE pCaratula varchar(100);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdMediador IS NULL THEN
        SELECT 'Debe indicar un mediador.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pTelefono IS NULL THEN
        SELECT 'Debe indicar un telefono.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Mediadores WHERE IdMediador = pIdMediador) THEN
		SELECT 'El mediador indicado no existe en el sistema' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM ChatsMediadores WHERE IdMediador = pIdMediador) THEN
		SELECT 'El mediador indicado ya tiene un chat creado' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        INSERT INTO ChatsMediadores (IdExternoChat, IdMediador, Telefono, IdUltimoMensajeLeido)
        VALUES (pIdExternoChat, pIdMediador, pTelefono, NULL);

        SELECT CONCAT('OK', LAST_INSERT_ID());
    COMMIT;
END $$
DELIMITER ;
