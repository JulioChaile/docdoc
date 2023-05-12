DROP PROCEDURE IF EXISTS `dsp_borrar_persona_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_persona_estudio`(pJWT varchar(500), pIdPersona int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar una persona controlando que no sea actor en un caso. Se borran los teléfonos asociados a la persona. 
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
	-- Control de parámetros vacíos
    IF pIdEstudio IS NULL THEN
		SELECT 'Usted no pertenece a ningún estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdPersona IS NULL THEN
		SELECT 'Debe indicar una persona.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio) THEN
		SELECT 'La persona indicada no existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdPersona FROM PersonasCaso pc INNER JOIN Personas p USING(IdPersona)
    WHERE pc.IdPersona = pIdPersona AND p.IdEstudio = pIdEstudio) THEN
		SELECT 'No se puede borrar la persona. La misma está asociada a un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuaro = pIdUsuarioGestion);
        
        INSERT INTO aud_TelefonosPersona
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#PERSONA', 'B', TelefonosPersona.*
        FROM TelefonosPersona WHERE IdPersona = pIdPersona;
        
        DELETE FROM TelefonosPersona WHERE IdPersona = pIdPersona;
		
        INSERT INTO aud_Personas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', Personas.*
        FROM Personas WHERE IdPersona = pIdPersona;
        
        DELETE FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
