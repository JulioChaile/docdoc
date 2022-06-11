DROP PROCEDURE IF EXISTS `dsp_modificar_cuaderno`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_cuaderno`(pJWT varchar(500), pIdCuaderno int, pCuaderno varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un ObjetivoEstudio, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
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
    IF pIdCuaderno IS NULL THEN
		SELECT 'Debe indicar un objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pCuaderno IS NULL OR pCuaderno = '' THEN
		SELECT 'Debe indicar un nombre al objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM CuadernosEstudio WHERE IdCuaderno = pIdCuaderno) THEN
		SELECT 'El cuaderno indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdEstudio = (SELECT IdEstudio FROM CuadernosEstudio WHERE IdCuaderno = pIdCuaderno);
    IF EXISTS (SELECT 1 FROM CuadernosEstudio WHERE IdCuaderno != pIdCuaderno AND IdEstudio = pIdEstudio) THEN
		SELECT 'El nombre indicado para el cuaderno ya se encuentra en uso en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	CuadernosEstudio
        SET		Cuaderno = pCuaderno
        WHERE	IdCuaderno = pIdCuaderno;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
