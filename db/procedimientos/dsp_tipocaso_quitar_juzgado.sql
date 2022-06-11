DROP PROCEDURE IF EXISTS `dsp_tipocaso_quitar_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_tipocaso_quitar_juzgado`(pJWT varchar(500), pIdCompetencia int, pIdTipoCaso smallint, pIdJuzgado int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
    /*
        Permite borrar una fila de la tabla TiposCasosJuzgados.
        Devuelve OK o un mensaje de error en Mensaje. 
    */
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
    -- Control de parámetros vacios
    IF pIdCompetencia IS NULL OR pIdCompetencia = '' THEN
		SELECT 'Debe indicar una competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdTipoCaso IS NULL OR pIdTipoCaso = '' THEN
		SELECT 'Debe indicar un tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdJuzgado IS NULL OR pIdJuzgado = '' THEN
        SELECT 'Debe indicar un juzgado.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
    SELECT 'La competencia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso) THEN
    SELECT 'El tipo de caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) THEN
    SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		DELETE FROM TiposCasosJuzgados WHERE IdCompetencia = pIdCompetencia AND IdTipoCaso = pIdTipoCaso AND IdJuzgado = pIdJuzgado;
		SELECT 'OK' Mensaje;
        COMMIT;
END $$
DELIMITER ;
