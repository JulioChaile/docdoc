DROP PROCEDURE IF EXISTS `dsp_alta_estadocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_estadocaso`(pJWT varchar(500), pIdEstudio int, pEstadoCaso varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un EstadoCaso, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstadoCaso int;
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEstadoCaso IS NULL OR pEstadoCaso = '' THEN
		SELECT 'Debe indicar un nobre al estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstadoCaso FROM EstadosCaso WHERE EstadoCaso = pEstadoCaso AND IdEstudio = pIdEstudio) THEN
		SELECT 'El nombre indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		SET pIdEstadoCaso = (SELECT COALESCE(MAX(IdEstadoCaso),0) + 1 FROM EstadosCaso);
		INSERT INTO EstadosCaso VALUES (pIdEstadoCaso, pIdEstudio, UPPER(pEstadoCaso), 'A');
        
		INSERT INTO aud_EstadosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', EstadosCaso.* 
        FROM EstadosCaso WHERE IdEstadoCaso = pIdEstadoCaso;
        
        SELECT CONCAT('OK', pIdEstadoCaso) Mensaje;
	COMMIT;
END $$
DELIMITER ;
