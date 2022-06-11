DROP PROCEDURE IF EXISTS `dsp_borrar_cuaderno`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_cuaderno`(pJWT varchar(500), pIdCuaderno int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un ObjetivoEstudio controlando que el mismo no tenga subestados asociados. 
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
    IF pIdCuaderno IS NULL THEN
		SELECT 'Debe indicar un cuaderno.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM CuadernosEstudio WHERE IdCuaderno = pIdCuaderno) THEN
		SELECT 'El cuaderno indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;        
		DELETE FROM CuadernosEstudio WHERE IdCuaderno = pIdCuaderno;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
