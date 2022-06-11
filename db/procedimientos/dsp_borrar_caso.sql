DROP PROCEDURE IF EXISTS `dsp_borrar_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_caso`(pJWT varchar(500), pIdCaso bigint, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Procedimiento que eliminar un caso junto con todas sus MultimediaMovimiento, MovimientosCaso,
    UsuariosCaso, PersonasCaso. Devuelve OK un mensaje de error en Mensaje.
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
	-- Control de parámetros vacíos
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio) THEN
		SELECT 'El caso indicado no pertenece al estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND
    IdUsuario = pIdUsuarioGestion) THEN
		SELECT 'Usted no está asociado como usuario del caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND
    IdUsuario = pIdUsuarioGestion AND Estado = 'A') THEN
		SELECT 'Debe tener permiso de administración sobre el caso para poder eliminarlo.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        INSERT INTO aud_MultimediaMovimiento
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#CASO', 'B', MultimediaMovimiento.*
        FROM MultimediaMovimiento INNER JOIN MovimientosCaso mc USING (IdMovimientoCaso) 
        WHERE mc.IdCaso = pIdCaso;
        
        DELETE 		mul.* 
        FROM		MultimediaMovimiento mul
        INNER JOIN	MovimientosCaso mc USING (IdMovimiento)
        WHERE		mc.IdCaso = pIdCaso;
        
        
        DROP TEMPORARY TABLE IF EXISTS tmp_objetivos_borrar;
        CREATE TEMPORARY TABLE tmp_objetivos_borrar ENGINE = MEMORY
		SELECT 		DISTINCT IdObjetivo 
        FROM 		MovimientosObjetivo mo 
        INNER JOIN 	MovimientosCaso mc USING (IdMovimientoCaso) 
        WHERE		mc.IdCaso = pIdCaso;
        
        INSERT INTO aud_MovimientosObjetivo
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#CASO', 'B', mo.*
        FROM MovimientosObjetivo mo INNER JOIN MovimientosCaso mc USING (IdMovimientoCaso) 
        WHERE mc.IdCaso = pIdCaso;
        
        DELETE 		mo.*
        FROM 		MovimientosObjetivo mo
        INNER JOIN	MovimientosCaso mc USING (IdMovimiento)
        WHERE		mc.IdCaso = pIdCaso;
        
        
        INSERT INTO aud_Objetivos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#CASO', 'B', Objetivos.*
        FROM Objetivos WHERE IdObjetivo IN (SELECT IdObjetivo FROM tmp_objetivos_borrar);
        
        DELETE FROM Objetivos WHERE IdObjetivo IN (SELECT IdObjetivo FROM tmp_objetivos_borrar);
        
        
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#CASO', 'B', MovimientosCaso.*
        FROM MovimientosCaso WHERE mc.IdCaso = pIdCaso;
        
        DELETE FROM MovimientosCaso WHERE IdCaso = pIdCaso;
        
        INSERT INTO aud_UsuariosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#CASO', 'B', UsuariosCaso.*
        FROM UsuariosCaso WHERE IdCaso = pIdCaso;
        
        DELETE FROM UsuariosCaso WHERE IdCaso = pIdCaso;
        
        INSERT INTO aud_PersonasCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#CASO', 'B', PersonasCaso.*
        FROM PersonasCaso WHERE IdCaso = pIdCaso;
        
        DELETE FROM PersonasCaso WHERE IdCaso = pIdCaso;
        
        INSERT INTO aud_Casos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', Casos.*
        FROM Casos WHERE IdCaso = pIdCaso;
        
        DELETE FROM Casos WHERE IdCaso = pIdCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
