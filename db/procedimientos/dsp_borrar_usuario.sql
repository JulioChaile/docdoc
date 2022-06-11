DROP PROCEDURE IF EXISTS `dsp_borrar_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_usuario`(pJWT varchar(500), pIdUsuario int, pIP varchar(40),
					pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un usuario controlando que no tenga personas, abogados, juniors ni movimientos 
    de caso asociados.
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			SHOW ERRORS;
			SELECT 'Error en la transacción. Contáctese con el administrado.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS(SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'No se puede borrar el usuario. El mismo pertenece a un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        DELETE FROM Usuarios WHERE IdUsuario = pIdUsuario;
        
			-- Audito
		INSERT INTO aud_Usuarios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', Usuarios.* 
		FROM Usuarios WHERE IdUsuario = pIdUsuario;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
