DROP PROCEDURE IF EXISTS `dsp_borrar_personas_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_personas_caso`(pJWT varchar(500), pIdCaso bigint, 
		pPersonas json, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar una o mas personas de un caso. Devuelve OK o el mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdPersona, pIndice, pIdEstudio int;
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
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pPersonas IS NULL OR JSON_LENGTH(pPersonas) = 0 THEN
		SELECT 'Debe indicar la/s persona/s que desea eliminar del caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio) THEN
		SELECT 'Caso inexistente en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion) THEN
		SELECT 'No puede borrar personas. No está asociado como usuario del caso' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion AND
    Permiso IN ('E','A')) THEN
		SELECT 'No puede borrar personas ya que no tiene los permisos necesarios.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIndice = 0;
        WHILE pIndice < JSON_LENGTH(pPersonas) DO
			SET pIdPersona = JSON_EXTRACT(pPersonas, CONCAT('$[', pIndice, ']'));
			-- Control de parámetros vacíos
            IF pIdPersona IS NULL THEN
				ROLLBACK;
                SELECT 'Error en los parámetros.' Mensaje;
                LEAVE PROC;
			END IF;
            -- Control de parámetros incorrectos
            IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio) THEN
				SELECT 'Error en los parámetros. Una de las personas indicadas no existe en la base de datos del estudio.' Mensaje;
                LEAVE PROC;
			END IF;
			IF NOT EXISTS (SELECT IdPersona FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona) THEN
				ROLLBACK;
                SELECT 'Error en los parámetros. Una de las personas indicadas no pertenece al caso.' Mensaje;
                LEAVE PROC;
			END IF;
            
			INSERT INTO aud_PersonasCaso
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#PERSONA#CASO', 'B', PersonasCaso.* 
			FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;
            
            DELETE FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;
			SET pIndice = pIndice + 1;
        END WHILE;
		SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
