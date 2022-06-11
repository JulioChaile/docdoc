DROP PROCEDURE IF EXISTS `dsp_borrar_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_consulta`(pJWT varchar(500), pIdConsulta int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar una consulta controlando que no haya sido derivada ya. 
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
    IF pIdConsulta IS NULL THEN
		SELECT 'Debe indicar una consulta.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdConsulta FROM Consultas WHERE IdConsulta = pIdConsulta) THEN	
		SELECT 'La consulta indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdConsulta FROM Consultas WHERE IdConsulta = pIdConsulta AND Estado = 'D') THEN
		SELECT 'No se puede borrar la consulta debido a que fue derivada.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdConsulta FROM DerivacionesConsultas WHERE IdConsulta = pIdConsulta) THEN
		SELECT 'No se puede borrar la consulta debido a que fue derivada.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        DELETE FROM Consultas WHERE IdConsulta = pIdConsulta;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
