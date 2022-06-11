DROP PROCEDURE IF EXISTS `dsp_alta_evento_mediacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_evento_mediacion`(pIdEvento int, pIdMediacion int)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Control de parámetros vacíos
    IF pIdEvento IS NULL THEN
		SELECT 'Se debe indicar un evento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdMediacion IS NULL THEN
		SELECT 'Se debe indicar una mediacion.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Eventos WHERE IdEvento = pIdEvento) THEN
		SELECT 'El evento indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Mediaciones WHERE IdMediacion = pIdMediacion) THEN
		SELECT 'La mediacion indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		INSERT INTO EventosMediaciones VALUES (pIdEvento, pIdMediacion);
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
