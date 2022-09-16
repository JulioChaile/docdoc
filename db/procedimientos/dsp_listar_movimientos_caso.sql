DROP PROCEDURE IF EXISTS `dsp_listar_movimientos_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_movimientos_caso`(pJWT varchar(500), pIdCaso bigint, pOffset int, pLimit int, pCadena varchar(400), pColor varchar(20), pUsuarios json, pTipos json, pIdUsuarioGestion int, pTareas int)
PROC: BEGIN
	/*
    Permite listar todos los movimientos de un caso. Lista todos si el IdCaso = 0.
    Ordena por FechaRealizado
    */
    DECLARE pIdEstudio, pIdUsuario int;
    
    SET pCadena = COALESCE(pCadena,'');
    SET pColor = COALESCE(pColor,'');
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
    FROM		(SELECT		mc.*, c.Caratula, tm.TipoMovimiento, o.IdObjetivo, o.Objetivo, eag.EstadoAmbitoGestion, c.IdEstadoAmbitoGestion, audmc.UsuarioAud UsuarioEdicion
				FROM		MovimientosCaso mc
				INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
                LEFT JOIN	MovimientosObjetivo mo USING (IdMovimientoCaso)
				LEFT JOIN	Objetivos o USING (IdObjetivo)
				INNER JOIN	Casos c ON c.IdCaso = mc.IdCaso
				INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
				INNER JOIN 	Usuarios u ON uc.IdUsuario = u.IdUsuario
				INNER JOIN 	UsuariosEstudio ue ON ue.IdUsuario = u.IdUsuario
				LEFT JOIN 	(SELECT UsuarioAud, amc.IdMovimientoCaso, amc.Id FROM aud_MovimientosCaso amc WHERE amc.Motivo = 'MODIFICAR' AND amc.TipoAud = 'D' ORDER BY Id DESC) audmc ON mc.IdMovimientoCaso = audmc.IdMovimientoCaso
				LEFT JOIN	EstadoAmbitoGestion eag USING(IdEstadoAmbitoGestion)
				WHERE		c.Estado != 'B' AND ue.IdEstudio = pIdEstudio
							AND (pIdCaso = 0 OR pIdCaso = '' OR mc.IdCaso = pIdCaso)
							AND (
									pCadena != 'dWz6H78mpQ' OR
									(
										pCadena = 'dWz6H78mpQ' AND
										mc.Escrito = 'dWz6H78mpQ' AND
										mc.IdResponsable = uc.IdUsuarioCaso AND
										uc.IdUsuario = pIdUsuario AND
										mc.FechaRealizado IS NULL
									)
								)
							AND (
									pTareas = 0 OR
									(
										(
											(pIdUsuarioGestion = 0 OR pIdUsuarioGestion = '' OR pIdUsuarioGestion IS NULL) OR
											mc.IdUsuarioCaso = (SELECT IdUsuarioCaso FROM UsuariosCaso WHERE IdUsuario = pIdUsuarioGestion AND IdCaso = mc.IdCaso)
										) AND
										mc.Escrito = 'dWz6H78mpQ' AND
										mc.FechaRealizado IS NULL
									)
								)
							AND (JSON_CONTAINS(pTipos, CONCAT('"', tm.TipoMovimiento, '"')) = 1 OR JSON_EXTRACT(pTipos, '$[0]') IS NULL)
							AND (mc.Detalle LIKE CONCAT('%', pCadena, '%') OR pCadena = 'dWz6H78mpQ')
							AND (mc.Color = pColor OR pColor = '') GROUP BY mc.IdMovimientoCaso) a
	LEFT JOIN	UsuariosCaso uc ON a.IdResponsable=uc.IdUsuarioCaso
    LEFT JOIN	Usuarios u ON u.IdUsuario = uc.IdUsuario
	WHERE		JSON_CONTAINS(pUsuarios, CONCAT('"', CONCAT(u.Apellidos, ', ', u.Nombres), '"')) = 1 OR JSON_EXTRACT(pUsuarios, '$[0]') IS NULL
	ORDER BY	COALESCE(a.FechaEsperada, a.FechaEdicion, a.FechaAlta) DESC
	LIMIT		pOffset, pLimit;
END $$
DELIMITER ;
