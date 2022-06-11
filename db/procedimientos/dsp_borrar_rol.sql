DROP PROCEDURE IF EXISTS `dsp_borrar_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_rol`(pToken varchar(500), pIdRol int, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
	Permite borrar un Rol existente y sus permisos asociados controlando que no existan usuarios asociados.
    No puede borrar un rol menor o igual a 2, roles reservados del sistema.
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
    SET pIdUsuario = f_valida_sesion_usuario(pToken);
    IF pIdUsuario = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdRol IS NULL THEN
		SELECT 'Debe indicar un rol.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdRol FROM Roles WHERE IdRol = pIdRol) THEN
		SELECT 'El rol indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
	IF EXISTS(SELECT IdRol FROM Usuarios WHERE IdRol = pIdRol) THEN
		SELECT 'No se puede borrar el rol. Existen usuarios asociados.' Mensaje;
		LEAVE PROC;
	END IF;
	-- Borra el rol y sus Permisos
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);
		-- Audito
		INSERT INTO aud_PermisosRol
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRA', 'B', PermisosRol.* 
        FROM PermisosRol WHERE IdRol = pIdRol;
        
        DELETE FROM PermisosRol WHERE IdRol = pIdRol;
		
        -- Audito
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRA', 'B', Roles.* 
        FROM Roles WHERE IdRol = pIdRol;
        
        DELETE FROM Roles WHERE IdRol = pIdRol;
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
