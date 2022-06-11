DROP PROCEDURE IF EXISTS `dsp_modificar_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_usuario`(pJWT varchar(500), pIdUsuario int, 
		pIdRol int, pUsuario varchar(120), pNombres varchar(30), pApellidos varchar(30), pEmail varchar(120),
        /*pPassword varchar(255),*/ pObservaciones varchar(255), pTelefono varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar los atributos Nombres, Apellidos y/o Email de un usuario, 
    controlando quel email no se encuentre en uso ya. Si pIdRol no es nulo, 
    la persona que ejecute este procedimiento debe tener el permiso "AsignarRol". 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    -- Manejo de errores en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			SELECT 'Error en la transacción. Contáctese con el administrado.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
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
    IF pEmail IS NULL OR pEmail = '' THEN
		SELECT 'Debe indicar un email.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pUsuario IS NULL OR pUsuario = '' THEN
		SELECT 'Debe indicar un nombre de usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    /*
    IF pPassword IS NULL OR pPassword = '' THEN
		SELECT 'La contraseña es obligatoria.' Mensaje;
        LEAVE PROC;
	END IF;
    */
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdRol IS NOT NULL AND NOT EXISTS (SELECT IdRol FROM Roles WHERE IdRol = pIdRol AND Estado = 'A') THEN
		SELECT 'El rol indicado no existe en el sistema o se encuentra dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdRol IS NOT NULL AND NOT EXISTS (SELECT pr.IdPermiso FROM Permisos p INNER JOIN PermisosRol pr
    WHERE p.Permiso = 'AsignarPermisosRol' AND pr.IdRol = (SELECT IdRol FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion))
    AND pIdRol != (SELECT IdRol FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'No tiene permisos para realizar esta acción.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Email FROM Usuarios WHERE Email = pEmail AND IdUsuario != pIdUsuario) THEN
		SELECT 'El email indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Usuario FROM Usuarios WHERE Usuario = pUsuario AND IdUsuario != pIdUsuario) THEN
		SELECT 'El nombre de usuario ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		IF pIdRol IS NULL THEN
			SET pIdRol = (SELECT IdRol FROM Usuarios WHERE IdUsuario = pIdUsuario);
		END IF;
        
        UPDATE	Usuarios
        SET		IdRol = pIdRol,
				Usuario = pUsuario,
                /*Password = pPassword,*/
				Nombres = pNombres,
				Apellidos = pApellidos,
                Email = pEmail,
                Observaciones = pObservaciones,
                Telefono = pTelefono
		WHERE 	IdUsuario = pIdUsuario;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
