DROP PROCEDURE IF EXISTS `dsp_activar_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_consulta`(pJWT varchar(500), pIdConsulta int, 
	pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el estado de una consulta a Activa, siempre que no esté activa ya. 
    Devuelve OK o un mensaje de error en Mensaje
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
    IF pIdConsulta IS NULL THEN
		SELECT 'Debe indicar una consulta.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdConsulta FROM Consultas WHERE IdConsulta = pIdConsulta) THEN
		SELECT 'La consulta indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Consultas WHERE IdConsulta = pIdConsulta) = 'A' THEN
		SELECT 'La consulta indicada ya se encuentra activa.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Consultas WHERE IdConsulta = pIdConsulta) = 'D' THEN
		SELECT 'La consulta indicada ya se encuentra derivada. No es posible modificar su estado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
		INSERT INTO aud_Consultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'A', Consultas.* 
        FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        UPDATE	Consultas
        SET		Estado = 'A'
        WHERE	IdConsulta = pIdConsulta;
        
        -- Antes
		INSERT INTO aud_Consultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', Consultas.* 
        FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
