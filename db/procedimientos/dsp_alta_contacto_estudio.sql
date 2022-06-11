DROP PROCEDURE IF EXISTS `dsp_alta_contacto_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_contacto_estudio`(pJWT varchar(500), pIdEstudio int, pNombres varchar(100), pApellidos varchar(45), pTelefono varchar(200), pEmail varchar(50), pTipo char(1))
PROC: BEGIN
	/*
    Permite crear contactos 
    Devuelve OK + Id del contacto creado o un mensaje de error en Mensaje.
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
	-- Control de parámetros vacíos
    IF pNombres IS NULL OR pNombres = '' THEN
		SELECT 'Debe indicar el nombre del contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar el apellido del contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipo IS NULL OR pTipo = '' THEN
		SELECT 'Debe indicar el tipo de contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL OR pIdEstudio = '' THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS   (SELECT 1
                FROM ContactosEstudio
                WHERE IdEstudio = pIdEstudio AND
                    (
                        (Telefono = pTelefono AND (pTelefono IS NOT NULL AND pTelefono != '')) OR
                        (Email = pEmail AND (pEmail IS NOT NULL AND pEmail != ''))
                    )
                ) THEN
		SELECT 'Ya existe un contacto en el estudio con los mismos datos de telefono y/o email.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'No existe el estudio indicado' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;        
        INSERT INTO ContactosEstudio SELECT 0, pIdEstudio, pApellidos, pNombres, pTelefono, pEmail, pTipo;
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
