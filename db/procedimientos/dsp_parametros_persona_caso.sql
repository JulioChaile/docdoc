DROP PROCEDURE IF EXISTS `dsp_parametros_persona_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_parametros_persona_caso`(pJWT varchar(500), pParametros json, pIdCaso bigint, pIdPersona int)
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
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
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdPersona IS NULL THEN
		SELECT 'Debe indicar una persona.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Personas WHERE IdPersona = pIdPersona) THEN
		SELECT 'La persona indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona) THEN
		SELECT 'La persona indicada no pertecene al caso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	PersonasCaso
        SET		ValoresParametros = pParametros
        WHERE	IdCaso = pIdCaso AND IdPersona = pIdPersona;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
