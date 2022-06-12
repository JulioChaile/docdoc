DROP PROCEDURE IF EXISTS `dsp_alta_mensajeestudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mensajeestudio`(pJWT varchar(500), pIdEstudio int, pTitulo varchar(100), pMensajeEstudio text, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un MensajeEstudio, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdMensajeEstudio int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros vacíos
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTitulo IS NULL OR pTitulo = '' THEN
		SELECT 'Debe indicar un titulo para el mensaje.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pMensajeEstudio IS NULL OR pMensajeEstudio = '' THEN
		SELECT 'Debe indicar un mensaje.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdMensajeEstudio FROM MensajesEstudio WHERE Titulo = pTitulo AND IdEstudio = pIdEstudio) THEN
		SELECT 'El titulo indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdMensajeEstudio FROM MensajesEstudio WHERE MensajeEstudio = pMensajeEstudio AND IdEstudio = pIdEstudio) THEN
		SELECT 'El mensaje indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		INSERT INTO MensajesEstudio VALUES (0, pTitulo, pMensajeEstudio, pIdEstudio, '');
        
        SELECT CONCAT('OK', pIdMensajeEstudio) Mensaje;
	COMMIT;
END $$
DELIMITER ;
