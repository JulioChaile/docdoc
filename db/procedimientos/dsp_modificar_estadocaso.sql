DROP PROCEDURE IF EXISTS `dsp_modificar_estadocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_estadocaso`(pJWT varchar(500), pIdEstadoCaso int, pEstadoCaso varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un EstadoCaso, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
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
    IF pEstadoCaso IS NULL OR pEstadoCaso = '' THEN
		SELECT 'Debe indicar un nobre al estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstadoCaso FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso) THEN
		SELECT 'El estado de caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdEstudio = (SELECT IdEstudio FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso);
    IF EXISTS (SELECT IdEstadoCaso FROM EstadosCaso WHERE EstadoCaso = pEstadoCaso AND IdEstudio = pIdEstudio) THEN
		SELECT 'El nombre indicado para el estado del caso ya se encuentra en uso en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
        INSERT INTO aud_EstadosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', EstadosCaso.* 
        FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso;
		
		UPDATE	EstadosCaso
        SET		EstadoCaso = UPPER(pEstadoCaso)
        WHERE	IdEstadoCaso = pIdEstadoCaso;
        
		-- Después
        INSERT INTO aud_EstadosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', EstadosCaso.* 
        FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
