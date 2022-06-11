DROP PROCEDURE IF EXISTS `dsp_alta_etiqueta_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_etiqueta_caso`(pJWT varchar(500), pIdEstudio int, pEtiqueta varchar(45), pIdCaso int)
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
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEtiqueta IS NULL OR pEtiqueta = '' THEN
		SELECT 'Debe indicar un nombre a la etiqueta.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM EtiquetasCaso WHERE Etiqueta = LOWER(pEtiqueta) AND IdEstudio = pIdEstudio AND IdCaso = pIdCaso) THEN
		SELECT 'Ya existe la etiqueta en el caso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		INSERT INTO EtiquetasCaso VALUES (0, pIdCaso, pIdEstudio, LOWER(pEtiqueta));
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
