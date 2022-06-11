DROP PROCEDURE IF EXISTS `dsp_modificar_causapenal_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_causapenal_caso`(pJWT varchar(500), pIdCaso bigint, pIdCausaPenalCaso int, pEstadoCausaPenal varchar(45), pNroExpedienteCausaPenal varchar(45), pRadicacionCausaPenal varchar(45), pComisaria varchar(200))
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
    DECLARE pFechaEstadoCausaPenal datetime;
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
    IF pIdCausaPenalCaso IS NULL THEN
		SELECT 'Debe indicar el id de causa penal.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        IF pEstadoCausaPenal = (SELECT EstadoCausaPenal FROM CausaPenalCaso WHERE IdCausaPenalCaso = pIdCausaPenalCaso) THEN
            SET pFechaEstadoCausaPenal = (SELECT FechaEstadoCausaPenal FROM CausaPenalCaso WHERE IdCausaPenalCaso = pIdCausaPenalCaso);
        ELSE
            SET pFechaEstadoCausaPenal = NOW();
        END IF;

		UPDATE  CausaPenalCaso
        SET     EstadoCausaPenal = pEstadoCausaPenal,
                NroExpedienteCausaPenal = pNroExpedienteCausaPenal,
                RadicacionCausaPenal = pRadicacionCausaPenal,
                Comisaria = pComisaria,
                FechaEstadoCausaPenal = pFechaEstadoCausaPenal
        WHERE   IdCausaPenalCaso = pIdCausaPenalCaso;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
