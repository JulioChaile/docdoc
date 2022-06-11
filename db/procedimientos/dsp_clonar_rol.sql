DROP PROCEDURE IF EXISTS `dsp_clonar_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_clonar_rol`(pJWT varchar(500), pIdRol int, pRol varchar(30),
											pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite clonar un rol a partir de un existente, pasándole el nombre, controlando que no exista ya. 
    Devuelve OK + Id o el mensaje de error en Mensaje.
    */
    DECLARE pIdRolNuevo int;
    DECLARE pRolNuevo varchar(30);
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
	IF EXISTS(SELECT Rol FROM Roles WHERE Rol = pRol) THEN
		SELECT 'El nombre del rol ya existe.' Mensaje;
		LEAVE PROC;
	END IF;
    IF (pRol IS NULL OR pRol = '') THEN
        SELECT 'Debe ingresar el nombre del rol.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Da de alta calculando el próximo id y clonando también la tabla permisos
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);
		SET pIdRolNuevo = (SELECT COALESCE(MAX(IdRol), 0) + 1 FROM Roles);
        SET pRolNuevo = (SELECT CONCAT(Rol, ' (', IdRol, ')') FROM Roles WHERE IdRol = pIdRol);
        INSERT INTO Roles VALUES (pIdRolNuevo, pRol, 'A', CONCAT('Clonado de ', pRolNuevo));
		-- Audito
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, NULL, 'I', Roles.* FROM Roles 
        WHERE IdRol = pIdRolNuevo;
        INSERT INTO PermisosRol
			SELECT	IdPermiso, pIdRolNuevo
            FROM	PermisosRol
            WHERE	IdRol = pIdRol;
		-- Audito
		INSERT INTO aud_PermisosRol
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, NULL, 'I', PermisosRol.* FROM PermisosRol
        WHERE IdRol = pIdRolNuevo;
        SELECT CONCAT('OK', pIdRolNuevo) Mensaje;
	COMMIT;
END $$
DELIMITER ;
