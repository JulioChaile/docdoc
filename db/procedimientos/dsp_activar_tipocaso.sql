DROP PROCEDURE IF EXISTS `dsp_activar_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_tipocaso`(pJWT varchar(500), pIdTipoCaso smallint, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
	Permite cambiar el estado de un tipo de caso a Activo, controlando que no se encuentre activo ya. 
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
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso) THEN
		SELECT 'El tipo de caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso) = 'A' THEN
		SELECT 'El tipo de caso ya se encuentra activo.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Auditoría previa
		INSERT INTO aud_TiposCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'A', TiposCaso.* 
        FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso;
        
        UPDATE	TiposCaso
        SET		Estado = 'A'
        WHERE 	IdTipoCaso = pIdTipoCaso;
        
        -- Auditoría posterior
		INSERT INTO aud_TiposCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', TiposCaso.* 
        FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
        
END $$
DELIMITER ;
