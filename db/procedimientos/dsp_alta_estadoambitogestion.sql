DROP PROCEDURE IF EXISTS `dsp_alta_estadoambitogestion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_estadoambitogestion`(pJWT varchar(500), pEstadoAmbitoGestion varchar(255),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un estado de ambito de gestion controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del estado de ambito de gestion creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdEstadoAmbitoGestion int;
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
    IF pEstadoAmbitoGestion IS NULL OR pEstadoAmbitoGestion = '' THEN
		SELECT 'Debe indicar el nombre del Estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT IdEstadoAmbitoGestion FROM EstadoAmbitoGestion WHERE EstadoAmbitoGestion = pEstadoAmbitoGestion) THEN
		SELECT 'El Estado indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        
        INSERT INTO EstadoAmbitoGestion SELECT 0, pEstadoAmbitoGestion, NULL;
        SET pIdEstadoAmbitoGestion = LAST_INSERT_ID();
        
        SELECT CONCAT('OK', pIdEstadoAmbitoGestion) Mensaje;
	COMMIT;
END $$
DELIMITER ;
