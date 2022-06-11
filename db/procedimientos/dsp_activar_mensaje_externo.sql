set collation_connection = utf8mb4_unicode_ci;
DROP PROCEDURE IF EXISTS `dsp_activar_mensaje_externo`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_mensaje_externo`(pIdMensajeExterno int, pIdMensajeApi varchar(64))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdMensajeExterno IS NULL THEN
        SELECT 'Debe indicar un mensaje.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdMensajeApi IS NULL THEN
        SELECT 'No se envio el Id externo del mensaje.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM MensajesExterno WHERE IdMensajeExterno = pIdMensajeExterno) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        UPDATE MensajesExterno
            SET IdMensajeApi = pIdMensajeApi
            WHERE IdMensajeExterno = pIdMensajeExterno;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;

