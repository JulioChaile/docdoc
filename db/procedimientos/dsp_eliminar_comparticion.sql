DROP PROCEDURE IF EXISTS `dsp_eliminar_comparticion`;
DELIMITER $$
CREATE PROCEDURE `dsp_eliminar_comparticion`(pJWT varchar(500), pIdCaso int, pIdEstudioDestino int)
PROC: BEGIN

    DECLARE pIdUsuarioGestion, pIdEstudio int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- SHOW ERRORS;
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
    IF pIdCaso = 0 OR pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdEstudioDestino = 0 OR pIdEstudioDestino IS NULL THEN
		SELECT 'Debe indicar un Estudio.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A') THEN
		SELECT 'Usted no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF NOT EXISTS (SELECT 1 FROM Comparticiones WHERE IdCaso = pIdCaso AND IdEstudioOrigen = pIdEstudio) THEN
        SELECT 'Usted no pertenece al estudio desde el cual se compartio el caso.' Mensaje;
        LEAVE PROC;
    END IF;
    START TRANSACTION;
        DELETE FROM Comparticiones
        WHERE       IdCaso = pIdCaso AND
                    IdEstudioOrigen = pIdEstudio AND
                    IdEstudioDestino = pIdEstudioDestino;

        DELETE FROM UsuariosCaso
        WHERE       IdCaso = pIdCaso AND
                    IdEstudio = pIdEstudioDestino;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
