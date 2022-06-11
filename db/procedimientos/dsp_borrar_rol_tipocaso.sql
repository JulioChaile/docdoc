DROP PROCEDURE IF EXISTS `dsp_borrar_rol_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_rol_tipocaso`(pJWT varchar(500), pIdRTC smallint, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un rol de tipo de caso controlando que no existan actores con ese rol. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
	DECLARE pUsuario varchar(120);
    -- Manejo de error en la transacción    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
        ROLLBACK;
	END;
    -- Controla Parámetros.
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdRTC IS NULL THEN
		SELECT 'Debe indicar un tipo caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdRTC FROM RolesTipoCaso WHERE IdRTC = pIdRTC) THEN
		SELECT 'El rol de tipo de caso no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdCaso FROM PersonasCaso WHERE IdRTC = pIdRTC) THEN
		SELECT 'No se puede borrar el rol del tipo de caso. Existen acotres con ese rol.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Auditoría previa
		INSERT INTO aud_RolesTipoCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', RolesTipoCaso.* 
        FROM RolesTipoCaso WHERE IdRTC = pIdRTC;
        
        DELETE FROM RolesTipoCaso WHERE	IdRTC = pIdRTC;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
