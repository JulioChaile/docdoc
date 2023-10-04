DROP PROCEDURE IF EXISTS `dsp_modificar_objetivo`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_objetivo`(pJWT varchar(500), pIdObjetivo int, pObjetivo varchar(200), pIdTipoMov int, pColorMov varchar(45),
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un objetivo. Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pIdCaso bigint;
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
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdObjetivo IS NULL THEN
		SELECT 'Debe indicar un objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pObjetivo IS NULL OR TRIM(pObjetivo) = '' THEN
		SELECT 'Debe indicar un nombre al objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdObjetivo FROM Objetivos WHERE IdObjetivo = pIdObjetivo) THEN
		SELECT 'El objetivo indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdCaso = (SELECT IdCaso FROM Objetivos WHERE IdObjetivo = pIdObjetivo);
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('A', 'E')) THEN
		SELECT 'Usted no tiene permisos de administración ni de escritura sobre el caso. No puede crear objetivos.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
    
		UPDATE	Objetivos
        SET		Objetivo = pObjetivo,
              IdTipoMov = pIdTipoMov,
              ColorMov = pColorMov
        WHERE	IdObjetivo = pIdObjetivo;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
