DROP PROCEDURE IF EXISTS `dsp_alta_movimiento_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_movimiento_caso`(pJWT varchar(500), pIdCaso int, 
			pIdTipoMov int, pIdResponsable int, pDetalle text, pFechaEsperada datetime,
            pCuaderno varchar(45), pEscrito text, pColor varchar(10), pMultimedia json, pFechaAlta date, pCliente char(1),
            pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
	Permite dar de alta un movimiento a un caso, controlando que el usuario sea un usuario del caso y
    tenga permiso de Edición o Administración sobre el caso, que el responsable sea un usuario del
    caso y tenga permiso de Edición o Administración sobre el caso.
    */
    DECLARE pIdUsuarioGestion, pIdUsuarioCaso, pIdResponsableCaso, pIdEstudio, pIndice, pIdMultimedia int;
    DECLARE pFechaRealizado date;
    DECLARE pIdMovimientoCaso bigint;
    DECLARE pUsuario varchar(120);
    DECLARE pURL varchar(100);
    DECLARE pTipo char(1);
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
    -- Control de parámetros vacíos
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdTipoMov IS NULL OR pIdTipoMov = 0 THEN
		SELECT 'Debe indicar el tipo de movimiento.' Mensaje;
        LEAVE PROC;
    END IF;
     /*IF pFechaEsperada IS NULL THEN
		SELECT 'Debe indicar la fecha del movimiento.' Mensaje;
        LEAVE PROC;
    END IF; */
    IF pDetalle IS NULL OR TRIM(pDetalle) = '' THEN
		SELECT 'Debe indicar el detalle del movimiento.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdResponsable IS NULL THEN
		SET pIdResponsable = pIdUsuarioGestion;
    END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCaso FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion AND Permiso IN ('A','E')) THEN
		SELECT 'Usted no tiene permiso para modificar el caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdResponsable AND Permiso IN ('A','E')) THEN
		SELECT 'La persona indicada como responsable del movimiento no tiene permiso para modificar el caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdTipoMov FROM TiposMovimiento WHERE IdTipoMov = pIdTipoMov /*AND IdEstudio = pIdEstudio*/) THEN
		SELECT 'El tipo de movimiento indicado no es válido.' Mensaje;
        LEAVE PROC;
	END IF;
    
    /* SET pFechaEsperada = COALESCE(pFechaEsperada, CURDATE()); */
    
    /*IF pFechaEsperada <= CURDATE() THEN
		SET pFechaRealizado = pFechaEsperada;
	END IF;*/
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIdUsuarioCaso = (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdUsuario = pIdUsuarioGestion AND IdCaso = pIdCaso AND IdEstudio = pIdEstudio);
        SET pIdResponsableCaso = (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdUsuario = pIdResponsable AND IdCaso = pIdCaso AND IdEstudio = pIdEstudio);
        
        INSERT INTO MovimientosCaso VALUES (0, pIdCaso, pIdTipoMov, pIdUsuarioCaso, pIdResponsableCaso, pDetalle,
											pFechaAlta, NOW(), pFechaEsperada, pFechaRealizado, pCuaderno, pEscrito, pColor);
        
        SET pIdMovimientoCaso = LAST_INSERT_ID();
        
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', MovimientosCaso.* 
		FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;
		
        SET pIndice = 0;
        WHILE pIndice < JSON_LENGTH(pMultimedia) DO
			SET pIdMultimedia = (SELECT COALESCE(MAX(IdMultimedia), 0) + 1 FROM Multimedia);
            SET pURL = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].Url')));
            SET pTipo = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].Tipo')));
			
            INSERT INTO Multimedia VALUES (pIdMultimedia, pURL, pFechaAlta, pTipo, pURL);
            
            INSERT INTO MultimediaCaso VALUES (pIdMultimedia, pIdCaso, 'M');

            INSERT INTO MultimediaMovimiento VALUES (pIdMovimientoCaso, pIdMultimedia);
            
			SET pIndice = pIndice + 1;	
        END WHILE;

        IF pCliente IS NOT NULL AND pCliente != '' THEN
            INSERT INTO MovimientosClientes VALUES (pIdMovimientoCaso, pIdCaso);
        END IF;
        
        SELECT CONCAT('OK', pIdMovimientoCaso) Mensaje;
    COMMIT;
END $$
DELIMITER ;
