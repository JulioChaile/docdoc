DROP PROCEDURE IF EXISTS `dsp_alta_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_juzgado`(pJWT varchar(500), pIdJurisdiccion int, pJuzgado varchar(50), pModoGestion char(1), pColor varchar(45),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un juzgado controlando que la jursidicción exista y que 
    el nombre no se encuentre en uso ya en la jurisdiccion indicada. 
    Devuelve OK + el id del juzgado creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdJuzgado int;
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
    IF pJuzgado IS NULL OR pJuzgado = '' THEN
		SELECT 'Debe indicar un nombre al juzgado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pModoGestion IS NULL OR pModoGestion = '' THEN
        SELECT 'Debe indicar el modo de gestión.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJurisdiccion FROM Jurisdicciones WHERE IdJurisdiccion = pIdJurisdiccion) THEN
		SELECT 'La jurisdiccion indicada no existe en el sitema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJurisdiccion = pIdJurisdiccion AND Juzgado = pJuzgado) THEN
		SELECT 'Ya existe un juzgado con el nombre indicado en la jurisdicción indicada.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pModoGestion <> 'E' AND pModoGestion <> 'J' THEN
        SELECT 'El modo de gestión indicado no es válido.' Mensaje;
        LEAVE PROC;
    END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		SET pIdJuzgado = (SELECT COALESCE(MAX(IdJuzgado),0) + 1 FROM Juzgados);
        
        INSERT INTO Juzgados VALUES(pIdJuzgado, pIdJurisdiccion, pJuzgado, 'A', pModoGestion, pColor);
        
        INSERT INTO aud_Juzgados
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Juzgados.* 
        FROM Juzgados WHERE IdJuzgado = pIdJuzgado;
        
        SELECT CONCAT('OK', pIdJuzgado) Mensaje;
	COMMIT;
END $$
DELIMITER ;
