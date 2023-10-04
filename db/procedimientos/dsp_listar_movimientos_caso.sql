DROP PROCEDURE IF EXISTS `dsp_listar_movimientos_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_movimientos_caso`(pJWT varchar(500), pIdCaso bigint, pOffset int, pLimit int, pCadena varchar(400), pColor json, pUsuarios json, pTipos json, pIdUsuarioGestion int, pTareas int, pRecordatorios int, pTipoAudiencia varchar(500), pOrden varchar(500), pFecha varchar(45))
PROC: BEGIN
	/*
    Permite listar todos los movimientos de un caso. Lista todos si el IdCaso = 0.
    Ordena por FechaRealizado
    */
    DECLARE pIdEstudio, pIdUsuario int;
    
	SET sort_buffer_size = 256000000;
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
    FROM		(SELECT		mc.*, rm.IdRecordatorioMovimiento, c.Caratula, tm.TipoMovimiento, o.IdObjetivo, o.Objetivo, eag.EstadoAmbitoGestion, c.IdEstadoAmbitoGestion, audmc.UsuarioAud UsuarioEdicion
				FROM		MovimientosCaso mc
    			LEFT JOIN   Audiencias ma USING(IdMovimientoCaso)
				INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
                LEFT JOIN	MovimientosObjetivo mo USING (IdMovimientoCaso)
				LEFT JOIN	Objetivos o USING (IdObjetivo)
				INNER JOIN	Casos c ON c.IdCaso = mc.IdCaso
				INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
				INNER JOIN 	Usuarios u ON uc.IdUsuario = u.IdUsuario
				INNER JOIN 	UsuariosEstudio ue ON ue.IdUsuario = u.IdUsuario
				LEFT JOIN 	(SELECT UsuarioAud, amc.IdMovimientoCaso, amc.Id FROM aud_MovimientosCaso amc WHERE amc.Motivo = 'MODIFICAR' AND amc.TipoAud = 'D' ORDER BY Id DESC) audmc ON mc.IdMovimientoCaso = audmc.IdMovimientoCaso
				LEFT JOIN	EstadoAmbitoGestion eag USING(IdEstadoAmbitoGestion)
				LEFT JOIN	RecordatorioMovimiento rm ON rm.IdMovimientoCaso = mc.IdMovimientoCaso
				WHERE		c.Estado NOT IN ('B', 'P', 'F', 'R', 'E') AND ue.IdEstudio = pIdEstudio
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
							AND (
								pFecha = '' OR
								pFecha IS NULL OR
								(pFecha = 'hoy' AND DATE(mc.FechaEsperada) = DATE(NOW()) AND mc.FechaRealizado IS NULL) OR
								(pFecha = 'futuros' AND DATE(mc.FechaEsperada) > DATE(NOW()) AND mc.FechaRealizado IS NULL) OR
								(pFecha = 'vencidos' AND DATE(mc.FechaEsperada) < DATE(NOW()) AND mc.FechaRealizado IS NULL)
							)
							AND (pTipoAudiencia = '' OR pTipoAudiencia IS NULL OR
								ma.IdMovimientoCaso IS NOT NULL AND
								c.Caratula LIKE CONCAT('%', pCadena, '%') AND
								DATE(mc.FechaEsperada) > DATE(NOW()) AND ma.Tipo = pTipoAudiencia)
							AND (pRecordatorios = 0 OR rm.IdRecordatorioMovimiento IS NOT NULL AND rm.Frecuencia != 0 AND DATE(NOW()) <= DATE(mc.FechaEsperada))
							AND (JSON_CONTAINS(pTipos, CONCAT('"', tm.IdTipoMov, '"')) = 1 OR JSON_EXTRACT(pTipos, '$[0]') IS NULL)
							AND (JSON_CONTAINS(pColor, CONCAT('"', mc.Color, '"')) = 1 OR JSON_EXTRACT(pColor, '$[0]') IS NULL)
							AND (mc.Detalle LIKE CONCAT('%', pCadena, '%') OR pCadena = 'dWz6H78mpQ')
							GROUP BY mc.IdMovimientoCaso) a
	LEFT JOIN	UsuariosCaso uc ON a.IdResponsable=uc.IdUsuarioCaso
    LEFT JOIN	Usuarios u ON u.IdUsuario = uc.IdUsuario
	WHERE		JSON_CONTAINS(pUsuarios, CONCAT('"', CONCAT(u.Apellidos, ', ', u.Nombres), '"')) = 1 OR JSON_EXTRACT(pUsuarios, '$[0]') IS NULL
	ORDER BY	CASE 
					WHEN pOrden = 'ESPERADA' THEN COALESCE(a.FechaEsperada, a.FechaEdicion, a.FechaAlta)
				END ASC,
				CASE 
					WHEN pOrden IS NULL OR pOrden = '' THEN COALESCE(a.FechaEsperada, a.FechaEdicion, a.FechaAlta)
					ELSE COALESCE(a.FechaAlta, a.FechaEdicion, a.FechaEsperada)
				END DESC
	LIMIT		pOffset, pLimit;
END $$
DELIMITER ;
