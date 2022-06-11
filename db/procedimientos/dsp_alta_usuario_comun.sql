DROP PROCEDURE IF EXISTS `dsp_alta_usuario_comun`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario_comun`(pJWT varchar(500), pIdRol int, pNombres varchar(30), 
			pApellidos varchar(30), pUsuario varchar(120), pPassword varchar(255), pEmail varchar(120), 
            pObservaciones varchar(255), pTelefono varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50),
            OUT pMensaje varchar(100)	)
PROC: BEGIN
	/*
    Procedimiento de uso interno de la base de datos que permite crear un usuario,
    controlando que el email no se encuentre en uso ya.
    Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.    
    */
    DECLARE pIdUsuario, pIdUsuarioGestion int;
    DECLARE pUsuarioAud, pDebeCambiarPass varchar(120);
    -- Manejo de errores en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			SET pMensaje = 'Error en la transacción interna. Contáctese con el administrador.';
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pNombres IS NULL OR pNombres = '' THEN
		SET pMensaje = 'Debe indicar un nombre.';
        LEAVE PROC;
	END IF;
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SET pMensaje = 'Debe indicar un apellido.';
        LEAVE PROC;
	END IF;
    IF pUsuario IS NULL OR pUsuario = '' THEN
		SET pMensaje = 'Debe indicar un nombre de ususario.';
        LEAVE PROC;
	END IF;
    IF pPassword IS NULL OR pPassword = '' THEN
		SET pMensaje = 'Password inválido.';
        LEAVE PROC;
	END IF;
    IF pEmail IS NULL OR pEmail = '' THEN
		SET pMensaje = 'Debe indicar un email.';
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF pIdRol IS NOT NULL AND NOT EXISTS (SELECT IdRol FROM Roles WHERE IdRol = pIdRol AND Estado = 'A') THEN
		SET pMensaje = 'El rol indicado no existe en el sistema o se encuentra dado de baja.';
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Usuario FROM Usuarios WHERE Usuario = pUsuario) THEN
		SET pMensaje = 'El nombre de usuario indicado ya se encuentra en uso.';
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Email FROM Usuarios WHERE Email = pEmail) THEN
		SET pMensaje = 'El email indicado ya se encuentra en uso.';
        LEAVE PROC;
	END IF;
	
    SET pUsuarioAud = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);

    SET pDebeCambiarPass = 'S';

    IF pObservaciones = 'cadete' OR pObservaciones = 'Cadete' OR pObservaciones = 'CADETE' THEN
        SET pDebeCambiarPass = 'N';
    END IF;
    
	INSERT INTO Usuarios VALUES(0, pIdRol, pNombres, pApellidos, pUsuario, pPassword,
								MD5(SUBSTRING(RAND(),3)), pEmail, 0, NULL, NOW(), pDebeCambiarPass, 'A',
								pObservaciones, pTelefono);
	SET pIdUsuario = LAST_INSERT_ID();
    
    -- Audito
	INSERT INTO aud_Usuarios
	SELECT 0, NOW(), pUsuarioAud, pIP, pUserAgent, pApp, 'ALTA', 'I', Usuarios.* 
	FROM Usuarios WHERE IdUsuario = pIdUsuario;
    
	SET pMensaje = CONCAT('OK', pIdUsuario);
END $$
DELIMITER ;
