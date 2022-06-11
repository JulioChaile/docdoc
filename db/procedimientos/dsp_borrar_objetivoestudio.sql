DROP PROCEDURE IF EXISTS `dsp_borrar_objetivoestudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_objetivoestudio`(pJWT varchar(500), pIdObjetivoEstudio int, 
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
    IF pIdObjetivoEstudio IS NULL THEN
		SELECT 'Debe indicar un objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdObjetivoEstudio FROM ObjetivosEstudio WHERE IdObjetivoEstudio = pIdObjetivoEstudio) THEN
		SELECT 'El objetivo indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;        
		DELETE FROM ObjetivosEstudio WHERE IdObjetivoEstudio = pIdObjetivoEstudio;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
