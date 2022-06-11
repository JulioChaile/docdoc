DROP PROCEDURE IF EXISTS `dsp_rechazar_derivacion_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_rechazar_derivacion_consulta`(pJWT varchar(500), pIdDerivacionConsulta int, pIdEstudio int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite rechazar una derivación de consulta a un estudio siempre que sea la última derivación de la consulta,
    que la derivación se encuentre en estado Pendiente y que la consulta se encuentre en estado Activa. Controla que el estudio indicado
    tenga la última derivación de la consulta y coincida con el estudio al que pertenece el usuario que ejecuta el procedimiento.
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudioUlt, pIdConsulta, pIdDerivacionUlt int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			SHOW ERRORS;
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
    IF pIdDerivacionConsulta IS NULL THEN
		SELECT 'Debe indicar una consulta derivada.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar el estudio al cual se deriva la consulta.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdUsuario FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion AND Estado = 'A') THEN
		SELECT 'Usted no pertenece al estudio o se encuentra dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdDerivacionConsulta FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta) THEN
		SELECT 'No existe la consulta derivada.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdConsulta = (SELECT IdConsulta FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta);
    SELECT 	IdDerivacionConsulta, IdEstudio
	INTO	pIdDerivacionUlt, pIdEstudioUlt
	FROM 	DerivacionesConsultas 
	WHERE 	IdConsulta = pIdConsulta AND 
			IdDerivacionConsulta = (SELECT 	MAX(IdDerivacionConsulta) 
									FROM 	DerivacionesConsultas 
									WHERE	IdConsulta = pIdConsulta);
	IF pIdDerivacionUlt != pIdDerivacionConsulta THEN
		SELECT 'No se puede aceptar la consulta. La derivación no es la última.' Mensaje;
        LEAVE PROC;
	END IF;
	IF pIdEstudioUlt != pIdEstudio THEN
		SELECT 'La consulta indicada no fue derivada al estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    
    IF EXISTS (SELECT Estado FROM Consultas WHERE IdConsulta = pIdConsulta AND Estado = 'B') THEN
		SELECT 'La consulta indicada está dada de baja.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT Estado FROM Consultas WHERE IdConsulta = pIdConsulta AND Estado = 'D') THEN
		SELECT 'La consulta indicada no está en estado Derivada.' Mensaje;
        LEAVE PROC;
	END IF;
	IF NOT EXISTS (SELECT IdDerivacionConsulta FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta AND Estado = 'P') THEN
		SELECT 'La derivación debe estar Pendiente para poder ser rechazada por el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
		INSERT INTO aud_DerivacionesConsultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'RECHAZAR#DERIVACION', 'A', DerivacionesConsultas.* 
        FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta;
        
        UPDATE	DerivacionesConsultas
		SET		Estado = 'R'
        WHERE	IdDerivacionConsulta = pIdDerivacionConsulta;
        
        -- Después
		INSERT INTO aud_DerivacionesConsultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'RECHAZAR#DERIVACION', 'D', DerivacionesConsultas.* 
        FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta;
        
        -- Antes
		INSERT INTO aud_Consultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'RECHAZAR#DERIVACION', 'A', Consultas.* 
        FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        UPDATE	Consultas
        SET		Estado = 'A'
        WHERE	IdConsulta = pIdConsulta;
        
        -- Antes
		INSERT INTO aud_Consultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'RECHAZAR#DERIVACION', 'D', Consultas.* 
        FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
