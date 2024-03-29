DROP PROCEDURE IF EXISTS `dsp_compartir_caso_por_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_compartir_caso_por_estudio`(pJWT varchar(500), pIdCaso bigint, pIdEstudio int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Devuelve OK o un Mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
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
    IF pIdEstudio IS NULL OR pIdEstudio = 0 THEN
        SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosCaso WHERE IdUsuario = pIdUsuarioGestion AND IdCaso = pIdCaso AND Permiso = 'A') THEN
        SELECT 'Usted no tiene permisos para compartir este caso.' Mensaje;
        LEAVE PROC;
    END IF;
    IF EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion) THEN
        SELECT 'Está intentando compartir el caso con el mismo estudio.' Mensaje;
        LEAVE PROC;
    END IF;
    IF EXISTS (SELECT 1 FROM Comparticiones WHERE IdEstudioDestino = pIdEstudio AND IdCaso = pIdCaso) THEN
        SELECT 'El caso ya fue compartido a este estudio.' Mensaje;
        LEAVE PROC;
    END IF;
	START TRANSACTION;
        SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso), 0)+1 FROM UsuariosCaso);

        SET @a = pIdUsuarioCaso;

        INSERT IGNORE INTO UsuariosCaso
        SELECT 	@a := @a + 1, pIdCaso, pIdEstudio, IdUsuario, 'A', 'N'
        FROM 	UsuariosEstudio WHERE IdEstudio = pIdEstudio;

        INSERT INTO aud_UsuariosCaso
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'COMPARTIR', 'I', UsuariosCaso.* 
        FROM UsuariosCaso WHERE IdUsuarioCaso = pIdUsuarioCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
