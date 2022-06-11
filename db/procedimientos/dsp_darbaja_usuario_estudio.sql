DROP PROCEDURE IF EXISTS `dsp_darbaja_usuario_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_darbaja_usuario_estudio`(pJWT varchar(500), pIdEstudio int, pIdUsuario int,
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado de un abogado de un estudio a Baja. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
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
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario del estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El Estudio indicada no existe en el sitema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El abogado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio) THEN
		SELECT 'El usuario no pertenece al estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio) = 'B' THEN
		SELECT 'OK' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
		INSERT INTO aud_UsuariosEstudio
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DARBAJA', 'A', UsuariosEstudio.* 
        FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuario;
		
        UPDATE	UsuariosEstudio
        SET		Estado = 'B' 
        WHERE	IdEstudio = pIdEstudio AND IdUsuario = pIdUsuario;
        
        -- Después
		INSERT INTO aud_UsuariosEstudio
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DARBAJA', 'D', UsuariosEstudio.* 
        FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuario;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
