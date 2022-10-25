DROP PROCEDURE IF EXISTS `dsp_archivar_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_archivar_caso`(pJWT varchar(500), pIdCaso bigint, pEstado char(1), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado del caso a R: Archivado. Devuelve OK o un mensaje de error en Mensaje.
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
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion) THEN
		SELECT 'Usted no está asociado al caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion 
    AND Permiso = 'A') THEN
		SELECT 'Se necesitan permisos de administración sobre el caso para poder archivarlo.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
		INSERT INTO aud_Casos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ESTADO', 'A', Casos.* 
        FROM Casos WHERE IdCaso = pIdCaso;
        
        UPDATE	Casos
        SET		Estado = pEstado 
        WHERE	IdCaso = pIdCaso;
        
        -- Después
		INSERT INTO aud_Casos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ESTADO', 'D', Casos.* 
        FROM Casos WHERE IdCaso = pIdCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
