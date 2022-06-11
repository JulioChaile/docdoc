DROP PROCEDURE IF EXISTS `dsp_eliminar_etiqueta_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_eliminar_etiqueta_caso`(pJWT varchar(500), pIdEtiquetaCaso int)
PROC: BEGIN
	/*
    Permite dar de alta un objetivo a un caso. Devuelve OK + Id del caso creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
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
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio jurídico. Contáctese con soporte.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEtiquetaCaso IS NULL THEN
		SELECT 'Debe indicar una etiqueta.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM EtiquetasCaso WHERE IdEtiquetaCaso = pIdEtiquetaCaso) THEN
		SELECT 'La etiqueta indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        DELETE FROM EtiquetasCaso WHERE IdEtiquetaCaso = pIdEtiquetaCaso;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
