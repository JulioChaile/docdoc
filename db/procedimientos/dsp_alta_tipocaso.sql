DROP PROCEDURE IF EXISTS `dsp_alta_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_tipocaso`(pJWT varchar(500), pTipoCaso varchar(40),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear tipos de caso controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del tipo de caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdTipoCaso smallint;
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
    IF pTipoCaso IS NULL OR pTipoCaso = '' THEN
		SELECT 'Debe indicar el nombre del tipo de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT IdTipoCaso FROM TiposCaso WHERE TipoCaso = pIdTipoCaso) THEN
		SELECT 'El tipo de caso indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIdTipoCaso = (SELECT COALESCE(MAX(IdTipoCaso),0) + 1 FROM TiposCaso);
        INSERT INTO TiposCaso VALUES(pIdTipoCaso, pTipoCaso, 'A', NULL);
        
        -- Auditoría
		INSERT INTO aud_TiposCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', TiposCaso.* 
        FROM TiposCaso WHERE IdTipoCaso = pIdTipoCaso;
        
        SELECT CONCAT('OK', pIdTipoCaso) Mensaje;
	COMMIT;
END $$
DELIMITER ;
