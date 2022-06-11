DROP PROCEDURE IF EXISTS `dsp_desrealizar_movimiento_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_desrealizar_movimiento_caso`(pJWT varchar(500), pIdMovimientoCaso bigint, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite marcar un movimiento de caso como realizado. Devuelve OK o un mensaje de error en Mensaje.
    */    
    DECLARE pIdCaso bigint;
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
		SELECT 'Usted no pertenece a un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros vacíos
	IF pIdMovimientoCaso IS NULL THEN
		SELECT 'Debe indicar un movimiento de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT IdMovimientoCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
		SELECT 'El movimiento de caso no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdCaso = (SELECT IdCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso);
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion) THEN
		SELECT 'Usted no tiene permisos sobre el caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion
    AND Permiso IN ('E', 'A')) THEN
		SELECT 'Debe tener permiso de Escritura o Administración para marcar un movimiento como realizado.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DESREALIZAR', 'A', MovimientosCaso.* 
        FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;
        
        UPDATE	MovimientosCaso
        SET		IdResponsable = NULL,
				FechaRealizado = NULL
		WHERE	IdMovimientoCaso = pIdMovimientoCaso;
        
        -- Después
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DESREALIZAR', 'D', MovimientosCaso.* 
        FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
