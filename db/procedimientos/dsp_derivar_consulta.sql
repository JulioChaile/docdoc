DROP PROCEDURE IF EXISTS `dsp_derivar_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_derivar_consulta`(pJWT varchar(500), pIdConsulta int, pIdEstudio int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite derivar una consulta cambiando su estado a Derivada e indicando el estudio al cual se deriva, 
    controlando que la consulta se encuentre Activa y no haya sido derivada ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdDerivacionConsulta int;
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
    IF pIdConsulta IS NULL THEN
		SELECT 'Debe indicar una consulta.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar el estudio al cual se deriva la consulta.' Mensajel;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdConsulta FROM Consultas WHERE IdConsulta = pIdConsulta) THEN
		SELECT 'La consulta indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Consultas WHERE IdConsulta = pIdConsulta) != 'A' THEN
		SELECT 'Solo pueden derivarse las consultas en estado Activa.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio AND Estado = 'B') THEN
		SELECT 'El estudio indicado está dado de baja. No se puede derivar la consulta.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
		SET pIdDerivacionConsulta = (SELECT COALESCE(MAX(IdDerivacionConsulta),0) + 1 FROM DerivacionesConsultas);
        INSERT INTO DerivacionesConsultas VALUES(pIdDerivacionConsulta, pIdEstudio, pIdConsulta, NOW(), 'P');
        
        -- Auditoría
		INSERT INTO aud_DerivacionesConsultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DERIVAR', 'I', DerivacionesConsultas.* 
        FROM DerivacionesConsultas WHERE IdDerivacionConsulta = pIdDerivacionConsulta;
        
        -- Antes
		INSERT INTO aud_Consultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DERIVAR', 'A', Consultas.* 
        FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        UPDATE	Consultas
        SET		Estado = 'D'
        WHERE	IdConsulta = pIdConsulta;
        
        -- Después
		INSERT INTO aud_Consultas
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'DERIVAR', 'D', Consultas.* 
        FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
