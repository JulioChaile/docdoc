DROP PROCEDURE IF EXISTS `dsp_borrar_difusion`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_difusion`(pJWT varchar(500), pIdDifusion smallint, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar una campaña de difusión controlando que no existan consultas asociadas. 
    Devuelve OK o un mesnaje de error en Mensaje.
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
	IF pIdDifusion IS NULL THEN
		SELECT 'Debe indicar una campaña de difusión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdDifusion FROM Difusiones WHERE IdDifusion = pIdDifusion) THEN
		SELECT 'La campaña indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdConsulta FROM Consultas WHERE IdDifusion = pIdDifusion) THEN
		SELECT 'No se puede borrar la campaña. Existen consultas asociadas.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        DELETE FROM	Difusiones WHERE IdDifusion = pIdDifusion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
