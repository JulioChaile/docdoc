DROP PROCEDURE IF EXISTS `dsp_alta_origen`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_origen`(pJWT varchar(500), pOrigen varchar(150), pIdEstudio int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un origen de caso controlando que no exista ya en el estudio indicado.
    Devuelve OK + Id del origen creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdOrigen int;
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
    IF pOrigen IS NULL OR TRIM(pOrigen) = '' THEN
		SELECT 'Debe indicar el nombre del origen.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT IdOrigen FROM Origenes WHERE Origen = pOrigen) THEN
		SELECT 'El origen indicado ya existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIdOrigen = (SELECT COALESCE(MAX(IdOrigen),0) + 1 FROM Origenes);
        INSERT INTO Origenes VALUES(pIdOrigen, TRIM(pOrigen), pIdEstudio);
        
        SELECT CONCAT('OK', pIdOrigen) Mensaje;
	COMMIT;
END $$
DELIMITER ;
