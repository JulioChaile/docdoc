DROP PROCEDURE IF EXISTS `dsp_alta_persona_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_persona_estudio`(pJWT varchar(500), pIdEstudio int, 
				pTipo char(1), pNombres varchar(50), pApellidos varchar(50),
				pEmail varchar(120), pDocumento varchar(8), pCuit char(11), pDomicilio varchar(120), 
                pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta una persona en un estudio, controlando que si es persona física el documento no exista o si 
    es persona jurídica que el CUIT no exista. Devuelve OK + Id de la persona creada o un mesaje d error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdPersona int;
    DECLARE pUsuario varchar(100);    
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipo IS NULL OR pTipo = '' OR pTipo NOT IN('F','J') THEN
		SELECT 'Debe indicar el tipo de persona que quiere crear.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNombres IS NULL OR pNombres = '' THEN
		SELECT IF(pTipo = 'F', 'El nombre de la persona física es obligatorio.', 'La razón social de la persona jurídica es obligatoria.' ) Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipo = 'F' AND pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar el apellido de la persona.' Mensaje;
		LEAVE PROC;        
	END IF;
    
    SET pApellidos = NULLIF(pApellidos,'');
    SET pEmail = NULLIF(pEmail,'');
    SET pDocumento = NULLIF(pDocumento,'');
    SET pCuit = NULLIF(pCuit,'');
    SET pCuit = NULLIF(pDomicilio,'');
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion AND Estado = 'A') THEN	
		SELECT 'No tiene permisos para realizar esta acción ya que no está activo en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEmail IS NOT NULL AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Email = pEmail) THEN
		SELECT 'El email indicado ya está en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipo = 'F' THEN
		IF pDocumento IS NOT NULL AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Tipo = 'F'
        AND Documento = pDocumento) THEN
			SELECT 'El documento indicado ya está en uso.' Mensaje;
            LEAVE PROC;
		END IF;
	END IF;    
    IF pTipo = 'J' THEN
		IF pCuit IS NOT NULL AND CHAR_LENGTH(pCuit) != 11 THEN
			SELECT 'El CUIT debe tener 11 dígitos numericos.' Mensaje;
            LEAVE PROC;
		END IF;
		IF pCuit IS NOT NULL AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Tipo = 'J' AND Cuit = pCuit) THEN
			SELECT 'Ya existe una persona jurídica con el CUIT indicado.' Mensaje;
            LEAVE PROC;
		END IF;
    END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        SET pIdPersona = (SELECT COALESCE(MAX(IdPersona),0) + 1 FROM Personas);
        
        INSERT INTO Personas VALUES (pIdPersona, pIdEstudio, pTipo, pNombres, pApellidos, pEmail, 
									pDocumento, pCuit, pDomicilio, NOW(), 'A', NULL);
        
		INSERT INTO aud_Personas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Personas.* 
        FROM Personas WHERE IdPersona = pIdPersona;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
