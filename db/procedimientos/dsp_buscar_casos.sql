DROP PROCEDURE IF EXISTS `dsp_buscar_casos`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_casos`(pIdEstudio int, pIdUsuario int, pTipo char(1), pCadena varchar(100), pOffset int, pOrden varchar(50), pLimit int)
PROC: BEGIN
    IF pTipo IS NULL OR pTipo = '' OR pTipo NOT IN ('T','P','E','C') THEN
		SET pTipo = 'T';
	END IF;
    IF pIdEstudio IS NULL OR pIdEstudio = '' THEN
		SET pIdEstudio = 0;
	END IF;
    IF pIdUsuario IS NULL OR pIdUsuario = '' THEN
		SET pIdUsuario = 0;
	END IF;
	IF pOffset IS NULL OR pOffset = '' THEN
		SET pOffset = 0;
	END IF;

    SET pCadena = COALESCE(pCadena,'');
    SET pOrden = COALESCE(pOrden,'fecha');
    
    SELECT DISTINCT c.IdCaso, c.IdNominacion, c.Caratula, c.NroExpediente, c.FechaAlta, ec.IdEstadoCaso, c.IdCompetencia, cp.Competencia, n.Nominacion,
					j.Juzgado, j.Color ColorJuzgado, ec.EstadoCaso, j.IdJuzgado, c.IdTipoCaso, tc.TipoCaso, tc.Color ColorTipoCaso, c.Defiende, c.Duplicado,
					c.IdOrigen, o.Origen, IF(c.Estado = 'E', 'R', c.Estado) Estado, c.IdEstadoAmbitoGestion, eag.EstadoAmbitoGestion, jeag.Orden, c.FechaEstado, cts.IdChat, cts.IdExternoChat,
					JSON_ARRAYAGG(JSON_OBJECT(
						'Etiqueta', ecc.Etiqueta,
						'IdEtiquetaCaso', ecc.IdEtiquetaCaso
					)) EtiquetasCaso,
					(SELECT  	JSON_ARRAYAGG(e.Estudio)
					FROM		Estudios e
					INNER JOIN	UsuariosCaso uc USING(IdEstudio)
					WHERE		uc.IdCaso = c.IdCaso) Estudios,
					JSON_ARRAYAGG(JSON_OBJECT(
									'Nombres', p.Nombres,
									'Apellidos', p.Apellidos,
									'IdPersona', p.IdPersona,
									'Documento', p.Documento,
									'EsPrincipal', pc.EsPrincipal,
									'Observaciones', pc.Observaciones,
									'Parametros', pc.ValoresParametros,
									'DocumentacionSolicitada', pc.DocumentacionSolicitada,
									'TokenApp', utac.TokenApp,
									'Telefonos', (
													SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
														'Telefono', tp.Telefono,
														'FechaAlta', tp.FechaAlta,
														'EsPrincipal', tp.EsPrincipal,
														'Detalle', tp.Detalle
													))
													FROM 	TelefonosPersona tp
													WHERE 	tp.IdPersona = pc.IdPersona
															AND tp.Telefono IS NOT NULL
															AND TRIM(tp.Telefono) != ''
															AND tp.Telefono != 'null'
												)
								)) PersonasCaso,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT(
													'EstudioOrigen', (SELECT Estudio FROM Estudios WHERE IdEstudio = eo.IdEstudioOrigen),
													'IdEstudioOrigen', eo.IdEstudioOrigen,
													'EstudiosDestino', (
																			SELECT JSON_ARRAYAGG(JSON_OBJECT(
																											'Estudio', ed.Estudio,
																											'IdEstudio', ed.IdEstudio
																			))
																			FROM (
																				SELECT DISTINCT e.Estudio, e.IdEstudio
																				FROM Comparticiones compp
																				INNER JOIN Estudios e ON e.IdEstudio = compp.IdEstudioDestino
																				WHERE compp.IdCaso = c.IdCaso AND compp.IdEstudioOrigen = eo.IdEstudioOrigen
																				) ed
													)
										))
					FROM (
						SELECT DISTINCT comp.IdEstudioOrigen
						FROM Comparticiones comp
						WHERE comp.IdCaso = c.IdCaso AND (comp.IdEstudioOrigen = pIdEstudio OR comp.IdEstudioDestino = pIdEstudio OR pIdEstudio = 0)
						) eo
					) Comparticiones,
					(SELECT		JSON_OBJECT(
                                    'IdMovimientoCaso', mc.IdMovimientoCaso,
                                    'IdCaso', mc.IdCaso,
                                    'IdTipoMov', mc.IdTipoMov,
                                    'TipoMovimiento', tm.TipoMovimiento,
                                    'IdUsuarioCaso', mc.IdUsuarioCaso,
                                    'IdResponsable', mc.IdResponsable,
                                    'UsuarioResponsable', CONCAT(ur.Apellidos,', ',ur.Nombres),
                                    'IdUsuarioResponsable', ur.IdUsuario,
                                    'Detalle', mc.Detalle,
                                    'FechaAlta', mc.FechaAlta,
                                    'FechaEdicion', mc.FechaEdicion,
                                    'FechaEsperada', mc.FechaEsperada,
                                    'FechaRealizado', mc.FechaRealizado,
                                    'Cuaderno', mc.Cuaderno,
                                    'Escrito', mc.Escrito,
                                    'Color', mc.Color
                                )
                    FROM		MovimientosCaso mc
                    INNER JOIN	TiposMovimiento tm USING(IdTipoMov)
                    LEFT JOIN	UsuariosCaso uc ON uc.IdUsuarioCaso = mc.IdResponsable
                    LEFT JOIN	Usuarios ur USING(IdUsuario)
                    WHERE		mc.IdCaso = c.IdCaso AND
                                mc.FechaAlta = (SELECT MAX(mcc.FechaAlta)
                                                FROM 	MovimientosCaso mcc
                                                WHERE	mcc.IdCaso = c.IdCaso) AND
                                mc.IdMovimientoCaso = 	(SELECT MAX(mccc.IdMovimientocaso)
                                                        FROM	MovimientosCaso mccc
                                                        WHERE	mccc.IdCaso = c.IdCaso)) UltimoMovimiento,
					(SELECT		JSON_OBJECT(
                                    'IdMovimientoCaso', mc.IdMovimientoCaso,
                                    'IdCaso', mc.IdCaso,
                                    'IdTipoMov', mc.IdTipoMov,
                                    'TipoMovimiento', tm.TipoMovimiento,
                                    'IdUsuarioCaso', mc.IdUsuarioCaso,
                                    'IdResponsable', mc.IdResponsable,
                                    'UsuarioResponsable', CONCAT(ur.Apellidos,', ',ur.Nombres),
                                    'IdUsuarioResponsable', ur.IdUsuario,
                                    'Detalle', mc.Detalle,
                                    'FechaAlta', mc.FechaAlta,
                                    'FechaEdicion', mc.FechaEdicion,
                                    'FechaEsperada', mc.FechaEsperada,
                                    'FechaRealizado', mc.FechaRealizado,
                                    'Cuaderno', mc.Cuaderno,
                                    'Escrito', mc.Escrito,
                                    'Color', mc.Color
                                )
                    FROM		MovimientosCaso mc
                    INNER JOIN	TiposMovimiento tm USING(IdTipoMov)
                    LEFT JOIN	UsuariosCaso uc ON uc.IdUsuarioCaso = mc.IdResponsable
                    LEFT JOIN	Usuarios ur USING(IdUsuario)
                    WHERE		mc.IdCaso = c.IdCaso AND
                                mc.FechaEdicion = (SELECT MAX(mcc.FechaEdicion)
                                                FROM 	MovimientosCaso mcc
                                                WHERE	mcc.IdCaso = c.IdCaso) AND
                                mc.IdMovimientoCaso = 	(SELECT MAX(mccc.IdMovimientocaso)
                                                        FROM	MovimientosCaso mccc
                                                        WHERE	mccc.IdCaso = c.IdCaso)) UltimoMovimientoEditado,
					(SELECT MAX(mchat.FechaEnviado) FROM Mensajes mchat WHERE mchat.IdUsuario IS NOT NULL AND mchat.IdChat = cts.IdChat) FechaUltMsj,
					idce.IdCasoEstudio
		FROM		Casos c
		INNER JOIN  Competencias cp ON cp.IdCompetencia = c.IdCompetencia
		INNER JOIN	TiposCaso tc ON tc.IdTipoCaso = c.IdTipoCaso
		INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
		LEFT JOIN	IdsCasosEstudio idce ON idce.IdCaso = c.IdCaso AND idce.IdEstudio = pIdEstudio
		LEFT JOIN	EtiquetasCaso ecc ON ecc.IdCaso = c.IdCaso
		LEFT JOIN	EstadosCaso ec ON ec.IdEstadoCaso = c.IdEstadoCaso
		LEFT JOIN	Juzgados j ON j.IdJuzgado = c.IdJuzgado
		LEFT JOIN	Nominaciones n ON n.IdNominacion = c.IdNominacion
		LEFT JOIN	Origenes o ON o.IdOrigen = c.IdOrigen
		LEFT JOIN   PersonasCaso pc ON pc.IdCaso = c.IdCaso
		LEFT JOIN	Personas p ON p.IdPersona = pc.IdPersona
		LEFT JOIN	Usuarios u ON u.Usuario = p.Documento
		LEFT JOIN	UsuariosTokenAppCliente utac ON utac.IdUsuario = u.IdUsuario 
		LEFT JOIN	TelefonosPersona tp ON tp.IdPersona = p.IdPersona
    	LEFT JOIN	EstadoAmbitoGestion eag ON eag.IdEstadoAmbitoGestion = c.IdEstadoAmbitoGestion
		LEFT JOIN	JuzgadosEstadosAmbitos jeag ON jeag.IdEstadoAmbitoGestion = eag.IdEstadoAmbitoGestion AND jeag.IdJuzgado = j.IdJuzgado
		LEFT JOIN	Chats cts ON cts.IdCaso = c.IdCaso
		WHERE		c.Estado NOT IN ('B', 'P', 'F') AND
                    (uc.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
					(uc.IdUsuario = pIdUsuario OR pIdUsuario = 0) AND
					(
						(pTipo = 'T' AND (
											p.Nombres  LIKE CONCAT('%',pCadena,'%') OR
											p.Apellidos  LIKE CONCAT('%',pCadena,'%') OR
											p.Documento  LIKE CONCAT('%',pCadena,'%') OR
											tp.Telefono  LIKE CONCAT('%',pCadena,'%') OR
										    REPLACE(c.Caratula, ',', '') LIKE CONCAT('%',pCadena,'%') OR
										    c.NroExpediente LIKE CONCAT('%',pCadena,'%')
                                        )
						) OR
						(pTipo = 'P' AND (
											p.Nombres  LIKE CONCAT('%',pCadena,'%') OR
											p.Apellidos  LIKE CONCAT('%',pCadena,'%') OR
											p.Documento  LIKE CONCAT('%',pCadena,'%') OR
											tp.Telefono  LIKE CONCAT('%',pCadena,'%') OR
										    REPLACE(c.Caratula, ',', '') LIKE CONCAT('%',pCadena,'%')
										 )
                        ) OR 
						(pTipo = 'C' AND (
											p.Documento = pCadena AND
											pc.Observaciones = 'Actor'
										 )
                        ) OR 
						(pTipo = 'E' AND (c.NroExpediente LIKE CONCAT('%',pCadena,'%') OR pCadena = ''))
					)
		GROUP BY	c.IdCaso, cts.IdChat, idce.IdCasoEstudio
		ORDER BY	CASE pOrden WHEN 'fecha' THEN c.FechaAlta END DESC,
					CASE pOrden WHEN 'alf' THEN LOWER(c.Caratula) END ASC
		LIMIT		pOffset, pLimit;
END $$
DELIMITER ;
