DROP PROCEDURE IF EXISTS `dsp_modificar_tipomovimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_tipomovimiento`(pJWT varchar(500), pIdTipoMov int,
		pTipoMovimiento varchar(50), pCategoria char(1), 
        pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar el nombre y categoría de un tipo de movimiento controlando que no exista
    el nombre para la nueva categoría indicada. Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			SHOW ERRORS;
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Conrol de parámetros vacios
    IF pIdTipoMov IS NULL THEN
		SELECT 'Debe indicar el tipo de movimiento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipoMovimiento IS NULL OR pTipoMovimiento = '' THEN
		SELECT 'Debe indicar el nombre del tipo de movimiento.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pCategoria IS NULL OR pCategoria = '' THEN
		SELECT 'Debe indicar la categoría del movimiento.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdTipoMov FROM TiposMovimiento WHERE IdTipoMov = pIdTipoMov) THEN
		SELECT 'El tipo de movimiento indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdEstudio = (SELECT IdEstudio FROM TiposMovimiento WHERE IdTipoMov = pIdTipoMov);
    IF EXISTS (SELECT IdTipoMov FROM TiposMovimiento WHERE IdEstudio = pIdEstudio AND Categoria = pCategoria AND TipoMovimiento = pTipoMovimiento
    AND IdTipoMov != pIdTipoMov) THEN
		SELECT 'El nombre indicado ya existe en la categoría.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
    
		UPDATE	TiposMovimiento
        SET		TipoMovimiento = pTipoMovimiento,
				Categoria = pCategoria
		WHERE	IdTipoMov = pIdTipoMov;
		
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
