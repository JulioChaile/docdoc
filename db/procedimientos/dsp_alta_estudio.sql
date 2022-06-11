DROP PROCEDURE IF EXISTS `dsp_alta_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_estudio`(pJWT varchar(500), pIdCiudad int, pEstudio varchar(70), 
					pDomicilio varchar(255), pTelefono varchar(50), pEspecialidades varchar(100),
                    pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un Estudio controlando que el nombre no se encuentre en uso ya.
    Devuelve OK + el id del Estudio creado o un Mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pUsuario varchar(120);
    DECLARE pMensaje varchar(100);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			show errors;
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
    IF pIdCiudad IS NULL THEN
		SELECT 'Debe indicar un ciudad.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEstudio IS NULL OR pEstudio = '' THEN
		SELECT 'Debe indicar el nombre del Estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pDomicilio IS NULL OR pDomicilio = '' THEN
		SELECT 'Debe indicar el Domicilio del Estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTelefono IS NULL OR pTelefono = '' THEN
		SELECT 'Debe indicar un teléfono de contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCiudad FROM Ciudades WHERE IdCiudad = pIdCiudad) THEN
		SELECT 'La ciudad indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstudio FROM Estudios WHERE Estudio = pEstudio) THEN
		SELECT 'El nombre del Estudio indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstudio FROM Estudios WHERE Domicilio = pDomicilio) THEN
		SELECT 'El Domicilio indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
	START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		SET pIdEstudio = (SELECT COALESCE(MAX(IdEstudio),0) + 1 FROM Estudios);
		
        INSERT INTO Estudios VALUES (pIdEstudio,pIdCiudad, pEstudio, pDomicilio, pTelefono, 'A', pEspecialidades);
        
        -- Auditoría
		INSERT INTO aud_Estudios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Estudios.* 
        FROM Estudios WHERE IdEstudio = pIdEstudio;
        
        CALL dsp_aux_inserts(pIdEstudio, pMensaje);
		
        IF pMensaje != 'OK' THEN
			SELECT 'Error al cargar los valores por defecto del estudio.' Mensaje;
            ROLLBACK;
            LEAVE PROC;
		END IF;
        
        SELECT CONCAT('OK', pIdEstudio) Mensaje;
	COMMIT;
END $$
DELIMITER ;
