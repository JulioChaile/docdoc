DROP PROCEDURE IF EXISTS `dsp_parametros_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_parametros_caso`(pJWT varchar(500), pParametros json, pIdCaso bigint)
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
    START TRANSACTION;
        IF EXISTS (SELECT 1 FROM ParametrosCaso WHERE IdCaso = pIdCaso) THEN
            UPDATE  ParametrosCaso
            SET     Parametros = pParametros
            WHERE   IdCaso = pIdCaso;
        ELSE
            INSERT INTO ParametrosCaso SELECT pIdCaso, pParametros;
        END IF;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
