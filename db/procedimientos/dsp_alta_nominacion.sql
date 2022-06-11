DROP PROCEDURE IF EXISTS `dsp_alta_nominacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_nominacion`(pJWT varchar(500), pIdJuzgado int, pNominacion varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear una nominación controlando que exista el juzgado y el nombre no se encuentre en uso ya. 
    Devuelve OK + el id de la nominación creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdNominacion int;
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
    IF pIdJuzgado IS NULL THEN
		SELECT 'Debe indicar un juzgado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNominacion IS NULL OR pNominacion = '' THEN
		SELECT 'Debe indicar una nominación.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdNominacion FROM Nominaciones WHERE IdJuzgado = pIdJuzgado AND Nominacion = pNominacion) THEN
		SELECT 'La nominación indicada ya existe en el juzgado indicado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		SET pIdNominacion = (SELECT COALESCE(MAX(IdNominacion),0) + 1 FROM Nominaciones);
        
        INSERT INTO Nominaciones VALUES(pIdNominacion, pIdJuzgado, pNominacion, 'A');
        
        INSERT INTO aud_Nominaciones
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Nominaciones.* 
        FROM Nominaciones WHERE IdNominacion = pIdNominacion;
        
        SELECT CONCAT('OK',pIdNominacion) Mensaje;
	COMMIT;
END $$
DELIMITER ;
