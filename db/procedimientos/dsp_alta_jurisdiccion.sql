DROP PROCEDURE IF EXISTS `dsp_alta_jurisdiccion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_jurisdiccion`(pJWT varchar(500), pJurisdiccion varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permtie crear una jurisdicción controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + el id de la jurisdicción creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdJurisdiccion int;
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
    IF pJurisdiccion IS NULL OR pJurisdiccion = '' THEN
		SELECT 'Debe indicar el nombre de la jurisdicción.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT IdJurisdiccion FROM Jurisdicciones WHERE Jurisdiccion = pJurisdiccion) THEN
		SELECT 'El nombre de la jurisdicción indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		SET pIdJurisdiccion = (SELECT COALESCE(MAX(IdJurisdiccion),0) + 1 FROM Jurisdicciones);
        
        INSERT INTO Jurisdicciones VALUES(pIdJurisdiccion, pJurisdiccion, 'A');
        
        INSERT INTO aud_Jurisdicciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Jurisdicciones.* 
        FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
        
        SELECT CONCAT('OK', pIdJurisdiccion) Mensaje;
	COMMIT;
END $$
DELIMITER ;
