DROP PROCEDURE IF EXISTS `dsp_modificar_difusion`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_difusion`(pJWT varchar(500), pIdDifusion smallint, pDifusion varchar(60), 
			pFechaInicio date, pFechaFin date, 
            pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar una campaña de difusión controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    DECLARE aux date;
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
	IF pIdDifusion IS NULL THEN
		SELECT 'Debe indicar una campaña de difusión.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pDifusion IS NULL OR pDifusion = '' THEN
		SELECT 'Debe indicar el nombre de la campaña de difusión.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pFechaInicio IS NULL THEN 
		SELECT 'Debe indicar la fecha de inicio de la campaña.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pFechaFin IS NULL THEN
		SELECT 'Debe indicar la fecha de finalización de la campaña.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdDifusion FROM Difusiones WHERE IdDifusion = pIdDifusion) THEN
		SELECT 'La campaña indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdDifusion FROM Difusiones WHERE Difusion = pDifusion AND IdDifusion != pIdDifusion) THEN
		SELECT 'El nombre de la campaña ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pFechaInicio > pFechaFin THEN
		SET aux = pFechaInicio;
        SET pFechaInicio = pFechaFin;
        SET pFechaFin = aux;
	END IF;
	START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        UPDATE	Difusiones
        SET		Difusion = pDifusion,
				FechaInicio = pFechaInicio,
                FechaFin = pFechaFin
		WHERE	IdDifusion = pIdDifusion;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
