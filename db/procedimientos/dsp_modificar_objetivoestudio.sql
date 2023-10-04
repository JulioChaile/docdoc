DROP PROCEDURE IF EXISTS `dsp_modificar_objetivoestudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_objetivoestudio`(pJWT varchar(500), pIdObjetivoEstudio int, pObjetivoEstudio varchar(50), pIdTipoMov int, pColorMov varchar(45),
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
    IF pIdObjetivoEstudio IS NULL THEN
		SELECT 'Debe indicar un objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pObjetivoEstudio IS NULL OR pObjetivoEstudio = '' THEN
		SELECT 'Debe indicar un nombre al objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdObjetivoEstudio FROM ObjetivosEstudio WHERE IdObjetivoEstudio = pIdObjetivoEstudio) THEN
		SELECT 'El objetivo indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdEstudio = (SELECT IdEstudio FROM ObjetivosEstudio WHERE IdObjetivoEstudio = pIdObjetivoEstudio);
    IF EXISTS (SELECT IdObjetivoEstudio FROM ObjetivosEstudio WHERE ObjetivoEstudio = pObjetivoEstudio AND IdEstudio = pIdEstudio AND IdObjetivoEstudio != pIdObjetivoEstudio) THEN
		SELECT 'El nombre indicado para el objetivo ya se encuentra en uso en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	ObjetivosEstudio
        SET		ObjetivoEstudio = pObjetivoEstudio,
              IdTipoMov = pIdTipoMov,
              ColorMov = pColorMov
        WHERE	IdObjetivoEstudio = pIdObjetivoEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
