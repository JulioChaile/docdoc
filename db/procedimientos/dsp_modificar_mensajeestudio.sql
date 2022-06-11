DROP PROCEDURE IF EXISTS `dsp_modificar_mensajeestudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_mensajeestudio`(pJWT varchar(500), pIdMensajeEstudio int, pTitulo varchar(100), pMensajeEstudio varchar(500), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un MensajeEstudio, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
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
    IF pIdMensajeEstudio IS NULL THEN
		SELECT 'Debe indicar un mensaje.' Mensaje;
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
    IF NOT EXISTS (SELECT IdMensajeEstudio FROM MensajesEstudio WHERE IdMensajeEstudio = pIdMensajeEstudio) THEN
		SELECT 'El mensaje indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdEstudio = (SELECT IdEstudio FROM MensajesEstudio WHERE IdMensajeEstudio = pIdMensajeEstudio);
    IF EXISTS (SELECT IdMensajeEstudio FROM MensajesEstudio WHERE Titulo = pTitulo AND IdEstudio = pIdEstudio AND IdMensajeEstudio != pIdMensajeEstudio) THEN
		SELECT 'El titulo indicado para el mensaje ya se encuentra en uso en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdMensajeEstudio FROM MensajesEstudio WHERE MensajeEstudio = pMensajeEstudio AND IdEstudio = pIdEstudio AND IdMensajeEstudio != pIdMensajeEstudio) THEN
		SELECT 'El mensaje indicado ya se encuentra en uso en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	MensajesEstudio
        SET		MensajeEstudio = pMensajeEstudio,
              Titulo = pTitulo
        WHERE	IdMensajeEstudio = pIdMensajeEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
