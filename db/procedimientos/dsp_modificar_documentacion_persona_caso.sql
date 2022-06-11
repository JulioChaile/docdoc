DROP PROCEDURE IF EXISTS `dsp_modificar_documentacion_persona_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_documentacion_persona_caso`(pJWT varchar(500), pIdPersona int, pIdCaso bigint, pDocumentacionSolicitada json)
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
    IF pIdPersona IS NULL THEN
		SELECT 'Debe indicar una persona.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM PersonasCaso WHERE IdPersona = pIdPersona AND IdCaso = pIdCaso) THEN
		SELECT 'La persona indicada no existe' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        UPDATE  PersonasCaso
        SET     DocumentacionSolicitada = pDocumentacionSolicitada
        WHERE   IdPersona = pIdPersona AND IdCaso = pIdCaso;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
