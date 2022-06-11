DROP PROCEDURE IF EXISTS `dsp_borrar_tipocasocompetencia`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_tipocasocompetencia`(pJWT varchar(500), pIdTipoCaso smallint, pIdCompetencia int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
    /*
    Permite borrar un tipo de caso de una competencia controlando que no tenga roles asociados. 
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
    IF pIdTipoCaso IS NULL OR pIdTipoCaso = '' THEN
		SELECT 'Debe indicar un tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCompetencia IS NULL OR pIdCompetencia = '' THEN
		SELECT 'Debe indicar una competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso) THEN
		SELECT 'El tipo de caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'La competencia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdTipoCaso, IdCompetencia FROM Casos WHERE IdTipoCaso = pIdTipoCaso AND IdCompetencia = pIdCompetencia) THEN
		SELECT 'No se puede borrar el tipo de caso. Existen casos asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdTipoCaso, IdCompetencia FROM CompetenciasTiposCaso WHERE IdTipoCaso = pIdTipoCaso AND IdCompetencia = pIdCompetencia) THEN
		SELECT 'El tipo de caso no existe en la competencia asociada.' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
        
		DELETE FROM CompetenciasTiposCaso WHERE IdTipoCaso = pIdTipoCaso AND IdCompetencia = pIdCompetencia;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
