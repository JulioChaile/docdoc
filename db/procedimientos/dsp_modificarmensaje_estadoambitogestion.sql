DROP PROCEDURE IF EXISTS `dsp_modificarmensaje_estadoambitogestion`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificarmensaje_estadoambitogestion`(pJWT varchar(500), pMensaje text,
		pIdEstadoAmbitoGestion int)
PROC: BEGIN
	/*
    Permite modificar un estado de ambito de gestion controlando que el nombre no se encuentre en uso ya. 
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
    IF pIdEstadoAmbitoGestion IS NULL THEN
		SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstadoAmbitoGestion FROM EstadoAmbitoGestion WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'El estado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		UPDATE	EstadoAmbitoGestion
        SET		Mensaje = pMensaje
        WHERE	IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
