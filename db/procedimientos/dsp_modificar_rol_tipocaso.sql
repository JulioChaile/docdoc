DROP PROCEDURE IF EXISTS `dsp_modificar_rol_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_rol_tipocaso`(pJWT varchar(500), pIdRTC smallint, 
		pRol varchar(30), pParametros json, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un rol de un tipo de caso controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.  
    */
    DECLARE pIdTipoCaso smallint;
    DECLARE pIdUsuarioGestion int;
	DECLARE pUsuario varchar(120);
    -- Manejo de error en la transacción    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
        ROLLBACK;
	END;
    -- Controla Parámetros.
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdRTC IS NULL THEN
		SELECT 'Debe indicar un tipo caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRol IS NULL OR pRol = '' THEN
		SELECT 'Debe indicar el nombre del rol.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdRTC FROM RolesTipoCaso WHERE IdRTC = pIdRTC) THEN
		SELECT 'El rol de tipo de caso no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdTipoCaso = (SELECT IdTipoCaso FROM RolesTipoCaso WHERE IdRTC = pIdRTC);
    IF EXISTS (SELECT IdRTC FROM RolesTipoCaso WHERE Rol = pRol AND IdTipoCaso != pIdTipoCaso) THEN
		SELECT 'Ya existe un rol para el tipo de caso indicado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		
        -- Auditoría previa
		INSERT INTO aud_RolesTipoCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', RolesTipoCaso.* 
        FROM RolesTipoCaso WHERE IdRTC = pIdRTC;
        
        UPDATE	RolesTipoCaso
        SET		Rol = pRol,
				Parametros = pParametros
        WHERE	IdRTC = pIdRTC;
        
        -- Auditoría posterior
		INSERT INTO aud_RolesTipoCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', RolesTipoCaso.* 
        FROM RolesTipoCaso WHERE IdRTC = pIdRTC;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
