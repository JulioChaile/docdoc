DROP PROCEDURE IF EXISTS `dsp_alta_objetivo`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_objetivo`(pJWT varchar(500), pIdCaso bigint, pObjetivo varchar(200), pIdTipoMov int, pColorMov varchar(45),
				pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta un objetivo a un caso. Devuelve OK + Id del caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdObjetivo, pIdEstudio int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			show errors;
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
    IF pIdCaso IS NULL THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pObjetivo IS NULL OR TRIM(pObjetivo) = '' THEN
		SELECT 'Debe indicar un nombre al objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('A', 'E')) THEN
		SELECT 'Usted no tiene permisos de administración ni de escritura sobre el caso. No puede crear objetivos.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pIdObjetivo = (SELECT COALESCE(MAX(IdObjetivo),0) + 1 FROM Objetivos);
        
        INSERT INTO Objetivos VALUES(pIdObjetivo, pIdCaso, pObjetivo, NOW(), pIdTipoMov, pColorMov);
        
        SELECT CONCAT('OK', pIdObjetivo) Mensaje;
	COMMIT;
END $$
DELIMITER ;
