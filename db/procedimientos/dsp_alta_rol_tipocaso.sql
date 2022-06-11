DROP PROCEDURE IF EXISTS `dsp_alta_rol_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_rol_tipocaso`(pJWT varchar(500), pIdTipoCaso smallint, 
		pRol varchar(30), pParametros json, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta un rol al tipo de caso, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje. 
    */
    DECLARE pIdRTC smallint;
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
    IF pIdTipoCaso IS NULL THEN
		SELECT 'Debe indicar un tipo caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRol IS NULL OR pRol = '' THEN
		SELECT 'Debe indicar el nombre del rol.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso) THEN
		SELECT 'El tipo de caso no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdRTC FROM RolesTipoCaso WHERE Rol = pRol AND IdTipoCaso = pIdTipoCaso) THEN
		SELECT 'Ya existe un rol para el tipo de caso indicado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        SET pIdRTC = (SELECT COALESCE(MAX(IdRTC),0) + 1 FROM RolesTipoCaso);
        
        INSERT INTO RolesTipoCaso VALUE(pIdRTC, pIdTipoCaso, pRol, NULLIF(pParametros,'[]'));
        
        -- Auditoría
		INSERT INTO aud_RolesTipoCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', RolesTipoCaso.* 
        FROM RolesTipoCaso WHERE IdRTC = pIdRTC;
        
        SELECT CONCAT('OK', pIdRTC) Mensaje;
	COMMIT;
END $$
DELIMITER ;
