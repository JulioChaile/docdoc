DROP PROCEDURE IF EXISTS `dsp_modificar_persona_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_persona_estudio`(pJWT varchar(500), pIdPersona int,
			pNombres varchar(50), pApellidos varchar(50), pEmail varchar(120), pDocumento varchar(8), pCuit char(11),
            pDomicilio varchar(120), pEsPrincipal char(1), pIdCaso bigint, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar los datos de una persona del estudio.
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pUsuario varchar(100);
    DECLARE pTipo char(1);
    DECLARE pDetalle varchar(200);
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
    SET pTipo = (SELECT Tipo FROM Personas WHERE IdEstudio = pIdEstudio AND IdPersona = pIdPersona);
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
    SET pDomicilio = NULLIF(pDomicilio,'');
    SET pEsPrincipal = NULLIF(pEsPrincipal,'');
    SET pIdCaso = NULLIF(pIdCaso,'');
    -- Control de parámetros incorrectos
    IF pIdEstudio IS NULL THEN
		SELECT 'No tiene permisos para realizar esta acción ya que no está activo en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEmail IS NOT NULL AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Email = pEmail AND IdPersona != pIdPersona) THEN
		SELECT 'El email indicado ya está en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipo = 'F' THEN
		IF pDocumento IS NOT NULL AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Tipo = 'F'
        AND Documento = pDocumento AND IdPersona != pIdPersona) THEN
			SELECT 'El documento indicado ya está en uso.' Mensaje;
            LEAVE PROC;
		END IF;
	END IF;    
    IF pTipo = 'J' THEN
		IF pCuit IS NOT NULL AND CHAR_LENGTH(pCuit) != 11 THEN
			SELECT 'El CUIT debe tener 11 dígitos numericos.' Mensaje;
            LEAVE PROC;
		END IF;
		IF pCuit IS NOT NULL AND EXISTS (SELECT IdPersona FROM Personas WHERE IdEstudio = pIdEstudio AND Tipo = 'J' AND Cuit = pCuit 
        AND IdPersona != pIdPersona) THEN
			SELECT 'Ya existe una persona jurídica con el CUIT indicado.' Mensaje;
            LEAVE PROC;
		END IF;
    END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
        INSERT INTO aud_Personas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR#PERSONA', 'A', Personas.* 
        FROM Personas WHERE IdPersona = pIdPersona;
        
        UPDATE	Personas
        SET		Nombres = pNombres,
				Apellidos = pApellidos,
                Email = pEmail,
				Documento = pDocumento,
                Cuit = pCuit,
                Domicilio = pDomicilio
		WHERE	IdPersona = pIdPersona;

        UPDATE  CasosPendientes
        SET     Domicilio = pDomicilio
        WHERE   IdPersona = pIdPersona;
        
        -- Antes
        INSERT INTO aud_Personas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR#PERSONA', 'D', Personas.* 
        FROM Personas WHERE IdPersona = pIdPersona;

        IF pIdCaso IS NOT NULL OR pIdCaso != '' THEN
            IF pEsPrincipal = 'S' THEN
                UPDATE  PersonasCaso
                SET     EsPrincipal = 'N'
                WHERE   IdCaso = pIdCaso;
            END IF;

            UPDATE  PersonasCaso
            SET     EsPrincipal = pEsPrincipal
            WHERE   IdCaso = pIdCaso AND IdPersona = pIdPersona;
        END IF;
        
        SELECT 'OK' Mensaje;
	COMMIT;
        
END $$
DELIMITER ;
