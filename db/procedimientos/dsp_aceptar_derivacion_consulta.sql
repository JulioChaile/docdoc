DROP PROCEDURE IF EXISTS `dsp_aceptar_derivacion_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_aceptar_derivacion_consulta`(pJWT varchar(500), pIdDerivacionConsulta int, 
		pIdEstudio int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite aceptar una consulta derivada a un estudio siempre y cuando la última derivación de la consulta sea para el estudio 
    que intenta aceptarla, la derivación se encuentre en estado Pendiente y la consulta se encuentre en estado Derivada.
    Devuelve OK o el mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudioUlt, pIdConsulta, pIdDerivacionUlt int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			show errors;
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
		SELECT 'La derivación debe estar Pendiente para poder ser aceptada por el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
		INSERT INTO aud_DerivacionesConsultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACEPTAR#DERIVACION', 'A', DerivacionesConsultas.* 
        FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta;
        
        UPDATE	DerivacionesConsultas
		SET		Estado = 'A'
        WHERE	IdDerivacionConsulta = pIdDerivacionConsulta;
        
        -- Después
		INSERT INTO aud_DerivacionesConsultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACEPTAR#DERIVACION', 'D', DerivacionesConsultas.* 
        FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
