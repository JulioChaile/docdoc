DROP PROCEDURE IF EXISTS `dsp_borrar_origen`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_origen`(pJWT varchar(500), pIdOrigen int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un origen controlando que no existan casos asociados. 
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
    IF pIdOrigen IS NULL THEN
		SELECT 'Debe indicar un origen de casos.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT IdOrigen FROM Origenes WHERE IdOrigen = pIdOrigen) THEN
		SELECT 'El origen indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdCaso FROM Casos WHERE IdOrigen = pIdOrigen) THEN
		SELECT 'No se puede borrar el origen. Existen casos asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    -- SET pIdEstudio = (SELECT IdEstudio FROM Origenes WHERE IdOrigen = pIdOrigen);
    
    /*IF NOT EXISTS (SELECT IdUsuario FROM AbogadosEstudio WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioGestion) THEN
		SELECT 'No se puede modificar el origen. Usted no pertenece al estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    */
    START TRANSACTION;			
        DELETE FROM Origenes WHERE IdOrigen = pIdOrigen;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
