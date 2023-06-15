DROP PROCEDURE IF EXISTS `dsp_modificar_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_juzgado`(pJWT varchar(500), pIdJuzgado int, pJuzgado varchar(50), pModoGestion char(1), pColor varchar(45),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un juzgado controlando que la jursidicción exista y que 
    el nombre no se encuentre en uso en la jurisdicción. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdJurisdiccion int;
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
    IF pJuzgado IS NULL OR pJuzgado = '' THEN
		SELECT 'Debe indicar un nombre al juzgado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pModoGestion IS NULL OR pModoGestion = '' THEN
        SELECT 'Debe indicar el modo de gestión.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdJurisdiccion = (SELECT IdJurisdiccion FROM Juzgados WHERE IdJuzgado = pIdJuzgado);
    IF EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJurisdiccion = pIdJurisdiccion AND Juzgado = pJuzgado
    AND IdJuzgado != pIdJuzgado) THEN
		SELECT 'Ya existe un juzgado con el nombre indicado en la jurisdicción indicada.' Mensaje;
        LEAVE PROC;
	END IF;

    IF pModoGestion <> 'E' AND pModoGestion <> 'J' THEN
        SELECT 'El modo de gestión indicado no es válido.' Mensaje;
        LEAVE PROC;
    END IF;

    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        INSERT INTO aud_Juzgados
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'A', Juzgados.* 
        FROM Juzgados WHERE IdJuzgado = pIdJuzgado;
		
        UPDATE	Juzgados
        SET		Juzgado = pJuzgado, ModoGestion = pModoGestion, Color = pColor
		WHERE	IdJuzgado = pIdJuzgado;
        
        INSERT INTO aud_Juzgados
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICAR', 'D', Juzgados.* 
        FROM Juzgados WHERE IdJuzgado = pIdJuzgado;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
