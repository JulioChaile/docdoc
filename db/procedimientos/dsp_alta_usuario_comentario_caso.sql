DROP PROCEDURE IF EXISTS `dsp_alta_usuario_comentario_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario_comentario_caso`(pJWT varchar(500), pIdComentarioCaso int, pIdCaso bigint, pIdUsuario int)
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
    IF pIdComentarioCaso IS NULL OR pIdComentarioCaso = '' THEN
		SELECT 'Debe indicar un comentario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCaso IS NULL OR pIdCaso = '' THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuario IS NULL OR pIdUsuario = '' THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'No existe el caso indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE IdUsuario = pIdUsuario) THEN
		SELECT 'No existe el usuario indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM ComentariosCaso WHERE IdComentarioCaso = pIdComentarioCaso) THEN
		SELECT 'No existe el comentario indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM UsuariosComentarioCaso WHERE IdComentarioCaso = pIdComentarioCaso AND IdUsuario = pIdUsuario) THEN
		SELECT 'Este usuario ya fue etiquetado en el comentario' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
        INSERT INTO UsuariosComentarioCaso VALUES(pIdCaso, pIdComentarioCaso, pIdUsuario, NULL);
        
        SELECT CONCAT('OK') Mensaje;
	COMMIT;
END $$
DELIMITER ;
