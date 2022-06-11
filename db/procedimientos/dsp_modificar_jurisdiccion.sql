DROP PROCEDURE IF EXISTS `dsp_modificar_jurisdiccion`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_jurisdiccion`(pJWT varchar(500), pIdJurisdiccion int, pJurisdiccion varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar una jurisdicción controlando que el nombre no se encuentre en uso ya. 
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
    IF pIdJurisdiccion IS NULL THEN
		SELECT 'Debe indicar una jurisdicción.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pJurisdiccion IS NULL OR pJurisdiccion = '' THEN
		SELECT 'Debe indicar el nombre de la jurisdicción.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJurisdiccion FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion) THEN
		SELECT 'La jurisdicción indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdJurisdiccion FROM Jurisdicciones WHERE Jurisdiccion = pJurisdiccion 
    AND IdJurisdiccion != pIdJurisdiccion) THEN
		SELECT 'El nombre de la jurisdicción indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        INSERT INTO aud_Jurisdicciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Jurisdicciones.* 
        FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
        
        UPDATE	Jurisdicciones
        SET		Jurisdiccion = pJurisdiccion
        WHERE	IdJurisdiccion = pIdJurisdiccion;
        
        INSERT INTO aud_Jurisdicciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Jurisdicciones.* 
        FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
