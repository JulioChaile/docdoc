DROP PROCEDURE IF EXISTS `dsp_modificar_competencia`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_competencia`(pJWT varchar(500), pCompetencia varchar(255),
		pIdCompetencia int)
PROC: BEGIN
	/*
    Permite modificar competencias controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
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
    -- Control de parámetros vacíos
    IF pCompetencia IS NULL OR pCompetencia = '' THEN
		SELECT 'Debe indicar el nombre de la competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCompetencia IS NULL THEN
		SELECT 'Debe indicar una competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT Competencia FROM Competencias WHERE Competencia = pCompetencia AND IdCompetencia != pIdCompetencia) THEN
		SELECT 'La competencia indicada ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'La competencia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		UPDATE	Competencias
        SET		Competencia = pCompetencia
        WHERE	IdCompetencia = pIdCompetencia;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
