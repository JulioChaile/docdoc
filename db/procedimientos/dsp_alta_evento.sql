DROP PROCEDURE IF EXISTS `dsp_alta_evento`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_evento`(pJWT varchar(500), pIdEventoAPI varchar(300), pIdCalendario int, pTitulo varchar(500), pDescripcion text, pComienzo datetime, pFin datetime, pIdColor varchar(2))
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
    IF pIdEventoAPI IS NULL THEN
		SELECT 'Se debe indicar el id del evento creado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCalendario IS NULL THEN
		SELECT 'Debe indicar el calendario al cual pertenece el evento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTitulo IS NULL THEN
		SELECT 'Debe ponerle un titulo al evento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdColor IS NULL THEN
		SELECT 'Debe indicar un color para el evento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pComienzo IS NULL THEN
		SELECT 'La hora de comienzo no puede estar vacia.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pFin IS NULL THEN
		SELECT 'La hora de finalizacion no puede estar vacia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM CalendariosEstudio WHERE IdCalendario = pIdCalendario) THEN
		SELECT 'El calendario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM Eventos WHERE IdEventoAPI = pIdEventoAPI) THEN
		SELECT 'El evento indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		  INSERT INTO Eventos VALUES (0, pIdEventoAPI, pIdCalendario, pTitulo, pDescripcion, pComienzo, pFin, pIdColor);
        
      SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
