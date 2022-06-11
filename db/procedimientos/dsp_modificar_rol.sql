DROP PROCEDURE IF EXISTS `dsp_modificar_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_rol`(pJWT varchar(500), pIdRol int, pRol varchar(30),
									pObservaciones varchar(255), pIP varchar(40), pUserAgent varchar(255), 
                                    pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un Rol existente controlando que el nombre del rol no exista ya.
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
	IF EXISTS(SELECT Rol FROM Roles WHERE Rol = pRol AND IdRol != pIdRol) THEN
		SELECT 'El nombre del rol ya existe.' Mensaje;
		LEAVE PROC;
	END IF;
    IF (pRol IS NULL OR pRol = '') THEN
        SELECT 'Debe ingresar el nombre del rol.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);
		
        -- Antes
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Roles.* 
        FROM Roles WHERE IdRol = pIdRol;
		
		
        UPDATE Roles SET Rol = pRol, Observaciones = pObservaciones WHERE IdRol = pIdRol;
		
        -- Después
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Roles.* 
        FROM Roles WHERE IdRol = pIdRol;
		SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
