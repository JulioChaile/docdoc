DROP PROCEDURE IF EXISTS `dsp_modificar_mediacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_mediacion`(pJWT varchar(500), pIdMediacion int, pIdMediador int, pIdBono int, pIdBeneficio int, pFechaBonos datetime,
                pFechaPresentado datetime, pFechaProximaAudiencia datetime, pLegajo varchar(45), pIdEstadoBeneficio int)
PROC: BEGIN
	/*
    Permite modificar mediadors controlando que el nombre no se encuentre en uso ya. 
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
    IF pIdMediacion IS NULL OR pIdMediacion = '' THEN
		SELECT 'Debe indicar una mediacion.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Mediaciones WHERE IdMediacion = pIdMediacion) THEN
		SELECT 'La mediacion indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	Mediaciones
        SET		IdMediador = pIdMediador,
                IdBono = pIdBono,
                IdBeneficio = pIdBeneficio,
                FechaBonos = pFechaBonos,
                FechaPresentado = pFechaPresentado,
                FechaProximaAudiencia = pFechaProximaAudiencia,
                Legajo = pLegajo,
                IdEstadoBeneficio = pIdEstadoBeneficio
        WHERE	IdMediacion = pIdMediacion;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
