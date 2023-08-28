
DROP PROCEDURE IF EXISTS `dsp_modificar_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_caso`(pJWT varchar(500), pIdCaso bigint, pIdJuzgado int, pIdNominacion int, pIdCompetencia int, pIdEstadoAmbitoGestion int,
			pIdEstadoCaso int, pCaratula varchar(500), pNroExpediente varchar(50), 
            pCarpeta varchar(5), pIdOrigen int, pIdTipoCaso smallint, pFechaEstado date, pObservaciones varchar(255), pIdCasoEstudio bigint(20),
            pDefiende char(1),
            pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar algunos parámetros de un caso, controlando que el usuario pertenezca a los UsuariosCaso y que tenga permiso
    de Administración o Escritura sobre el caso. Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- show errors;
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
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
        IF pIdCompetencia IS NULL THEN
            SELECT 'Debe indicar una competencia.' Mensaje;
            LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'La competencia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdJuzgado IS NOT NULL AND NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado AND Estado = 'A') THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    /*IF NOT EXISTS (SELECT IdEstadoAmbitoGestion FROM EstadoAmbitoGestion WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'El estado de ambito de gestion indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF*/
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('A','E')) THEN
		SELECT 'Usted no tiene permiso para modificar el caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdNominacion IS NOT NULL AND NOT EXISTS (SELECT IdNominacion FROM Nominaciones WHERE IdNominacion = pIdNominacion AND IdJuzgado = pIdJuzgado AND Estado = 'A') THEN
		SELECT 'La nominación indicada no existe.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstadoCaso IS NOT NULL AND NOT EXISTS (SELECT pIdEstadoCaso FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso) THEN
		SELECT 'El estado indicado no es válido.' Mensaje;
        LEAVE PROC;
	END IF;

    
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		
        -- Auditoría previa
		INSERT INTO aud_Casos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Casos.* 
		FROM Casos WHERE IdCaso = pIdCaso;
        
		UPDATE 	Casos 
        SET		IdJuzgado = pIdJuzgado,
				IdNominacion = pIdNominacion,
                IdCompetencia = pIdCompetencia,
                IdTipoCaso = pIdTipoCaso,
				IdEstadoCaso = pIdEstadoCaso,
                IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion,
                IdOrigen = pIdOrigen,
				Caratula = pCaratula,
                NroExpediente = pNroExpediente,
                FechaEstado = pFechaEstado,
                Carpeta = pCarpeta,
                Observaciones = pObservaciones,
                Defiende = pDefiende
		WHERE 	IdCaso = pIdCaso;

        SET pIdCasoEstudio = COALESCE(pIdCasoEstudio, 0);

        IF NOT EXISTS (SELECT 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio AND IdCaso = pIdCaso) AND pIdCasoEstudio != 0 THEN
            INSERT INTO IdsCasosEstudio SELECT pIdCasoEstudio, pIdCaso, pIdEstudio;
        END IF;
        
        -- Auditoría posterior
        INSERT INTO aud_Casos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Casos.* 
		FROM Casos WHERE IdCaso = pIdCaso;
        
        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
