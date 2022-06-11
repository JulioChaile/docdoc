DROP PROCEDURE IF EXISTS `dsp_borrar_contacto_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_contacto_estudio`(pJWT varchar(500), pIdContacto int, pIdEstudio int)
PROC: BEGIN
	/*
    Permite borrar un mediador controlando que no tenga roles asociados. 
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
    IF pIdContacto IS NULL OR pIdContacto = '' THEN
		SELECT 'Debe indicar un contacto.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL OR pIdEstudio = '' THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM ContactosEstudio WHERE IdContacto = pIdContacto) THEN
		SELECT 'El contacto indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM ContactosEstudio WHERE IdContacto = pIdContacto AND IdEstudio = pIdEstudio) THEN
		SELECT 'El contacto no pertenece a este estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		  DELETE FROM ContactosEstudio
      WHERE       IdContacto = pIdContacto;
       
      SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
