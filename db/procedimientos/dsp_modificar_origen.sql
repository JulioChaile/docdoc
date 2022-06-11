DROP PROCEDURE IF EXISTS `dsp_modificar_origen`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_origen`(pJWT varchar(500), pIdOrigen int, pOrigen varchar(150), 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un origen de casos controlando que el usuario que ejecuta la acción pertence al estudio
    al cual pertenece el origen indicado.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			SHOW ERRORS;
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
    IF pIdOrigen IS NULL THEN
		SELECT 'Debe indicar un origen de casos.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pOrigen IS NULL OR TRIM(pOrigen) = '' THEN
		SELECT 'Debe indicar el nombre del origen.' Mensaje;
		LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT IdOrigen FROM Origenes WHERE IdOrigen = pIdOrigen) THEN
		SELECT 'El origen indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    -- SET pIdEstudio = (SELECT IdEstudio FROM Origenes WHERE IdOrigen = pIdOrigen);
    
    IF EXISTS (SELECT IdOrigen FROM Origenes WHERE Origen = TRIM(pOrigen) AND IdOrigen != pIdOrigen) THEN
		SELECT 'Ya existe un origen con el nombre indicado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        
        UPDATE	Origenes
        SET		Origen = TRIM(pOrigen)
		WHERE	IdOrigen = pIdOrigen;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
