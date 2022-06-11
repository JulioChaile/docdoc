DROP PROCEDURE IF EXISTS `dsp_borrar_tipomovimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_tipomovimiento`(pJWT varchar(500), pIdTipoMov int, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un tipo de movimiento controlando que no tenga movimientos asociados.
    Devuelve OK o un mensaje de error en Mensaje.
    */
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
    -- Conrol de parámetros vacios
    IF pIdTipoMov IS NULL THEN
		SELECT 'Debe indicar el tipo de movimiento.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdTipoMov FROM TiposMovimiento WHERE IdTipoMov = pIdTipoMov) THEN
		SELECT 'El tipo de movimiento indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdMovimientoCaso FROM MovimientosCaso WHERE IdTipoMov = pIdTipoMov) THEN
		SELECT 'No se puede borrar el tipo de movimiento. Existen movimientos de caso asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		DELETE FROM	TiposMovimiento WHERE IdTipoMov = pIdTipoMov;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
