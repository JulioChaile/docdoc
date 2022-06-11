DROP PROCEDURE IF EXISTS `dsp_alta_usuario_token_app`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario_token_app`(pJWT varchar(500), pIdUsuario int, pIdEstudio int, pTokenApp varchar(200))
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario del estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTokenApp IS NULL THEN
		SELECT 'Debe indicar el token.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El Estudio indicado no existe en el sitema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El abogado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio) THEN
		SELECT 'El usuario no pertenece al estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		IF EXISTS (SELECT TokenApp FROM UsuariosTokenApp WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio) THEN
            UPDATE  UsuariosTokenApp
            SET     TokenApp = pTokenApp
            WHERE   IdUsuario = pIdUsuario;
        ELSE
            INSERT INTO UsuariosTokenApp SELECT pIdUsuario, pIdEstudio, pTokenApp;
        END IF;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
