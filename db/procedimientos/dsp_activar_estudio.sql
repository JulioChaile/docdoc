DROP PROCEDURE IF EXISTS `dsp_activar_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_activar_estudio`(pJWT varchar(500), pIdEstudio int, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite cambiar el Estado de un Estudio a Activo. 
    Devuelve OK o un Mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
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
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Cotrol de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El Estudio indicada no existe en el sitema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF (SELECT Estado FROM Estudios WHERE IdEstudio = pIdEstudio) = 'A' THEN
		SELECT 'OK' Mensaje;
		LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        -- Antes
		INSERT INTO aud_Estudios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'A', Estudios.* 
        FROM Estudios WHERE IdEstudio = pIdEstudio;
        
		UPDATE	Estudios
        SET		Estado = 'A'
        WHERE	IdEstudio = pIdEstudio;
        
        -- Después
		INSERT INTO aud_Estudios
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ACTIVAR', 'D', Estudios.* 
        FROM Estudios WHERE IdEstudio = pIdEstudio;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
