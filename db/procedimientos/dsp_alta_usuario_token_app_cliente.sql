DROP PROCEDURE IF EXISTS `dsp_alta_usuario_token_app_cliente`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario_token_app_cliente`(pJWT varchar(500), pIdUsuario int, pUsuario varchar(200), pTokenApp varchar(200))
PROC: BEGIN
	/*
    Devuelve OK o un mensaje de error en Mensaje.
    */
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
    IF pIdUsuario IS NULL OR pUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTokenApp IS NULL THEN
		SELECT 'Debe indicar el token.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario AND Usuario = pUsuario) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		IF EXISTS (SELECT TokenApp FROM UsuariosTokenAppCliente WHERE IdUsuario = pIdUsuario) THEN
            UPDATE  UsuariosTokenAppCliente
            SET     TokenApp = pTokenApp
            WHERE   IdUsuario = pIdUsuario;
        ELSE
            INSERT INTO UsuariosTokenAppCliente SELECT pIdUsuario, pUsuario, pTokenApp;
        END IF;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
