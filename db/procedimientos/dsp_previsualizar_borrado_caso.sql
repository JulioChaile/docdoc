DROP PROCEDURE IF EXISTS `dsp_previsualizar_borrado_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_previsualizar_borrado_caso`(pJWT varchar(500), pIdCaso bigint)
PROC: BEGIN
	/*
    Permite obtener una previsualización de los elementos a ser eliminados en un caso.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
	SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF NOT EXISTS (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdUsuario = pIdUsuarioGestion
    AND Permiso IN ('E','A')) THEN
		SELECT 'No puede previsualizar el borrado del caso. Debe tener permiso de Edición o Administración sobre el mismo.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SELECT	COALESCE((SELECT COUNT(IdPersona) FROM PersonasCaso WHERE IdCaso = pIdCaso GROUP BY IdCaso),0) CantPersonas,
			COALESCE((SELECT COUNT(IdMovimientoCaso) FROM MovimientosCaso WHERE IdCaso = pIdCaso GROUP BY IdCaso),0) CantMovimientos,
			COALESCE((SELECT COUNT(IdMultimedia) FROM MultimediaMovimiento mm INNER JOIN MovimientosCaso mc USING (IdMovimientoCaso)
            INNER JOIN Casos c USING (IdCaso) WHERE c.IdCaso = pIdCaso),0) CantMultimedia,
            COALESCE((SELECT COUNT(IdUsuarioCaso) FROM UsuariosCaso WHERE IdCaso = pIdCaso), 0) CantUsuariosCaso;
END $$
DELIMITER ;
