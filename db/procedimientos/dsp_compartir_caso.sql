DROP PROCEDURE IF EXISTS `dsp_compartir_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_compartir_caso`(pJWT varchar(500), pIdCaso bigint, pIdUsuario int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite compartir un caso a un usuario de otro estudio, controlando que el usuario que gestiona la acción tenga permisos para compartir.
    Devuelve OK o un Mensaje de error en Mensaje.
    */
    DECLARE pIdEstudio, pIdUsuarioGestion int;
    DECLARE pIdUsuarioCaso bigint;
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
    IF pIdCaso IS NULL OR pIdCaso = 0 THEN
        SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdUsuario IS NULL OR pIdUsuario = 0 THEN
        SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosCaso WHERE IdUsuario = pIdUsuarioGestion AND IdCaso = pIdCaso AND Permiso = 'A') THEN
        SELECT 'Usted no tiene permisos para compartir este caso.' Mensaje;
        LEAVE PROC;
    END IF;
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario);
    IF pIdEstudio IS NULL THEN
        SELECT 'El usuario indicado no pertenece a ningún estudio.' Mensaje;
        LEAVE PROC;
    END IF;
    IF EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion) THEN
        SELECT 'Está intentando compartir el caso con un usuario del mismo estudio.' Mensaje;
        LEAVE PROC;
    END IF;
    IF EXISTS (SELECT 1 FROM Comparticiones WHERE IdEstudioDestino = pIdEstudio AND IdCaso = pIdCaso) THEN
        SELECT 'El caso ya fue compartido a este estudio.' Mensaje;
        LEAVE PROC;
    END IF;
	START TRANSACTION;
        SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso), 0)+1 FROM UsuariosCaso);

        INSERT INTO UsuariosCaso SELECT pIdUsuarioCaso, pIdCaso, pIdEstudio, pIdUsuario, 'A', 'N';

        INSERT INTO aud_UsuariosCaso
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'COMPARTIR', 'I', UsuariosCaso.* 
        FROM UsuariosCaso WHERE IdUsuarioCaso = pIdUsuarioCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
