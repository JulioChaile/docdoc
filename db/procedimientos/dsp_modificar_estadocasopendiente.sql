DROP PROCEDURE IF EXISTS `dsp_modificar_estadocasopendiente`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_estadocasopendiente`(pJWT varchar(500), pEstadoCasoPendiente varchar(255),
		pIdEstadoCasoPendiente int)
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
    IF pEstadoCasoPendiente IS NULL OR pEstadoCasoPendiente = '' THEN
		SELECT 'Debe indicar el nombre del estado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstadoCasoPendiente IS NULL THEN
		SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM EstadosCasoPendiente WHERE EstadoCasoPendiente = pEstadoCasoPendiente AND IdEstadoCasoPendiente != pIdEstadoCasoPendiente) THEN
		SELECT 'El estado indicada ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM EstadosCasoPendiente WHERE IdEstadoCasoPendiente = pIdEstadoCasoPendiente) THEN
		SELECT 'El estado indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		UPDATE	EstadosCasoPendiente
        SET		EstadoCasoPendiente = pEstadoCasoPendiente
        WHERE	IdEstadoCasoPendiente = pIdEstadoCasoPendiente;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
