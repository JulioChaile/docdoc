DROP PROCEDURE IF EXISTS `dsp_modificar_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_estudio`(pJWT varchar(500), pIdEstudio int, 
		pIdCiudad int, pEstudio varchar(70), pDomicilio varchar(255), 
        pTelefono varchar(50), pEspecialidades varchar(100), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un Estudio controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un Mensaje de error en Mensaje.
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un Estudio.' Mensaje;
        LEAVE PROC;
	END IF;
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
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El Estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdCiudad FROM Ciudades WHERE IdCiudad = pIdCiudad) THEN
		SELECT 'La ciudad indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstudio FROM Estudios WHERE Estudio = pEstudio AND IdEstudio != pIdEstudio) THEN
		SELECT 'El nombre del Estudio indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstudio FROM Estudios WHERE Domicilio = pDomicilio AND IdEstudio != pIdEstudio) THEN
		SELECT 'El Domicilio indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
	START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		
        -- Auditoría previa
		INSERT INTO aud_Estudios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Estudios.* 
		FROM Estudios WHERE IdEstudio = pIdEstudio;
        
		UPDATE 	Estudios 
        SET		IdCiudad = pIdCiudad,
				Estudio = pEstudio,
				Domicilio = pDomicilio,
                Telefono = pTelefono,
                Especialidades = pEspecialidades
		WHERE 	IdEstudio = pIdEstudio;
        
        -- Auditoría posterior
		INSERT INTO aud_Estudios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Estudios.* 
		FROM Estudios WHERE IdEstudio = pIdEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
