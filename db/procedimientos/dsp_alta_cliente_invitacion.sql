DROP PROCEDURE IF EXISTS `dsp_alta_cliente_invitacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_cliente_invitacion`(pIdCiudad int, pEstudio varchar(70), pDomicilio varchar(255), pTelefono varchar(50), pEspecialidades varchar(100),
					pIdCaso bigint, pNombres varchar(30), pApellidos varchar(30), pUsuario varchar(120),  pPassword varchar(255),
                    pEmail varchar(120), pTelefonoUsuario varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un Estudio, un Usuario, agregar el usuario al estudio y agregar el usuario a un caso.
    Devuelve OK + el id del Usuario creado o un Mensaje de error en Mensaje.
    */
    DECLARE pIdEstudio, pIdUsuario int;
    DECLARE pIdUsuarioCaso bigint;
    DECLARE pMensaje varchar(100);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- show errors;
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
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
    IF pNombres IS NULL OR pNombres = '' THEN
		SELECT 'Debe indicar un nombre.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar un apellido.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pUsuario IS NULL OR pUsuario = '' THEN
		SELECT 'Debe indicar un nombre de ususario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pPassword IS NULL OR pPassword = '' THEN
		SELECT 'Password inválido.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEmail IS NULL OR pEmail = '' THEN
		SELECT 'Debe indicar un email.' Mensaje;
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
    IF EXISTS (SELECT Usuario FROM Usuarios WHERE Usuario = pUsuario) THEN
		SELECT 'El nombre de usuario indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Email FROM Usuarios WHERE Email = pEmail) THEN
		SELECT 'El email indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
	START TRANSACTION;
		SET pIdEstudio = (SELECT COALESCE(MAX(IdEstudio),0) + 1 FROM Estudios);
		
        INSERT INTO Estudios VALUES (pIdEstudio, pIdCiudad, pEstudio, pDomicilio, pTelefono, 'A', pEspecialidades);
        
        CALL dsp_aux_inserts(pIdEstudio, pMensaje);
		
        IF pMensaje != 'OK' THEN
			SELECT 'Error al cargar los valores por defecto del estudio.' Mensaje;
            ROLLBACK;
            LEAVE PROC;
		END IF;

        CALL dsp_alta_cliente_comun(pNombres, pApellidos, pUsuario, pPassword, pEmail, 
								'Cliente invitado.', pTelefonoUsuario, pIP, pUserAgent, pApp, pMensaje);
        
        IF SUBSTRING(pMensaje,1,2) != 'OK' THEN
            ROLLBACK;
			SELECT pMensaje Mensaje;
            LEAVE PROC;
		END IF;

        SET pIdUsuario = SUBSTRING(pMensaje, 3);
        INSERT INTO UsuariosEstudio VALUES(pIdEstudio, pIdUsuario, pIdEstudio, NULL, (SELECT IdRolEstudio FROM RolesEstudio WHERE IdEstudio = pIdEstudio AND RolEstudio = 'Abogado' ), 'A');
        
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso), 0)+1 FROM UsuariosCaso);
            
        INSERT INTO UsuariosCaso SELECT pIdUsuarioCaso, pIdCaso, pIdEstudio, pIdUsuario, 'A', 'N';
        
        SELECT CONCAT('OK', pIdUsuario) Mensaje;
	COMMIT;
END $$
DELIMITER ;
