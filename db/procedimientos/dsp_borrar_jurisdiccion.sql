DROP PROCEDURE IF EXISTS `dsp_borrar_jurisdiccion`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_jurisdiccion`(pJWT varchar(500), pIdJurisdiccion int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar una jurisdicción controlando que no tenga juzgados asociados. 
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
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJurisdiccion FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion) THEN
		SELECT 'La jurisdicción indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJurisdiccion = pIdJurisdiccion) THEN
		SELECT 'No se puede borrar la jurisdicción. Existen juzgados asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        INSERT INTO aud_Jurisdicciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', Jurisdicciones.* 
        FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
    
		DELETE FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
