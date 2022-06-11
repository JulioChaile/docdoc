DROP PROCEDURE IF EXISTS `dsp_alta_mensaje_chat_interno`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mensaje_chat_interno`(pIdCaso bigint, pContenido text, pIdUsuario int, pCliente char(1), pURLMult varchar(100), pTipoMult char(1))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    DECLARE pFechaVisto timestamp;
    DECLARE pIdMultimedia int;

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
    IF pContenido IS NULL THEN
        SELECT 'No puede enviar un mensaje sin contenido.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El chat indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        SET pFechaVisto = IF(pCliente = 'S', NULL, NOW());
        SET pIdMultimedia = NULL;

        IF pURLMult != '' AND pURLMult IS NOT NULL THEN
			SET pIdMultimedia = (SELECT COALESCE(MAX(IdMultimedia), 0) + 1 FROM Multimedia);

            INSERT INTO Multimedia VALUES (pIdMultimedia, pURLMult, NOW(), pTipoMult, 'Sin nombre');
            
            INSERT INTO MultimediaCaso VALUES (pIdMultimedia, pIdCaso, 'R');
        END IF;

        INSERT INTO MensajesChatInterno
        VALUES (0, pContenido, NOW(), NULL, pIdCaso, pIdMultimedia, pIdUsuario, pCliente);

        SELECT CONCAT('OK', LAST_INSERT_ID());
    COMMIT;
END $$
DELIMITER ;
