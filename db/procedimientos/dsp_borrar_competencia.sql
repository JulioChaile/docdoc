DROP PROCEDURE IF EXISTS `dsp_borrar_competencia`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_competencia`(pJWT varchar(500), pIdCompetencia int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un competencia controlando que no tenga roles asociados. 
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
		SELECT 'Debe indicar un competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'El competencia indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM CompetenciasTiposCaso WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'No se puede borrar el competencia. Existen tipos de casos asciados.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		DELETE FROM Competencias WHERE	IdCompetencia = pIdCompetencia;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
