DROP PROCEDURE IF EXISTS `dsp_darbaja_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_darbaja_juzgado`(pJWT varchar(500), pIdJuzgado int,
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado de un juzgado a Baja. 
    Devuelve OK o un mensaje de error en Mensaje.
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
    IF pIdJuzgado IS NULL THEN
		SELECT 'Debe indicar un juzgado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) = 'B' THEN
		SELECT 'OK' Mensaje;
		LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		
        INSERT INTO aud_Juzgados
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Juzgados.* 
        FROM Juzgados WHERE IdJuzgado = pIdJuzgado;
        
		UPDATE	Juzgados
        SET		Estado = 'B'
        WHERE	IdJuzgado = pIdJuzgado;
        
        INSERT INTO aud_Juzgados
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Juzgados.* 
        FROM Juzgados WHERE IdJuzgado = pIdJuzgado;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
