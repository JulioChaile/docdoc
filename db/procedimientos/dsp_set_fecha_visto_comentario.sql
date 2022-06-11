DROP PROCEDURE IF EXISTS `dsp_set_fecha_visto_comentario`;
DELIMITER $$
CREATE PROCEDURE `dsp_set_fecha_visto_comentario`(pJWT varchar(500), pIdCaso bigint, pIdUsuario int)
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
    IF pIdCaso IS NULL OR pIdCaso = '' THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuario IS NULL OR pIdUsuario = '' THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'No existe el usuario indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'No existe el caso indicado' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
        UPDATE  UsuariosComentarioCaso
        SET     FechaVisto = NOw()
        WHERE   IdCaso = pIdCaso AND
                IdUsuario = pIdUsuario AND
                FechaVisto IS NULL;
        
        SELECT CONCAT('OK') Mensaje;
	COMMIT;
END $$
DELIMITER ;
