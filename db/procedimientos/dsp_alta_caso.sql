DROP PROCEDURE IF EXISTS `dsp_alta_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_caso`(pJWT varchar(500), pIdJuzgado int, pIdNominacion int, pIdCompetencia int,
			pIdTipoCaso smallint, pIdEstadoCaso int, pIdOrigen int, pCaratula varchar(150), 
            pNroExpediente varchar(50), pCarpeta varchar(5), pObservaciones varchar(255), pPersonasCaso json, pIdEstadoAmbitoGestion int,
            pDefiende char(1), pDetalleOrigen varchar(500),
            pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta un caso controlando que el usuario que ejecuta el procedimiento pertenezca y esté activo en algún estudio. 
    El caso se da de alta junto con las personas relacionadas, siempre que existan en el parámetro pPersonasCaso JSON. 
    Si una persona indicada en el parámetro existe ya en el estudio, se utiliza su Id para asociarla al caso. 
    Si no existe, se la crea. El criterio para determinar si una persona existe o no es, si es Física, su documento debe existir en el sistema; 
    si es Jurídica, su CUIT debe existir en el sistema. Si no se indica una carátula, la misma se genera a partir del Tipo de Caso y 
    el nombre del actor principal, siguiendo el formato: Actor Principal s/ Tipo de Caso. 
    Devuelve OK + el id del caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIndice, pIdPersona, pIdUsuarioCaso, pIdEstudio, pIdTipoMov int;
    DECLARE pIdCaso, pIdCasoEstudio bigint;
    DECLARE pNombres, pApellidos varchar(50);
    DECLARE pObservacionesPersona varchar(255);
    DECLARE pDomicilio varchar(200);
    DECLARE pEmail, pUsuario varchar(120);
    DECLARE pCuit char(11);
    DECLARE pDocumento varchar(8);
    DECLARE pEsPrincipal, pTipoPersona char(1);
    DECLARE pIdRTC smallint;
    DECLARE pMensaje varchar(1000);
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
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
	IF pIdTipoCaso IS NULL THEN 
		SELECT 'Debe indicar el tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstadoCaso IS NULL THEN
		SET pIdEstadoCaso = (SELECT MIN(IdEstadoCaso) FROM EstadosCaso WHERE IdEstudio = pIdEstudio);        
	END IF;
        IF pIdCompetencia IS NULL THEN
            SELECT 'Debe indicar una competencia.' Mensaje;
            LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF pIdJuzgado IS NOT NULL AND NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado AND Estado = 'A') THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCompetencia FROM Competencias WHERE IdCompetencia = pIdCompetencia) THEN
		SELECT 'La competencia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    /*IF NOT EXISTS (SELECT IdEstadoAmbitoGestion FROM EstadoAmbitoGestion WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'El estado de ambito de gestion indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;*/
    IF pIdNominacion IS NOT NULL AND NOT EXISTS (SELECT IdNominacion FROM Nominaciones WHERE IdNominacion = pIdNominacion AND IdJuzgado = pIdJuzgado AND Estado = 'A') THEN
		SELECT 'La nominación indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso AND Estado = 'A') THEN
		SELECT 'El tipo de caso indicado no existe en el sistema o está dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdEstadoCaso FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso AND IdEstudio = pIdEstudio) THEN
		SELECT 'El estado de caso indicado no existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdOrigen IS NOT NULL AND NOT EXISTS (SELECT IdOrigen FROM Origenes WHERE IdOrigen = pIdOrigen) THEN
		SELECT 'El origen de caso indicado no existe.' Mensaje;
        LEAVE PROC;
	END IF;
    IF JSON_LENGTH(pPersonasCaso) = 0 OR pPersonasCaso IS NULL THEN
		SELECT 'No puede crear un caso sin por lo menos una persona asociada.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIdCaso = (SELECT COALESCE(MAX(IdCaso),0) + 1 FROM Casos);
        
        INSERT INTO Casos VALUES(pIdCaso, pIdJuzgado, pIdNominacion, pIdCompetencia, pIdTipoCaso, pIdEstadoCaso, pIdEstadoAmbitoGestion, pIdOrigen, pCaratula, pNroExpediente, NOW(),
								pCarpeta, NOW(), pObservaciones, NULL, 'A', pDefiende);
		
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) + 1 FROM UsuariosCaso);
        INSERT INTO UsuariosCaso VALUES (pIdUsuarioCaso, pIdCaso, pIdEstudio, pIdUsuarioGestion, 'A', 'S');
        
        SET @a = pIdUsuarioCaso;
        
        INSERT IGNORE INTO UsuariosCaso
        SELECT 	@a := @a + 1, pIdCaso, pIdEstudio, IdUsuario, 'A', 'N'
        FROM 	UsuariosEstudio WHERE IdEstudio = pIdEstudio; 
        
        INSERT INTO aud_UsuariosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA#CASO', 'I', UsuariosCaso.* 
		FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio;
        
        CALL dsp_alta_personas_caso_comun(pJWT, pIdCaso, pPersonasCaso, pIP, pUserAgent, pApp, pMensaje);
        
        IF SUBSTRING(pMensaje, 1, 2) != 'OK' THEN
			SELECT pMensaje Mensaje;
            ROLLBACK;
            LEAVE PROC;
		END IF;
    
        IF pCaratula IS NULL OR TRIM(pCaratula) = '' THEN
			SET pCaratula = (SELECT		CONCAT(p.Apellidos,', ',p.Nombres,' s/ ', tc.TipoCaso)
							FROM		Casos c 
							INNER JOIN	TiposCaso tc USING (IdTipoCaso)
							INNER JOIN	PersonasCaso pc USING (IdCaso)
							INNER JOIN	Personas p USING(IdPersona)
							WHERE		c.IdCaso = pIdCaso AND pc.EsPrincipal = 'S');    
			UPDATE Casos SET Caratula = pCaratula WHERE IdCaso = pIdCaso;
		END IF;

        -- Ingreso objetivos por defecto
        INSERT IGNORE INTO Objetivos
        SELECT @o := @o + 1, pIdCaso, oe.ObjetivoEstudio, NOW()
        FROM ObjetivosEstudio oe, (SELECT @o := MAX(IdObjetivo) FROM Objetivos) s
        WHERE oe.IdEstudio = pIdEstudio;
        
        -- Audito
		INSERT INTO aud_Casos
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Casos.* 
        FROM Casos WHERE IdCaso = pIdCaso;

        IF EXISTS (SELECT 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio) THEN
            SET pIdCasoEstudio = (SELECT MAX(IdCasoEstudio) + 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio);
        ELSE
            SET pIdCasoEstudio = 1;
        END IF;

        IF pDetalleOrigen IS NOT NULL AND pDetalleOrigen != '' THEN
            SET pIdTipoMov = (SELECT IdTipoMov FROM TiposMovimiento WHERE IdEstudio = pIdEstudio AND TipoMovimiento = 'Gestión oficina');

            INSERT INTO MovimientosCaso VALUES (0, pIdCaso, pIdTipoMov, pIdUsuarioCaso, pIdUsuarioCaso, pDetalleOrigen, NOW(), NOW(), null, null, null, null, 'primary');
        END IF;

        INSERT INTO IdsCasosEstudio SELECT pIdCasoEstudio, pIdCaso, pIdEstudio;
        
        SELECT CONCAT('OK', pIdCaso) Mensaje;
	COMMIT;
END $$
DELIMITER ;
