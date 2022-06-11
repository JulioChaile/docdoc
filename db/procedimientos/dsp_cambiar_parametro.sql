DROP PROCEDURE IF EXISTS `dsp_cambiar_parametro`;
DELIMITER $$
CREATE PROCEDURE `dsp_cambiar_parametro`(pJWT varchar(500), pIdEstudio int,
				pParametro varchar(20), pValor varchar(50), 
				pIP varchar(40), pUserAgent varchar(255), pAplicacion varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el valor de un parámetro siempre y cuando sea editable.
    Devuelve OK o el mensaje de error en Mensaje.
    */
    DECLARE pIdUsuario int;
    DECLARE pUsuario varchar(120);
	-- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
        ROLLBACK;
	END;
    -- Controla Parámetros
    SET pIdUsuario = f_valida_sesion_usuario(pJWT);
    IF pIdUsuario = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (pValor IS NULL OR pValor = '') THEN
        SELECT 'Debe ingresar un valor para el parámetro.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS(SELECT Parametro FROM Empresa WHERE Parametro = pParametro AND IdEstudio = pIdEstudio) THEN
        SELECT 'El estudio no tiene el parámetro indicado.' Mensaje;
        LEAVE PROC;
	END IF;
	IF NOT EXISTS(SELECT Parametro FROM Empresa WHERE Parametro = pParametro AND IdEstudio = pIdEstudio AND EsEditable = 'S') THEN
        SELECT 'El parámetro no es editable.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);
        -- Aud Antes
        INSERT INTO aud_Empresa
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pAplicacion, 'CAMBIAR#PARAMETRO', 'A', Empresa.* 
        FROM Empresa WHERE Parametro = pParametro AND IdEstudio = pIdEstudio;
        -- Modifico parámetro
        UPDATE Empresa SET Valor = pValor WHERE Parametro = pParametro AND IdEstudio = pIdEstudio;
        -- Aud Después
        INSERT INTO aud_Empresa
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pAplicacion, 'CAMBIAR#PARAMETRO', 'D', Empresa.* 
        FROM Empresa WHERE Parametro = pParametro AND IdEstudio = pIdEstudio;
		SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
