DROP PROCEDURE IF EXISTS `dsp_borrar_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_estudio`(pJWT varchar(500), pIdEstudio int, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un Estudio controlando que no tenga Abogados ni casos asociados. 
    Devuelve OK o un Mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un Estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El Estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
    END IF;
    IF EXISTS (SELECT IdEstudio FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'No se puede borrar el Estudio. Existen Abogados asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstudio FROM UsuariosCaso WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'No se puede borrar el Estudio. Existen casos asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Auditoría
		INSERT INTO aud_Estudios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'BORRAR', 'B', Estudios.* 
        FROM Estudios WHERE IdEstudio = pIdEstudio;
        
		DELETE FROM Estudios WHERE IdEstudio = pIdEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
