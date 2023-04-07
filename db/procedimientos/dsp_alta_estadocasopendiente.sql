DROP PROCEDURE IF EXISTS `dsp_alta_estadocasopendiente`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_estadocasopendiente`(pJWT varchar(500), pEstadoCasoPendiente varchar(255),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un estado de ambito de gestion controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del estado de ambito de gestion creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdEstadoCasoPendiente int;
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
		SELECT 'Debe indicar el nombre del Estado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM EstadosCasoPendiente WHERE EstadoCasoPendiente = pEstadoCasoPendiente) THEN
		SELECT 'El Estado indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        
        INSERT INTO EstadosCasoPendiente SELECT 0, pEstadoCasoPendiente;
        SET pIdEstadoCasoPendiente = LAST_INSERT_ID();
        
        SELECT CONCAT('OK', pIdEstadoCasoPendiente) Mensaje;
	COMMIT;
END $$
DELIMITER ;
