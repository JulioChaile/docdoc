DROP PROCEDURE IF EXISTS `dsp_activar_jurisdiccion`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_jurisdiccion`(pJWT varchar(500), pIdJurisdiccion int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el Estado de una jurisdiccion a Activo. 
    Devuelve OK o un Mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores en la transacción
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
    IF pIdJurisdiccion IS NULL THEN
		SELECT 'Debe indicar una jurisdiccion.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJurisdiccion FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion) THEN
		SELECT 'La jurisdiccion indicada no existe en el sitema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion) = 'A' THEN
		SELECT 'OK' Mensaje;
		LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		-- Antes
		INSERT INTO aud_Jurisdicciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'A', Jurisdicciones.* 
        FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
		
		UPDATE	Jurisdicciones
        SET		Estado = 'A'
        WHERE	IdJurisdiccion = pIdJurisdiccion;
        
        INSERT INTO aud_Jurisdicciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', Jurisdicciones.* 
        FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
