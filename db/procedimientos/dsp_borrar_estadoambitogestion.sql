DROP PROCEDURE IF EXISTS `dsp_borrar_estadoambitogestion`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_estadoambitogestion`(pJWT varchar(500), pIdEstadoAmbitoGestion int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un estado de ambito de gestion controlando que no tenga roles asociados. 
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
    IF pIdEstadoAmbitoGestion IS NULL OR pIdEstadoAmbitoGestion = '' THEN
		SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstadoAmbitoGestion FROM EstadoAmbitoGestion WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'El estado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM JuzgadosEstadosAmbitos WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'No se puede borrar el estado. Existen ambitos de gestion asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		DELETE FROM EstadoAmbitoGestion WHERE	IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
