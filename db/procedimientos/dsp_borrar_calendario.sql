DROP PROCEDURE IF EXISTS `dsp_borrar_calendario`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_calendario`(pJWT varchar(500), pIdCalendario int)
PROC: BEGIN
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
    IF pIdCalendario IS NULL THEN
		SELECT 'Debe indicar un calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM CalendariosEstudio WHERE IdCalendario = pIdCalendario) THEN
		SELECT 'El calendario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		DELETE FROM CalendariosEstudio WHERE IdCalendario = pIdCalendario;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
