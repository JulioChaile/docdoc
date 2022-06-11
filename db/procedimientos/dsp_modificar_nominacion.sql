DROP PROCEDURE IF EXISTS `dsp_modificar_nominacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_nominacion`(pJWT varchar(500), pIdNominacion int, pNominacion varchar(50), 
				pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar una nominación controlando que exista el juzgado y el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdJuzgado int;
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
	IF pIdNominacion IS NULL THEN
		SELECT 'Debe indicar una nominación.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNominacion IS NULL OR pNominacion = '' THEN
		SELECT 'Debe indicar una nominación.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdNominacion FROM Nominaciones WHERE IdNominacion = pIdNominacion) THEN
		SELECT 'La nominación indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdJuzgado = (SELECT IdJuzgado FROM Nominaciones WHERE IdNominacion = pIdNominacion);
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdNominacion FROM Nominaciones WHERE IdJuzgado = pIdJuzgado AND Nominacion = pNominacion
    AND IdNominacion != pIdNominacion) THEN
		SELECT 'Ya existe una nominación con el mismo nombre en el juzgado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		INSERT INTO aud_Nominaciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Nominaciones.* 
        FROM Nominaciones WHERE IdNominacion = pIdNominacion;
		
        UPDATE	Nominaciones
        SET		Nominacion = pNominacion
		WHERE	IdNominacion = pIdNominacion;
        
        INSERT INTO aud_Nominaciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Nominaciones.* 
        FROM Nominaciones WHERE IdNominacion = pIdNominacion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
