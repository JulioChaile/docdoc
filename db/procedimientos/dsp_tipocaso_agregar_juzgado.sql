DROP PROCEDURE IF EXISTS `dsp_tipocaso_agregar_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_tipocaso_agregar_juzgado` (pJWT varchar(500), pIdTipoCaso smallint,pIdCompetencia int, pIdJuzgado int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
    /*
        Inserta una fila en la tabla TiposCasosJuzgados haciendo control de que no exista aún, y que la competencia, el tipo de caso y el juzgado se encuentren activos.
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
    -- Control de parámetros vacíos
    IF pIdTipoCaso IS NULL OR pIdTipoCaso = '' THEN
		SELECT 'Debe indicar un tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdJuzgado IS NULL OR pIdJuzgado = '' THEN
        SELECT 'Debe indicar un juzgado.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdCompetencia IS NULL OR pIdCompetencia = '' THEN
        SELECT 'Debe indicar una competencia.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia AND Estado = 'A') THEN
		SELECT 'La competencia indicada no existe en el sistema o bien no está activa.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado AND Estado = 'A') THEN
        SELECT 'El juzgado indicado no existe en el sistema o bien no se encuentra activo.' Mensaje;
        LEAVE PROC;
    END IF;
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso AND Estado = 'A') THEN
		SELECT 'El tipo de caso indicado no existe en el sistema o bien no está activo.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdJuzgado FROM TiposCasosJuzgados WHERE IdJuzgado = pIdJuzgado AND IdTipoCaso = pIdTipoCaso AND IdCompetencia = pIdCompetencia) THEN
		SELECT 'El juzgado ya se encuentra agregado al tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
    INSERT INTO TiposCasosJuzgados SELECT pIdCompetencia, pIdTipoCaso, pIdJuzgado;
    SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ; 
