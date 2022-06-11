DROP PROCEDURE IF EXISTS `dsp_darbaja_estadocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_darbaja_estadocaso`(pJWT varchar(500), pIdEstadoCaso int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado de un EstadoCaso a Activo. 
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
    IF pIdEstadoCaso IS NULL THEN
		SELECT 'Debe indicar un estado de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstadoCaso FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso) THEN
		SELECT 'El estado de caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso) = 'B' THEN
		SELECT 'OK' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
        INSERT INTO aud_EstadosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DARBAJA', 'A', EstadosCaso.* 
        FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso;
    
		UPDATE	EstadosCaso
        SET		Estado = 'B'
        WHERE	IdEstadoCaso = pIdEstadoCaso;
        
        -- Después
        INSERT INTO aud_EstadosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DARBAJA', 'D', EstadosCaso.* 
        FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
