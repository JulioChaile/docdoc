DROP PROCEDURE IF EXISTS `dsp_listar_movimientos_realizados_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_movimientos_realizados_caso`(pJWT varchar(500), pIdCaso bigint)
PROC: BEGIN
	/*
    Permite listar todos los movimientos de un caso. Lista todos si el IdCaso = 0.
    Ordena por FechaRealizado
    */
    DECLARE pIdEstudio, pIdUsuario int;
    
    SET pIdUsuario = (SELECT COALESCE(IdUsuario,0) FROM Usuarios WHERE Token = pJWT AND Estado = 'A');
    IF EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario AND IdRol IS NULL) THEN
		IF pIdUsuario = 0 THEN
			SELECT NULL;
			LEAVE PROC;
		END IF;
		SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND Estado = 'A');
		IF pIdEstudio IS NULL THEN
			SELECT NULL;
			LEAVE PROC;
		END IF;
    END IF;
    SELECT		u.IdUsuario IdUsuarioResponsable, 
				CONCAT(u.Apellidos, ', ', u.Nombres) UsuarioResponsable,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('URL', Url, 'Tipo', Tipo)) 
                FROM MultimediaMovimiento 
                INNER JOIN Multimedia USING(IdMultimedia)
                WHERE IdMovimientoCaso=a.IdMovimientoCaso) Multimedia, a.*
    FROM		(SELECT		mc.*, c.Caratula, tm.TipoMovimiento, o.IdObjetivo, o.Objetivo
				FROM		MovimientosCaso mc
				INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
                LEFT JOIN	MovimientosObjetivo mo USING (IdMovimientoCaso)
				LEFT JOIN	Objetivos o USING (IdObjetivo)
				INNER JOIN	Casos c ON c.IdCaso = mc.IdCaso
				INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
				WHERE		c.Estado != 'B' AND (
								(uc.IdUsuario = pIdUsuario AND uc.Permiso = 'A') 
								OR (mc.IdResponsable = uc.IdUsuarioCaso AND uc.IdUsuario = pIdUsuario)
							) 
							AND (pIdCaso = 0 OR mc.IdCaso = pIdCaso)
							AND mc.FechaRealizado IS NOT NULL
				ORDER BY	COALESCE(mc.FechaEdicion, mc.FechaAlta) DESC) a
	LEFT JOIN	UsuariosCaso uc ON a.IdResponsable=uc.IdUsuarioCaso
    LEFT JOIN	Usuarios u ON u.IdUsuario = uc.IdUsuario;
END $$
DELIMITER ;
