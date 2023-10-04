DROP PROCEDURE IF EXISTS `dsp_alta_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_consulta`(pIdDifusion smallint, 
		pApynom varchar(100), pTelefono varchar(15), pTexto text, pDNI varchar(45),
        pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta una consulta desde la web. 
    Devuelve OK + id de la consulta creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdConsulta, pIdUsuarioCaso, pIdEstudio, pIdPersona, pIdObjetivo int;
    DECLARE pIdCaso, pIdMovimientoCaso bigint;
    DECLARE pDomicilio varchar(500);
    -- Control de parámetros vacíos
    IF pApynom IS NULL OR pApynom = '' THEN
		SELECT 'Debe indicar su nombre y apellido.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTelefono IS NULL OR pTelefono = '' THEN
		SELECT 'Debe indicar su número de teléfono.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTexto IS NULL OR pTexto = '' THEN
		SELECT 'Debe indicar el motivo de su consulta.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF pIdDifusion IS NOT NULL AND pIdDifusion != 0 AND NOT EXISTS (SELECT IdDifusion FROM Difusiones WHERE IdDifusion = pIdDifusion) THEN
		SELECT 'La campaña indicada no es válida.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        -- Guardo la consulta
		SET pIdConsulta = (SELECT COALESCE(MAX(IdConsulta),0) + 1 FROM Consultas);
        
        INSERT INTO Consultas VALUES(pIdConsulta, pIdDifusion, pApynom, pTelefono, pTexto, NOW(), 'A');

        -- Guardo como un caso nuevo con estado pendiente
        SET pIdCaso = (SELECT COALESCE(MAX(IdCaso),0) + 1 FROM Casos);
        
        INSERT INTO Casos VALUES(pIdCaso, 12, null, 10, 15, 16, 21, 8, pApynom, null, NOW(),
								null, NOW(), '', NULL, 'P', NULL, 'N');

        SET pIdEstudio = 5;
		
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) FROM UsuariosCaso);
        
        SET @a = pIdUsuarioCaso;
        
        INSERT IGNORE INTO UsuariosCaso
        SELECT 	@a := @a + 1, pIdCaso, pIdEstudio, IdUsuario, 'A', 'S'
        FROM 	UsuariosEstudio WHERE IdEstudio = pIdEstudio; 
        
        INSERT INTO aud_UsuariosCaso
		SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA#CASO', 'I', UsuariosCaso.* 
		FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio;

        -- Guardo la persona en el caso
        SET pIdPersona = (SELECT COALESCE(MAX(IdPersona),0) + 1 FROM Personas);

        IF EXISTS (SELECT 1 FROM Padron2019 WHERE DNI = CONCAT('', pDNI) LIMIT 1) THEN
            SET pDomicilio = (SELECT CONCAT(p.DOMICILIO, ', ', p.Localidad, ', ', p.Departamento)
                                FROM		Padron2019 p
                                WHERE		DNI = CONCAT('', pDNI)
                                LIMIT		1);
        ELSEIF EXISTS (SELECT 1 FROM Padron WHERE DNI = CONCAT('', pDNI) LIMIT 1) THEN
            SET pDomicilio = (SELECT CONCAT(p.DOMICILIO, ', ', p.LOCALIDAD, ', ', p.DEPARTAMENTO)
                                FROM		Padron p
                                WHERE		DNI = CONCAT('', pDNI)
                                LIMIT		1);
        ELSE
            SET pDomicilio = '';
        END IF;
            
        INSERT INTO Personas VALUES(pIdPersona, pIdEstudio, 'F', UPPER(pApynom), '', 
                                   '', pDNI, '', pDomicilio, NOW(), 'A', NULL);

        INSERT INTO aud_Personas
        SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA#PERSONA#CASO', 'I', Personas.* 
        FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio;

        INSERT INTO PersonasCaso VALUES(pIdCaso, pIdPersona, NULL, 'S', 'Actor', null, null);

		INSERT INTO aud_PersonasCaso
		SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA#PERSONA#CASO', 'I', PersonasCaso.* 
		FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;

        -- Guardo como caso pendiente
        INSERT INTO CasosPendientes  SELECT 0, '', pApynom, pDomicilio, pTelefono, 1, 35, 8, '',
                                            NULL, NULL, NULL, 5, NOW(), '', pIdCaso, pIdPersona, NOW(), null, null;

        -- Guardo el telefono
        INSERT INTO TelefonosPersona VALUES(pIdPersona, pTelefono, NOW(), 'Consulta de DocDoc', 'S');

        -- Guardo la consulta como movimiento del caso
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio);
        
        INSERT INTO MovimientosCaso VALUES (0, pIdCaso, 236, pIdUsuarioCaso, null, pTexto,
											NOW(), NOW(), null, null, null, null, 'primary');
        
        SET pIdMovimientoCaso = LAST_INSERT_ID();
        
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA', 'I', MovimientosCaso.* 
		FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;

        -- Creo y asocio un objetivo 'Consulta' al movimiento
        SET pIdObjetivo = (SELECT COALESCE(MAX(IdObjetivo),0) + 1 FROM Objetivos);
        
        INSERT INTO Objetivos VALUES(pIdObjetivo, pIdCaso, 'Consulta de DocDoc', NOW(), 14, 'warning');

        INSERT INTO MovimientosObjetivo VALUES (pIdObjetivo, pIdMovimientoCaso);

        -- Ingreso objetivos por defecto
        INSERT IGNORE INTO Objetivos
        SELECT @o := @o + 1, pIdCaso, oe.ObjetivoEstudio, NOW(), oe.IdTipoMov, oe.ColorMov
        FROM ObjetivosEstudio oe, (SELECT @o := MAX(IdObjetivo) FROM Objetivos) s
        WHERE oe.IdEstudio = 5;
        
        SELECT 'OK' Exito, pIdCaso IdCaso, pIdPersona IdPersona;
	COMMIT;
END $$
DELIMITER ;
