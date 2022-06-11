DROP PROCEDURE IF EXISTS `dsp_alta_difusion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_difusion`(pJWT varchar(500), pDifusion varchar(60),
		pFechaInicio date, pFechaFin date, pIP varchar(40), 
        pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear una campaña de difusión controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id de la campaña de difusión creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pIdDifusion smallint;
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
    IF EXISTS (SELECT IdDifusion FROM Difusiones WHERE Difusion = pDifusion) THEN
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
        
        SET pIdDifusion = (SELECT COALESCE(MAX(IdDifusion),0) + 1 FROM Difusiones);
        INSERT INTO Difusiones VALUES(pIdDifusion, pDifusion, pFechaInicio, pFechaFin);
        
        SELECT CONCAT('OK',pIdDifusion) Mensaje;
	COMMIT;
END $$
DELIMITER ;
