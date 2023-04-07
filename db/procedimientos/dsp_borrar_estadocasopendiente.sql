DROP PROCEDURE IF EXISTS `dsp_borrar_estadocasopendiente`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_estadocasopendiente`(pJWT varchar(500), pIdEstadoCasoPendiente int, 
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
    IF pIdEstadoCasoPendiente IS NULL OR pIdEstadoCasoPendiente = '' THEN
		SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM EstadosCasoPendiente WHERE IdEstadoCasoPendiente = pIdEstadoCasoPendiente) THEN
		SELECT 'El estado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
		DELETE FROM EstadosCasoPendiente WHERE	IdEstadoCasoPendiente = pIdEstadoCasoPendiente;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
