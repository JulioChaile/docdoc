DROP PROCEDURE IF EXISTS `dsp_alta_cuaderno`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_cuaderno`(pJWT varchar(500), pIdEstudio int, pCuaderno varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un ObjetivoEstudio, controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdObjetivoEstudio int;
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pCuaderno IS NULL OR pCuaderno = '' THEN
		SELECT 'Debe indicar un nombre al cuaderno.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM CuadernosEstudio WHERE Cuaderno = pCuaderno AND IdEstudio = pIdEstudio) THEN
		SELECT 'El nombre indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		INSERT INTO CuadernosEstudio VALUES (0, pIdEstudio, pCuaderno);
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
