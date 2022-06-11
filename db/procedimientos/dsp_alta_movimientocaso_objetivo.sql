DROP PROCEDURE IF EXISTS `dsp_alta_movimientocaso_objetivo`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_movimientocaso_objetivo`(pJWT varchar(500), pIdObjetivo int, pIdMovimientoCaso bigint, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite asociar un movimiento de caso a un objetivo siempre que el movimiento no se encuentre asociado ya a un objetivo. 
    Controla que el movimiento exista. Devuelve OK o el mensaje de error.
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
    IF pIdMovimientoCaso IS NULL THEN
		SELECT 'Debe indicar un movimiento de caso.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdObjetivo FROM Objetivos WHERE IdObjetivo = pIdObjetivo) THEN
		SELECT 'El objetivo indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdMovimientoCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
		SELECT 'El movimiento no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdCaso = (SELECT IdCaso FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso);
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('A', 'E')) THEN
		SELECT 'Usted no tiene permisos de administración ni de escritura sobre el caso. No puede crear objetivos.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdObjetivo FROM MovimientosObjetivo WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
		SET @Objetivo = (SELECT Objetivo FROM Objetivos WHERE IdObjetivo = pIdObjetivo);
		SELECT CONCAT('El movimiento indicado ya está asociado al objetivo ', @Objetivo,'.') Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdMovimientoCaso FROM MovimientosCaso mc INNER JOIN MovimientosObjetivo mo USING (IdMovimientoCaso) INNER JOIN 
    Objetivos o USING (IdObjetivo) WHERE mo.IdObjetivo = pIdObjetivo AND mc.IdCaso != pIdCaso) THEN
		SELECT 'No puede agrupar movimientos de casos distintos bajo un mismo objetivo.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		INSERT INTO MovimientosObjetivo VALUES (pIdObjetivo, pIdMovimientoCaso);
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
