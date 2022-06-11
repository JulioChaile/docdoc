DROP PROCEDURE IF EXISTS `dsp_borrar_persona_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_persona_caso`(pJWT varchar(500), pIdCaso bigint, 
		pIdPersona int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar una persona de un caso. Devuelve OK o el mensaje de error en Mensaje.
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
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdPersona IS NULL OR pIdPersona = '' THEN
		SELECT 'Debe indicar la persona que desea eliminar del caso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        -- Control de parámetros incorrectos
        IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio) THEN
            SELECT 'Error en los parámetros. Una de las personas indicadas no existe en la base de datos del estudio.' Mensaje;
            LEAVE PROC;
        END IF;
        
        DELETE FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;

		SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
