DROP PROCEDURE IF EXISTS `dsp_alta_tipomovimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_tipomovimiento`(pJWT varchar(500), pIdEstudio int,	
		pTipoMovimiento varchar(50), pCategoria char(1),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un tipo de movimiento controlando que el nombre no esté en uso para la categoría dada.
    Devuelve OK + Id del tipo de movimiento creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdTipoMov int;
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
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
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdTipoMov FROM TiposMovimiento WHERE Categoria = pCategoria AND TipoMovimiento = pTipoMovimiento) THEN
		SELECT 'El nombre indicado ya existe en la categoría.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pIdTipoMov = (SELECT COALESCE(MAX(IdTipoMov),0) + 1 FROM TiposMovimiento);
        
        INSERT INTO TiposMovimiento VALUES(pIdTipoMov, pIdEstudio, pTipoMovimiento, pCategoria);
        
        SELECT CONCAT('OK',pIdTipoMov) Mensaje;
	COMMIT;
END $$
DELIMITER ;
