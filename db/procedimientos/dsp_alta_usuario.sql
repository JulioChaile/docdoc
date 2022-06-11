DROP PROCEDURE IF EXISTS `dsp_alta_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario`(pJWT varchar(500), pIdRol int, pNombres varchar(30), 
		pApellidos varchar(30), pUsuario varchar(120), pPassword varchar(255), pEmail varchar(120),
        pObservaciones varchar(255), pTelefono varchar(20), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta un usuario controlando que el nombre de usuario y el email 
    no se encuentren en uso ya. Inicialmente la cuenta se crea con DebeCambiarPass = 'S' 
    para forzar un blanqueo de contraseña. 
    Devuelve OK + Id del usuario creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pMensaje varchar(100);
    -- Manejo de errores en la transacción
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
    IF pIdRol IS NULL THEN
		SELECT 'No se puede dar de alta un usuario final con este procedimiento.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		CALL dsp_alta_usuario_comun(pJWT, pIdRol, pNombres, pApellidos, pUsuario, pPassword, pEmail, pObservaciones, pTelefono,
								pIP, pUserAgent, pApp, pMensaje);
		SELECT pMensaje Mensaje;
        
        IF SUBSTRING(pMensaje,1,2) = 'OK' THEN
			COMMIT;
		ELSE
            ROLLBACK;
		END IF;
END $$
DELIMITER ;
