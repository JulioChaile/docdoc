DROP PROCEDURE IF EXISTS `dsp_competencia_agregar_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_competencia_agregar_tipocaso`(pJWT varchar(500), pIdCompetencia int, pIdTipoCaso smallint,
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Agregar un tipo de caso a una competencia controlando que no exista ya y que ambas existan.
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
    IF pIdTipoCaso IS NULL OR pIdTipoCaso = '' THEN
		SELECT 'Debe indicar un tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia AND Estado = 'A') THEN
		SELECT 'El competencia indicado no existe en el sistema o bien no está activa.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso AND Estado = 'A') THEN
		SELECT 'El tipo de caso indicado no existe en el sistema o bien no está activo.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdTipoCaso FROM CompetenciasTiposCaso WHERE IdTipoCaso = pIdTipoCaso AND IdCompetencia = pIdCompetencia) THEN
		SELECT 'El tipo de caso ya está agregado a la competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		INSERT INTO CompetenciasTiposCaso SELECT pIdCompetencia, pIdTipoCaso;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
