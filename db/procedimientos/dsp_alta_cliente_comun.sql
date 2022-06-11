DROP PROCEDURE IF EXISTS `dsp_alta_cliente_comun`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_cliente_comun`(pNombres varchar(30), 
			pApellidos varchar(30), pUsuario varchar(120), pPassword varchar(255), pEmail varchar(120), 
            pObservaciones varchar(255), pTelefono varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50),
            OUT pMensaje varchar(100)	)
PROC: BEGIN
	/*
    Procedimiento de uso interno de la base de datos que permite crear un usuario,
    controlando que el email no se encuentre en uso ya.
    Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.    
    */
    DECLARE pIdUsuario int;
    -- Manejo de errores en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			-- show errors;
			SET pMensaje = 'Error en la transacción interna. Contáctese con el administrador.';
		END;
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
    IF EXISTS (SELECT Usuario FROM Usuarios WHERE Usuario = pUsuario) THEN
		SET pMensaje = 'El nombre de usuario indicado ya se encuentra en uso.';
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Email FROM Usuarios WHERE Email = pEmail) THEN
		SET pMensaje = 'El email indicado ya se encuentra en uso.';
        LEAVE PROC;
	END IF;
    
	INSERT INTO Usuarios VALUES(0, NULL, pNombres, pApellidos, pUsuario, pPassword,
								MD5(SUBSTRING(RAND(),3)), pEmail, 0, NULL, NOW(), 'N', 'A',
								pObservaciones, pTelefono);
	SET pIdUsuario = LAST_INSERT_ID();
    
	SET pMensaje = CONCAT('OK', pIdUsuario);
END $$
DELIMITER ;
