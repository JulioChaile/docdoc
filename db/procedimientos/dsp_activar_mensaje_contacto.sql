DROP PROCEDURE IF EXISTS `dsp_activar_mensaje_contacto`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_mensaje_contacto`(pIdMensaje bigint, pIdExternoMensaje varchar(64))
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
        SELECT 'Debe indicar un mensaje.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdExternoMensaje IS NULL THEN
        SELECT 'No se envio el Id externo del mensaje.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM MensajesChatsContactos WHERE IdMensaje = pIdMensaje) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        UPDATE MensajesChatsContactos
            SET IdExternoMensaje = pIdExternoMensaje
            WHERE IdMensaje = pIdMensaje;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;

