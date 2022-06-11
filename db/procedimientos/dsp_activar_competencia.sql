DROP PROCEDURE IF EXISTS `dsp_activar_competencia`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_competencia`(pJWT varchar(500), pIdCompetencia int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
	Permite cambiar el estado de un competencia a Activa, controlando que no se encuentre activa ya. 
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
    IF pIdCompetencia IS NULL OR pIdCompetencia = '' THEN
		SELECT 'Debe indicar una competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'La competencia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Competencias WHERE IdCompetencia = pIdCompetencia) = 'A' THEN
		SELECT 'La competencia ya se encuentra activa.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        UPDATE	Competencias
        SET		Estado = 'A'
        WHERE 	IdCompetencia = pIdCompetencia;
        
        SELECT 'OK' Mensaje;
	COMMIT;
        
END $$
DELIMITER ;
