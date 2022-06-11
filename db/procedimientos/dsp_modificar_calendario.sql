DROP PROCEDURE IF EXISTS `dsp_modificar_calendario`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_calendario`(pJWT varchar(500), pIdCalendario int, pTitulo varchar(100), pDescripcion text, pIdColor varchar(2))
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
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
    IF pTitulo IS NULL THEN
		SELECT 'Debe indicar un titulo para el calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdColor IS NULL THEN
		SELECT 'Debe indicar un color para el calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT 1 FROM CalendariosEstudio WHERE IdCalendario = pIdCalendario) THEN
		SELECT 'El calendario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM CalendariosEstudio WHERE Titulo = pTitulo) THEN
		SELECT 'Ya existe un calendario con el titulo indicado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        
        UPDATE	CalendariosEstudio
        SET		Titulo = pTitulo,
                Descripcion = pDescripcion,
                IdColor = pIdColor
		WHERE	IdCalendario = pIdCalendario;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
