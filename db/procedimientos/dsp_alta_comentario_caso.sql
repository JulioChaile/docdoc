DROP PROCEDURE IF EXISTS `dsp_alta_comentario_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_comentario_caso`(pJWT varchar(500), pComentario text, pIdCaso bigint, pIdUsuario int)
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
    IF pComentario IS NULL OR pComentario = '' THEN
		SELECT 'El comentario no puede estar vacio.' Mensaje;
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

    START TRANSACTION;
        INSERT INTO ComentariosCaso VALUES(0, pComentario, NOW(), pIdCaso, pIdUsuario);
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
