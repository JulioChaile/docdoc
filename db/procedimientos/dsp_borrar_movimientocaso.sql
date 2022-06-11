DROP PROCEDURE IF EXISTS `dsp_borrar_movimientocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_movimientocaso`(pJWT varchar(500), pIdMovimientoCaso bigint, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un movimiento de un caso. Controla que el movimiento exista.
    Devuelve OK o el mensaje de error.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pUsuario varchar(120);
    DECLARE pIdCaso bigint;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- show errors;
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
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdMovimientoCaso IS NULL THEN
		SELECT 'Debe indicar un movimiento de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdMovimientoCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
		SELECT 'El movimiento no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdCaso = (SELECT IdCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso);
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('A', 'E')) THEN
		SELECT 'Usted no tiene permisos de administración ni de escritura sobre el caso. No puede borrar movimientos.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		
        INSERT INTO aud_MovimientosObjetivo
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRA#MOVIMIENTO', 'B', MovimientosObjetivo.* 
		FROM MovimientosObjetivo WHERE IdMovimientoCaso = pIdMovimientoCaso;
        
        DELETE FROM MovimientosObjetivo WHERE IdMovimientoCaso = pIdMovimientoCaso;
		
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRA', 'B', MovimientosCaso.* 
		FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;
        
        DELETE FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;
        
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
