DROP PROCEDURE IF EXISTS `dsp_borrar_usuario_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_usuario_estudio`(pJWT varchar(500), pIdEstudio int, pIdUsuario int,
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un abogado de un estudio controlando que no tenga casos ni juniors asociados. 
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuario IS NULL THEN
		SELECT 'Debe indicar un usuario del estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
	IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio) THEN
		SELECT 'No puede borrar el usuario ya que no pertenece al estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdCaso FROM UsuariosCaso WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'No se puede borrar el usuario. Existen casos asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuarioPadre = pIdUsuario) THEN
		SELECT 'No se puede borrar el usuario. Existen usuarios de menor jerarquía asociados al mismo.' Mensaje;
        LEAVE PROC;
	END IF;	
    
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Auditoría
		INSERT INTO aud_UsuariosEstudio
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR#USUARIO#ESTUDIO', 'B', UsuariosEstudio.* 
        FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio;
        
		DELETE FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND IdEstudio = pIdEstudio;
        
        -- Auditoría
		INSERT INTO aud_Usuarios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRA', 'B', Usuarios.* 
        FROM Usuarios WHERE IdUsuario = pIdUsuario;
        
        DELETE FROM Usuarios WHERE IdUsuario = pIdUsuario;
		
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
