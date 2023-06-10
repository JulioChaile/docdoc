DROP PROCEDURE IF EXISTS `dsp_alta_personas_caso_comun`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_personas_caso_comun`(pJWT varchar(500), pIdCaso bigint, pPersonasCaso json, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50), OUT pMensaje varchar(1000))
PROC: BEGIN
	/*
    Permite dar de alta personas y asignarlas a un caso. Se controla si la persona existe en el estudio y si existe,
    no se modifican sus datos personales. Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdPersona, pIdEstudio, pIndice, pIndiceTel, pIndiceParams int;
    /*DECLARE pIdRTC smallint;*/
    DECLARE pTipoPersona, pEsPrincipal char(1);
    DECLARE pNombres, pApellidos varchar(50);
    DECLARE pDocumento varchar(100);
	DECLARE pDomicilio varchar(200);
    DECLARE pCuit char(11);
    DECLARE pUsuario, pEmail varchar(120);
    DECLARE pObservaciones, pValor varchar(255);
    DECLARE pParametros, pTelefonosPersona, pParametrosFinal, pObj, pPersonaCaso json;
    DECLARE pTelefono varchar(20);
	DECLARE pDetalle varchar(200);
	DECLARE pTelefonoEsPrincipal char(1);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- Obtener el código de error y el mensaje de error
			DECLARE errorCode INT;
			DECLARE errorMessage VARCHAR(255);
			GET DIAGNOSTICS CONDITION 1 errorCode = MYSQL_ERRNO, errorMessage = MESSAGE_TEXT;

			-- Construir el mensaje de error
			SET pMensaje = CONCAT('Error en la transacción interna. Código: ', errorCode, '. Mensaje: ', errorMessage);
		END;
	-- Validación de sesión
    SET @nromensaje = 0;
    SET pMensaje = '';
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Ocurrió un problema con su sesión.\n');
	END IF;
	-- Control de parámetros vacíos
    IF pIdCaso IS NULL THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe indicar un caso.\n');
	END IF;
    IF pPersonasCaso IS NULL OR JSON_LENGTH(pPersonasCaso) = 0 THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe indicar las personas que quiere agregar al caso.\n');
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso) THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'El caso indicado no existe en el sistema.\n');
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion AND IdRol IS NULL) THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe estar registrado como abogado para poder realizar esta acción.\n');
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A') THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Usted no es un/a abogado/a activo en ningún estudio.\n');
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion) THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Usted no tiene acceso al caso.' );
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('E','A')) THEN
		SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Usted no tiene permiso de escritura ni administración sobre el caso.\n');
	END IF;
    -- Muestro los mensajes acumulados
    IF @nromensaje > 0 THEN
        LEAVE PROC;
    END IF;
	
	SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
	SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
	
    SET pIndice = 0;
	loop_personas: WHILE pIndice < JSON_LENGTH(pPersonasCaso) DO
		
        SET pPersonaCaso = JSON_EXTRACT(pPersonasCaso, CONCAT('$[', pIndice, ']'));
        
		SET pNombres = JSON_UNQUOTE(JSON_EXTRACT(pPersonaCaso, '$.Nombres'));
		SET pApellidos = JSON_UNQUOTE(JSON_EXTRACT(pPersonaCaso, '$.Apellidos'));
		
		IF pNombres IS NULL OR pNombres = '' THEN
			SET pMensaje = CONCAT(pMensaje, 'No se pueden indicar personas sin nombre.\n');
			LEAVE PROC;
		END IF;
		
		SET @Persona = CONCAT(IF(pApellidos != NULL,
								CONCAT(pApellidos, ', ')
								,''), pNombres);
		
		SET pTipoPersona = JSON_UNQUOTE(JSON_EXTRACT(pPersonaCaso, '$.TipoPersona'));
		IF pTipoPersona IS NULL OR pTipoPersona = '' OR pTipoPersona NOT IN('F','J') THEN
			SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe indicar si la persona "', @Persona, '" es física o jurídica.\n');
			LEAVE PROC;
		END IF;
        
        SET pEsPrincipal = JSON_UNQUOTE(JSON_EXTRACT(pPersonaCaso, '$.EsPrincipal'));
        IF pEsPrincipal = 'S' AND EXISTS (SELECT IdPersona FROM PersonasCaso WHERE IdCaso = pIdCaso AND EsPrincipal = 'S') THEN
			SET @Principal = (SELECT CONCAT(Apellidos, Nombres) FROM Personas INNER JOIN PersonasCaso USING (IdPersona) WHERE IdCaso = pIdCaso);
			SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
				'La persona "', @Principal, '" está marcada como principal y solo puede haber una persona principal por caso.\n');
		LEAVE PROC;
		END IF;
        IF pEsPrincipal IS NULL OR pEsPrincipal = '' THEN
			SET pEsPrincipal = 'N';
		END IF;
        
        SET pEmail = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].Email')));
        SET pObservaciones = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].Observaciones')));
		/*
		SET pIdRTC = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].IdRTC')));
		IF pIdRTC IS NULL OR pIdRTC = '' THEN
			SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe indicar el rol que cumple la persona en el caso.\n');
		END IF;
		IF NOT EXISTS (SELECT IdRTC FROM RolesTipoCaso WHERE IdRTC = pIdRTC AND IdTipoCaso = (SELECT IdTipoCaso 
		FROM Casos WHERE IdCaso = pIdCaso)) THEN
			SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
				'El rol indicado para la persona "',@Persona, '" es incompatible con el tipo de caso.\n');
		END IF;
		*/ 
		
        SET pIdPersona = 0;
		CASE pTipoPersona
		WHEN 'F' THEN
			BEGIN
				IF pApellidos IS NULL OR pApellidos = '' THEN
					SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe indicar el apellido de la persona "', @Persona,'".\n');
				END IF;
                
				SET pDocumento = JSON_UNQUOTE(JSON_EXTRACT(pPersonaCaso, '$.Documento'));
				IF pDocumento IS NOT NULL AND CHAR_LENGTH(pDocumento) > 8 THEN
					SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
						'Debe indicar el documento de la persona "', @Persona, '" y este debe tener como máximo 8 números.\n');
				END IF;
				
                -- Checkeo si la persona existe. Si no existe, se crea.
				IF pDocumento IS NOT NULL AND pDocumento != '' AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio 
				AND Documento = pDocumento) THEN
					SET pIdPersona = (	SELECT 	IdPersona FROM 	Personas 
										WHERE 	IdEstudio = pIdEstudio AND 
												Documento = pDocumento AND
												Tipo = 'F');
				ELSE
					SET pCuit = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].CUIT')));
					SET pDomicilio = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].Domicilio')));
					/*
					IF pCuit IS NOT NULL AND pCuit != '' AND CHAR_LENGTH(pCuit) != 11 THEN
						SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
							'El Nº de CUIT de la persona "', @Persona, '" debe contener 11 números sin guiones.\n');
					END IF;		
					*/								
				END IF;
			END;
		WHEN 'J' THEN
			BEGIN
				SET pCuit = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].CUIT')));
				/*
				IF pCuit IS NOT NULL AND pCuit != '' AND CHAR_LENGTH(pCuit) != 11 THEN
					SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
						'El Nº de CUIT de la persona "', @Persona, '" debe contener 11 números sin guiones.\n');
				END IF;
				*/
                
                -- Si no se reinicia el valor del documento, queda con el valor de la iteración anterior
                SET pDocumento = null;
                
                IF EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Cuit = pCuit AND pCuit IS NOT NULL AND pCuit != '' AND pCuit != 'null') THEN
					SET pIdPersona  = (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Cuit = pCuit AND Tipo = 'J');
				ELSE
					
					SET pDomicilio = JSON_UNQUOTE(JSON_EXTRACT(pPersonasCaso, CONCAT('$[',pIndice,'].Domicilio')));					
					
					/*
					IF pIdRTC IS NULL OR pIdRTC = '' THEN
						SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 'Debe indicar el rol que cumple la persona en el caso.\n');
					END IF;
					IF NOT EXISTS (SELECT IdRTC FROM RolesTipoCaso WHERE IdRTC = pIdRTC AND IdTipoCaso = (SELECT IdTipoCaso 
					FROM Casos WHERE IdCaso = pIdCaso)) THEN
						SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
							'El rol indicado para la persona "',@Persona, '" es incompatible con el tipo de caso.\n');
					END IF;
					*/
				END IF;

				IF pCuit = 'null' THEN
					SET pCuit = NULL;
				END IF;
                
			END;
		END CASE;
        
        IF pIdPersona != 0 AND EXISTS (SELECT IdPersona FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona) THEN
			SET pIndice = pIndice + 1;
			ITERATE loop_personas;
        END IF;
        
        IF @nromensaje > 0 THEN
			SET pIndice = pIndice + 1;
			ITERATE loop_personas;
		END IF;
        
        IF pIdPersona = 0 THEN
			/*
			IF EXISTS (SELECT IdEstudio FROM Personas WHERE IdEstudio = pIdEstudio AND Documento = pDocumento) THEN
				SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
					'Ya existe una persona con el documento ', pDocumento, ' en el estudio.\n');
				LEAVE PROC;
			END IF;

			IF EXISTS (SELECT IdEstudio FROM Personas WHERE IdEstudio = pIdEstudio AND Cuit = pCuit) THEN
				SET pMensaje = CONCAT(pMensaje, @nromensaje := @nromensaje + 1, '- ', 
					'Ya existe una persona con el cuit ', pCuit, ' en el estudio.\n');
				LEAVE PROC;
			END IF;
			*/
            
			SET pIdPersona = (SELECT COALESCE(MAX(IdPersona),0) + 1 FROM Personas);
            
			INSERT INTO Personas VALUES(pIdPersona, pIdEstudio, pTipoPersona, UPPER(pNombres), UPPER(pApellidos), 
										LOWER(pEmail), pDocumento, pCuit, UPPER(pDomicilio), NOW(), 'A');
			-- Audito
			INSERT INTO aud_Personas
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA#PERSONA#CASO', 'I', Personas.* 
			FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio;
		END IF;
		
        
        SET pParametros = JSON_UNQUOTE(JSON_EXTRACT(pPersonaCaso, '$.Parametros'));
        SET pParametrosFinal = JSON_ARRAY();
        SET pIndiceParams = 0;
        loop_params: WHILE pIndiceParams < JSON_LENGTH(pParametros) DO
			SET pValor = JSON_EXTRACT(pParametros, CONCAT('$[', pIndiceParams, '].Valor'));
            IF pValor IS NULL OR TRIM(pValor) = '' THEN
				SET pIndiceParams = pIndiceParams + 1;
				ITERATE loop_params;
			END IF;
            SET pObj = JSON_EXTRACT(pParametros, CONCAT('$[', pIndiceParams, ']'));
            SET pParametrosFinal = JSON_ARRAY_INSERT(pParametrosFinal,'$[0]', pObj);
            
			SET pIndiceParams = pIndiceParams + 1;
        END WHILE;
        
		INSERT INTO PersonasCaso VALUES(pIdCaso, pIdPersona, /*pIdRTC*/ NULL, pEsPrincipal, NULLIF(pObservaciones,''), pParametrosFinal, null);
		-- Audito
		INSERT INTO aud_PersonasCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA#PERSONA#CASO', 'I', PersonasCaso.* 
		FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;
		
		
		SET pTelefonosPersona = JSON_EXTRACT(pPersonaCaso, '$.Telefonos');
        SET pIndiceTel = 0;
		-- Agrego teléfonos si no existen
		loop_tel: WHILE pIndiceTel < JSON_LENGTH(pTelefonosPersona) DO
			SET pTelefono = SUBSTRING(JSON_UNQUOTE(JSON_EXTRACT(pTelefonosPersona,CONCAT('$[', pIndiceTel, '].Telefono'))), 1, 20);
			SET pDetalle = SUBSTRING(JSON_UNQUOTE(JSON_EXTRACT(pTelefonosPersona,CONCAT('$[', pIndiceTel, '].Detalle'))), 1, 200);
			SET pTelefonoEsPrincipal = SUBSTRING(JSON_UNQUOTE(JSON_EXTRACT(pTelefonosPersona,CONCAT('$[', pIndiceTel, '].EsPrincipal'))), 1, 1);
			IF NOT EXISTS (SELECT IdPersona FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono) THEN
				INSERT INTO TelefonosPersona VALUES(pIdPersona, pTelefono, NOW(), pDetalle, pTelefonoEsPrincipal);
			END IF;                        
			
			SET pIndiceTel = pIndiceTel + 1;
		END WHILE;
		
		SET pIndice = pIndice + 1;
	END WHILE;
	
    IF @nromensaje = 0 THEN
		SET pMensaje = CONCAT('OK', pIdPersona);
	END IF;
END $$
DELIMITER ;
