DROP PROCEDURE IF EXISTS `dsp_borrar_usuario_acl`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_usuario_acl`(pJWT varchar(500), pIdACL int)
PROC: BEGIN
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
    IF pIdACL IS NULL THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM UsuariosACL WHERE IdACL = pIdACL) THEN
		SELECT 'El usuario indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		DELETE FROM UsuariosACL WHERE IdACL = pIdACL;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
