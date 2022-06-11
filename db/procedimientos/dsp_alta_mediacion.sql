DROP PROCEDURE IF EXISTS `dsp_alta_mediacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mediacion`(pJWT varchar(500), pIdMediador int, pIdCaso bigint, pIdBono int, pIdBeneficio int, pFechaBonos date,
                pFechaPresentado date, pFechaProximaAudiencia datetime, pLegajo varchar(45), pIdEstadoBeneficio int)
PROC: BEGIN
	/*
    Permite crear mediadores controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del mediador creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdMediacion int;
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
    IF pIdCaso IS NULL OR pIdCaso = '' THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Mediadores WHERE IdMediador = pIdMediador) AND pIdMediador IS NOT NULL THEN
		SELECT 'El mediador indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM Mediaciones WHERE IdCaso = pIdCaso) THEN
        SELECT 'El caso indicado ya posee una mediacion cargada';
    END IF;
    START TRANSACTION;        
        INSERT INTO Mediaciones SELECT 0, pIdMediador, pIdCaso, pIdBono, pIdBeneficio, pFechaBonos, pFechaPresentado, pFechaProximaAudiencia, pLegajo, pIdEstadoBeneficio;
        SET pIdMediacion = LAST_INSERT_ID();
        
        SELECT CONCAT('OK', pIdMediacion) Mensaje;
	COMMIT;
END $$
DELIMITER ;
