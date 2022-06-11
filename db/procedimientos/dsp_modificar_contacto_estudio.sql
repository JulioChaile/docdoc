DROP PROCEDURE IF EXISTS `dsp_modificar_contacto_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_contacto_estudio`(pJWT varchar(500), pIdContacto int, pIdEstudio int, pNombres varchar(255), pApellidos varchar(100), pTelefono varchar(200), pEmail varchar(50), pTipo char(1))
PROC: BEGIN
	/*
    Permite modificar mediadors controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
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
    IF pNombres IS NULL OR pNombres = '' THEN
		SELECT 'Debe indicar el nombre del contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar el apellido del contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTipo IS NULL OR pTipo = '' THEN
		SELECT 'Debe indicar el tipo del contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdContacto IS NULL THEN
		SELECT 'Debe indicar una contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar una estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM ContactosEstudio WHERE IdEstudio = pIdEstudio AND (Telefono = pTelefono OR Email = pEmail) AND IdContacto != pIdContacto) THEN
		SELECT 'Ya existe un contacto en el estudio con los mismos datos de telefono y/o email.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM ContactosEstudio WHERE IdContacto = pIdContacto) THEN
		SELECT 'El contacto indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM ContactosEstudio WHERE IdContacto = pIdContacto AND IdEstudio = pIdEstudio) THEN
		SELECT 'El contacto indicado no existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	ContactosEstudio
        SET		Nombres = pNombres,
                Apellidos = pApellidos,
                Telefono = pTelefono,
                Email = pEmail,
                Tipo = pTipo
        WHERE	IdContacto = pIdContacto;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
