DROP PROCEDURE IF EXISTS `dsp_actualizar_permisos_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_actualizar_permisos_rol`(pToken varchar(500), pIdRol int, 
								pPermisos json, pIP varchar(40), 
                                pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite actualizar los permisos de un rol. Elimina todos los permisos actuales 
    y da de alta los contenidos en pPermisos. Renueva el token de los usuarios
    que tengan el rol indicado.
    Devuelve OK o un mensaje de error en Mensaje.
    */
	-- Declaración de variables 
    DECLARE pIdUsuarioGestion, pIndice, pIdPermiso int;
    DECLARE pUsuarioAud varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- SHOW ERRORS;
            SELECT 'Error en la transacción.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
	SET pIdUsuarioGestion = f_valida_sesion_usuario(pToken);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros vacíos
    IF (pIdRol IS NULL) THEN
		SELECT 'Debe indicar un rol.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdRol FROM Roles WHERE IdRol = pIdRol AND Estado = 'A') THEN
		SELECT 'El rol indicado no existe en el sistema o se encuentra dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;    
    START TRANSACTION;
		SET pUsuarioAud = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Auditoría anterior
		INSERT INTO aud_PermisosRol
        SELECT 0,NOW(), pUsuarioAud, pIP, pUserAgent, pApp, 'BORRAR', 'B',
        PermisosRol.* FROM PermisosRol WHERE IdRol = pIdRol;
        
        DELETE FROM PermisosRol WHERE IdRol = pIdRol;
        
        SET pIndice = 0;
        WHILE (pIndice < JSON_LENGTH(pPermisos)) DO
			SET pIdPermiso = JSON_UNQUOTE(JSON_EXTRACT(pPermisos,CONCAT('$[',pIndice,']')));
			INSERT INTO PermisosRol VALUES (pIdPermiso, pIdRol);
            
            SET pIndice = pIndice + 1;
        END WHILE;
        
        -- Auditoría posterior
		INSERT INTO aud_PermisosRol
        SELECT 0,NOW(), pUsuarioAud, pIP, pUserAgent, pApp, 'ALTA', 'I',
        PermisosRol.* FROM PermisosRol WHERE IdRol = pIdRol;
        
        UPDATE	Usuarios
        SET		Token = MD5(RAND())
        WHERE	IdRol = pIdRol;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
