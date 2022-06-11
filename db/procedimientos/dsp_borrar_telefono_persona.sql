DROP PROCEDURE IF EXISTS `dsp_borrar_telefono_persona`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_telefono_persona`(pJWT varchar(500), pIdPersona int, 
		pTelefono varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar el teléfono de una persona del estudio.
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
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no pertenece a un estudio o está dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros vacíos
    IF pIdPersona IS NULL THEN
		SELECT 'Debe indicar una persona.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio) THEN
		SELECT 'La persona no existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdPersona FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono COLLATE utf8mb4_unicode_ci) THEN
		SELECT 'El teléfono indicado no existe.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		
        INSERT INTO aud_TelefonosPersona
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#TELEFONO', 'B', TelefonosPersona.* 
        FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono COLLATE utf8mb4_unicode_ci;
        
        DELETE FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono COLLATE utf8mb4_unicode_ci;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
