DROP PROCEDURE IF EXISTS `dsp_borrar_rol_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_rol_estudio`(pJWT varchar(500), pIdRolEstudio int,
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un rol de un estudio siempre que no existan usuarios con ese rol asignado.
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
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
    IF pIdRolEstudio IS NULL THEN
		SELECT 'Debe indicar un rol de estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdRolEstudio FROM RolesEstudio WHERE IdRolEstudio = pIdRolEstudio) THEN
		SELECT 'El rol de estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdRolEstudio = pIdRolEstudio) THEN
		SELECT 'No se puede borrar el rol. Existen usuarios con el rol asignado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        INSERT INTO aud_RolesEstudio 
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', RolesEstudio.* 
        FROM RolesEstudio WHERE IdRolEstudio = pIdRolEstudio;
        
        DELETE FROM RolesEstudio WHERE IdRolEstudio = pIdRolEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
