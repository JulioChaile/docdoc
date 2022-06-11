DROP PROCEDURE IF EXISTS `dsp_borrar_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_mediador`(pJWT varchar(500), pIdMediador int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un mediador controlando que no tenga roles asociados. 
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
    IF pIdMediador IS NULL OR pIdMediador = '' THEN
		SELECT 'Debe indicar un mediador.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdMediador FROM Mediadors WHERE IdMediador = pIdMediador) THEN
		SELECT 'El mediador indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM MediadorCaso WHERE IdMediador = pIdMediador) THEN
		SELECT 'No se puede borrar el mediador. Existen casos asciados.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		DELETE FROM Mediadores WHERE	IdMediador = pIdMediador;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
