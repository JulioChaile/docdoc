DROP PROCEDURE IF EXISTS `dsp_activar_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_rol`(pJWT varchar(500), pIdRol int, pIP varchar(40),
						pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado del Rol a Activo siempre y cuando no esté activo ya. 
    Devuelve OK o el mensaje de error en Mensaje.
    */
    DECLARE pIdUsuario int;
	DECLARE pUsuario varchar(120);
    -- Manejo de error en la transacción    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
        ROLLBACK;
	END;
    -- Controla Parámetros.
    SET pIdUsuario = f_valida_sesion_usuario(pJWT);
    IF pIdUsuario = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS(SELECT Estado FROM Roles WHERE IdRol = pIdRol AND Estado = 'A') THEN
		SELECT 'OK' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);
		
        -- Antes
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'A', Roles.* 
        FROM Roles WHERE IdRol = pIdRol;
		
		UPDATE Roles SET Estado = 'A' WHERE IdRol = pIdRol;
		
        -- Después
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', Roles.* 
        FROM Roles WHERE IdRol = pIdRol;
		SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
