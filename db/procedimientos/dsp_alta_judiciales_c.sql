set collation_connection=utf8mb4_unicode_ci;
DROP PROCEDURE IF EXISTS `dsp_alta_judiciales_c`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_judiciales_c`(pIdEstadoAmbitoGestion int, pIdUsuario int, pCantCasos int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdUsuario IS NULL THEN
        SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdEstadoAmbitoGestion IS NULL THEN
        SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pCantCasos IS NULL OR pCantCasos = 0 THEN
        SELECT 'Debe indicar la cantidad de casos.' Mensaje;
        LEAVE PROC;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM EstadoAmbitoGestion WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'El estado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'El usuario no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
            INSERT INTO JudicialesC (IdUsuario, IdEstadoAmbitoGestion, CantCasos)
            VALUES (pIdUsuario, pIdEstadoAmbitoGestion, pCantCasos);

            SELECT CONCAT('OK', LAST_INSERT_ID());
    COMMIT;
END $$
DELIMITER ;
