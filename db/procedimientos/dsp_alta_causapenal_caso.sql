DROP PROCEDURE IF EXISTS `dsp_alta_causapenal_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_causapenal_caso`(pJWT varchar(500), pIdCaso bigint, pEstadoCausaPenal varchar(45), pNroExpedienteCausaPenal varchar(45), pRadicacionCausaPenal varchar(45), pComisaria varchar(200))
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
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM CausaPenalCaso WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado ya tiene datos de causa penal cargados.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		INSERT INTO CausaPenalCaso SELECT 0, pIdCaso, pEstadoCausaPenal, pNroExpedienteCausaPenal, pRadicacionCausaPenal, pComisaria, NOW();

        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
    COMMIT;
END $$
DELIMITER ;
