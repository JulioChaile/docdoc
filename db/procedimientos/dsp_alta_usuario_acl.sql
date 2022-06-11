DROP PROCEDURE IF EXISTS `dsp_alta_usuario_acl`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario_acl`(pJWT varchar(500), pIdACLAPI varchar(300), pIdCalendario int, pIdUsuario int, pRol char(1))
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
    -- Manejo de errores
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
    IF pIdACLAPI IS NULL THEN
		SELECT 'Se debe indicar el id del permiso creado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCalendario IS NULL THEN
		SELECT 'Debe indicar el calendario al cual pertenece el usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe ponerle un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRol IS NULL THEN
		SELECT 'Debe indicar el rol del usuario en el calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM CalendariosEstudio WHERE IdCalendario = pIdCalendario) THEN
		SELECT 'El calendario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRol != 'R' AND pRol != 'W' AND pRol != 'O' THEN
		SELECT 'Indique un rol valido.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		INSERT INTO UsuariosACL VALUES (0, pIdACLAPI, pIdCalendario, pIdUsuario, pRol);
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
