DROP PROCEDURE IF EXISTS `dsp_darbaja_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_darbaja_usuario`(pJWT varchar(500), pIdUsuario int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado de un usuario a Baja, siempre y cuando no se aun usuario activo de un estudio. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuarioAud varchar(120);
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
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario indicada no existe en el sitema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND Estado = 'A') THEN
		SELECT 'No se puede dar de baja el usuario debido a que esta activo en un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Usuarios WHERE IdUsuario = pIdUsuario) = 'B' THEN
		SELECT 'OK' Mensaje;
		LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuarioAud = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		-- Antes
		INSERT INTO aud_Usuarios
		SELECT 0, NOW(), pUsuarioAud, pIP, pUserAgent, pApp, 'DARBAJA', 'A', Usuarios.* 
        FROM Usuarios WHERE IdUsuario = pIdUsuario;
        
		UPDATE	Usuarios
        SET		Estado = 'B'
        WHERE	IdUsuario = pIdUsuario;
        
        -- Después
		INSERT INTO aud_Usuarios
		SELECT 0, NOW(), pUsuarioAud, pIP, pUserAgent, pApp, 'DARBAJA', 'D', Usuarios.* 
        FROM Usuarios WHERE IdUsuario = pIdUsuario;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
