DROP PROCEDURE IF EXISTS `dsp_alta_calendario_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_calendario_estudio`(pJWT varchar(500), pIdEstudio int, pIdCalendarioAPI varchar(300), pTitulo varchar(100), pDescripcion text, pIdColor varchar(2))
PROC: BEGIN
    DECLARE pIdUsuarioGestion int;
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Se debe indicar el estudio al cual pertenecera el calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCalendarioAPI IS NULL THEN
		SELECT 'Debe indicar el id del calendario creado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTitulo IS NULL THEN
		SELECT 'Debe ponerle un titulo al calendario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM CalendariosEstudio WHERE Titulo = pTitulo) THEN
		SELECT 'El titulo ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM CalendariosEstudio WHERE IdCalendarioAPI = pIdCalendarioAPI) THEN
		SELECT 'El calendario indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		INSERT INTO CalendariosEstudio VALUES (0, pIdCalendarioAPI, pIdEstudio, pTitulo, pDescripcion, pIdColor);
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
