DROP PROCEDURE IF EXISTS `dsp_activar_nominacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_nominacion`(pJWT varchar(500), pIdNominacion int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado de una nominación a Activo. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
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
	IF pIdNominacion IS NULL THEN
		SELECT 'Debe indicar una nominación.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdNominacion FROM Nominaciones WHERE IdNominacion = pIdNominacion) THEN
		SELECT 'La nominación indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Nominaciones WHERE IdNominacion = pIdNominacion) = 'A' THEN
		SELECT 'OK' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		INSERT INTO aud_Nominaciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'A', Nominaciones.* 
        FROM Nominaciones WHERE IdNominacion = pIdNominacion;
    
		UPDATE	Nominaciones
        SET		Estado = 'A'
        WHERE	IdNominacion = pIdNominacion;
        
        INSERT INTO aud_Nominaciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', Nominaciones.* 
        FROM Nominaciones WHERE IdNominacion = pIdNominacion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
