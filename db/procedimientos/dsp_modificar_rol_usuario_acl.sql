DROP PROCEDURE IF EXISTS `dsp_modificar_rol_usuario_acl`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_rol_usuario_acl`(pJWT varchar(500), pIdACL int, pRol char(1))
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			SHOW ERRORS;
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
    IF pIdACL IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRol IS NULL THEN
		SELECT 'Debe indicar el rol del usuario en el calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT 1 FROM UsuariosACL WHERE IdACL = pIdACL) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRol != 'R' AND pRol != 'W' AND pRol != 'O' THEN
		SELECT 'Indique un rol valido.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        
        UPDATE	UsuariosACL
        SET		Rol = pRol
		WHERE	IdACL = pIdACL;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
