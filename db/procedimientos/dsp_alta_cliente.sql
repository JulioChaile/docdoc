DROP PROCEDURE IF EXISTS `dsp_alta_cliente`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_cliente`(pIdRol int, pNombres varchar(30), 
			pApellidos varchar(30), pUsuario varchar(120), pPassword varchar(255), pEmail varchar(120), 
            pObservaciones varchar(255), pTelefono varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Procedimiento de uso interno de la base de datos que permite crear un usuario,
    controlando que el email no se encuentre en uso ya.
    Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.    
    */
    DECLARE pIdUsuario int;
    -- Control de parámetros vacíos
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
    IF pIdRol IS NOT NULL AND NOT EXISTS (SELECT IdRol FROM Roles WHERE IdRol = pIdRol AND Estado = 'A') THEN
		SELECT 'El rol indicado no existe en el sistema o se encuentra dado de baja.' Mensaje;
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

	INSERT INTO Usuarios VALUES(0, pIdRol, pNombres, pApellidos, pUsuario, pPassword,
								MD5(SUBSTRING(RAND(),3)), pEmail, 0, NULL, NOW(), 'N', 'A',
								pObservaciones, pTelefono);
	SET pIdUsuario = LAST_INSERT_ID();
    
    -- Audito
	INSERT INTO aud_Usuarios
	SELECT 0, NOW(), 'cliente', pIP, pUserAgent, pApp, 'ALTA', 'I', Usuarios.* 
	FROM Usuarios WHERE IdUsuario = pIdUsuario;
    
	SELECT CONCAT('OK', pIdUsuario) Mensaje;
END $$
DELIMITER ;
