DROP PROCEDURE IF EXISTS `dsp_modificar_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_tipocaso`(pJWT varchar(500), pIdTipoCaso smallint, 
		pTipoCaso varchar(40), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un tipo de caso controlando que el nombre no se encuentre en uso ya. 
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
    IF pIdTipoCaso IS NULL OR pIdTipoCaso = '' THEN
		SELECT 'Debe indicar un tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipoCaso IS NULL OR pTipoCaso = '' THEN
		SELECT 'Debe indicar el nombre del tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso) THEN
		SELECT 'El tipo de caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE TipoCaso = pIdTipoCaso AND IdTipoCaso != pIdTipoCaso) THEN
		SELECT 'El tipo de caso indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Auditoría previa
		INSERT INTO aud_TiposCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', TiposCaso.* 
        FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso;
        
		UPDATE	TiposCaso
        SET		TipoCaso = pTipoCaso
        WHERE	IdTipoCaso = pIdTipoCaso;
        
        -- Auditoría posterior
		INSERT INTO aud_TiposCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', TiposCaso.* 
        FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
