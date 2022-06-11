DROP PROCEDURE IF EXISTS `dsp_modificar_evento`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_evento`(pJWT varchar(500), pIdEvento int, pTitulo varchar(100), pDescripcion text, pComienzo datetime, pFin datetime, pIdColor varchar(2))
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
		SELECT 'Debe indicar un titulo para el evento.' Mensaje;
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
    -- Control de parámetros vacíos
    IF NOT EXISTS (SELECT 1 FROM Eventos WHERE IdEvento = pIdEvento) THEN
		SELECT 'El evento indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        
        UPDATE	Eventos
        SET		Titulo = pTitulo,
                Descripcion = pDescripcion,
                IdColor = pIdColor,
                Comienzo = pComienzo,
                Fin = pFin
		WHERE	IdEvento = pIdEvento;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
