DROP PROCEDURE IF EXISTS `dsp_modifica_permiso_usuario_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modifica_permiso_usuario_caso`(pJWT varchar(500), pIdUsuario int, pIdCaso int, pPermiso char(1),
            pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar el permiso de un usuario sobre un caso, da de alta el usuario caso si no existe.
    Controla que el usuario que está intentando realizar esta acción tenga permiso de administrador sobre
    el caso.
    Devuelve OK + el id del UsuarioCaso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdUsuarioCaso, pIdEstudio int;
    DECLARE pUsuario varchar(30);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- SHOW ERRORS;
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
    IF pIdUsuario = 0 OR pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdCaso = 0 OR pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
    END IF;
    IF LENGTH(pPermiso) != 1 OR pPermiso IS NULL THEN
		SELECT 'Debe indicar el permiso.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A') THEN
		SELECT 'Usted no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdUsuario = pIdUsuarioGestion AND IdCaso = pIdCaso AND Permiso = 'A') THEN
		SELECT 'Usted no es administrador de este caso.' Mensaje;
        LEAVE PROC;
    END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario AND Estado = 'A') THEN
		SELECT 'El usuario indicado no se encuentra activo en el sistema.' Mensaje;
        LEAVE PROC;
    END IF;
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario indicado no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIdUsuarioCaso = (SELECT COALESCE(IdUsuarioCaso,0) FROM UsuariosCaso WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio AND IdCaso = pIdCaso);
        IF pIdUsuarioCaso IS NULL OR pIdUsuarioCaso = 0 THEN
			SET pIdUsuarioCaso  = (SELECT MAX(IdUsuarioCaso) FROM UsuariosCaso) + 1;
            
            INSERT INTO UsuariosCaso
			VALUES (pIdUsuarioCaso, pIdCaso, pIdEstudio, pIdUsuario, pPermiso, 'N');
            
            -- Audito
			INSERT INTO aud_UsuariosCaso
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', UsuariosCaso.* 
			FROM UsuariosCaso WHERE IdUsuarioCaso = pIdUsuarioCaso;
		ELSE
			-- Auditoría anterior
			INSERT INTO aud_UsuariosCaso
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', UsuariosCaso.* 
			FROM UsuariosCaso WHERE IdUsuarioCaso = pIdUsuarioCaso;
            
			UPDATE UsuariosCaso SET Permiso = pPermiso WHERE IdUsuarioCaso = pIdUsuarioCaso;
            
            -- Auditoría posterior
			INSERT INTO aud_UsuariosCaso
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', UsuariosCaso.* 
			FROM UsuariosCaso WHERE IdUsuarioCaso = pIdUsuarioCaso;
        END IF;
        SELECT CONCAT('OK', pIdUsuarioCaso) Mensaje;
	COMMIT;
END $$
DELIMITER ;
