DROP PROCEDURE IF EXISTS `dsp_dame_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_caso`(pIdCaso bigint, pIdEstudio int, pMovs char(1))
BEGIN
	/*
    Permite instanciar un caso desde la base de datos, incluyendo en distintos objetos json a las personas involucradas, los movimientos del caso 
    y los usuarios que tienen acceso al mismo.
    */

    DECLARE pMovimientosCaso, pPersonasCaso, pTelefonos, pUsuariosCaso json;
	DECLARE pIdCasoEstudio bigint;

	SET pIdCasoEstudio = (SELECT IdCasoEstudio FROM IdsCasosEstudio WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio ORDER BY IdCasoEstudio DESC LIMIT 1);

    SET @@group_concat_max_len = 1024 * 1024 * 1024;
    
	IF pMovs = "S" THEN
		SET pMovimientosCaso = (SELECT 		CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
																	'IdMovimientoCaso', mc.IdMovimientoCaso,
																	'IdTipoMov', mc.IdTipoMov,
																	'TipoMovimiento', tm.TipoMovimiento,
																	'IdUsuarioCaso', mc.IdUsuarioCaso,
																	'IdResponsable', mc.IdResponsable,
																	'Detalle', mc.Detalle,
																	'FechaAlta', mc.FechaAlta,
																	'FechaEsperada', mc.FechaEsperada,
																	'FechaRealizado', mc.FechaRealizado,
																	'Cuaderno', mc.Cuaderno,
																	'Color', mc.Color,
																	'IdUsuarioResponsable', u.IdUsuario,
																	'UsuarioResponsable', CONCAT(u.Apellidos, ', ', u.Nombres)
													)),''),']')
								FROM		MovimientosCaso mc
								LEFT JOIN	UsuariosCaso uc ON mc.IdResponsable = uc.IdUsuarioCaso
								LEFT JOIN	Usuarios u ON u.IdUsuario = uc.IdUsuario
								INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
								WHERE		mc.IdCaso = pIdCaso
								ORDER BY 	mc.FechaAlta DESC);
	END IF;
	
    SET pPersonasCaso = (SELECT		CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
																'IdPersona', pc.IdPersona,
																'IdRTC', pc.IdRTC,
																'RolTipoCaso', rtc.Rol,
																'EsPrincipal', pc.EsPrincipal,
																'Observaciones', pc.Observaciones,
																'Tipo', p.Tipo,
																'Nombres', p.Nombres,
																'Apellidos', p.Apellidos,
																'Email', p.Email,
																'Documento', p.Documento,
																'Cuit', p.Cuit,
																'Domicilio', p.Domicilio,
																'FechaAlta', p.FechaAlta,
																'Estado', p.Estado,
																'FechaNacimiento', p.FechaNacimiento,
                                                                'Parametros', pc.ValoresParametros,
																'Telefonos', (
																	SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
																		'Telefono', tp.Telefono,
																		'FechaAlta', tp.FechaAlta,
																		'EsPrincipal', tp.EsPrincipal,
																		'Detalle', tp.Detalle
																	))
																	FROM 	TelefonosPersona tp
																	WHERE 	tp.IdPersona = pc.IdPersona
																),
																'TokenApp', (
																				SELECT	utac.TokenApp
																				FROM	UsuariosTokenAppCliente utac
																				WHERE	utac.Usuario = p.Documento
																			),
																'IdHistoriaClinica', hc.IdHistoriaClinica,
																'EstadoHC', hc.Estado,
																'NumeroHC', hc.Numero,
																'CentroMedicoHC', hc.CentroMedico,
																'FechaEstadoHC', hc.FechaEstadoHC,
																'DocumentacionSolicitada', pc.DocumentacionSolicitada
                                                )),''),']')
						FROM		PersonasCaso pc
                        INNER JOIN	Personas p USING (IdPersona)
                        LEFT JOIN	RolesTipoCaso rtc USING (IdRTC)
						LEFT JOIN	HistoriaClinicaPersonaCaso hc ON hc.IdCaso = pIdCaso AND hc.IdPersona = pc.IdPersona
                        WHERE		pc.IdCaso = pIdCaso);
                        
	UPDATE Casos SET FechaUltVisita = NOW() WHERE IdCaso = pIdCaso;
	
    SELECT 	c.*, pPersonasCaso PersonasCaso, rd.FechaLimite RecDocFecha, rd.Frecuencia RecDocFrec, rd.Activa RecDocActiva,
			pMovimientosCaso MovimientosCaso, cmp.Competencia, o.Origen, j.Juzgado, j.Color ColorJuzgado,
			n.Nominacion, tc.TipoCaso, tc.Color ColorTipoCaso, eag.EstadoAmbitoGestion, cts.IdChat, cts.IdExternoChat, cts.IdPersona IdPersonaChat, cts.Telefono TelefonoChat, me.IdMediacion, pIdCasoEstudio IdCasoEstudio,
			(SELECT FotoCaso FROM FotosCaso WHERE IdCaso = c.IdCaso ORDER BY IdFotoCaso DESC LIMIT 1) FotoCaso, c.Defiende,
			JSON_OBJECT(
				'IdCausaPenalCaso', cp.IdCausaPenalCaso,
				'EstadoCausaPenal', cp.EstadoCausaPenal,
				'FechaEstadoCausaPenal', cp.FechaEstadoCausaPenal,
				'NroExpedienteCausaPenal', cp.NroExpedienteCausaPenal,
				'RadicacionCausaPenal', cp.RadicacionCausaPenal,
				'Comisaria', cp.Comisaria
			) CausaPenal,
			(
				SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
							'Etiqueta', ec.Etiqueta,
							'IdEtiquetaCaso', ec.IdEtiquetaCaso
						))
				FROM	EtiquetasCaso ec
				WHERE	ec.IdCaso = c.IdCaso
			) EtiquetasCaso,
			p.Parametros,
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
					) Comparticiones
    FROM	Casos c
	INNER JOIN Competencias cmp ON cmp.IdCompetencia = c.IdCompetencia
	LEFT JOIN RecordatorioDocumentacion rd ON rd.IdCaso = c.IdCaso
	LEFT JOIN Origenes o ON o.IdOrigen = c.IdOrigen
	LEFT JOIN Juzgados j ON j.IdJuzgado = c.IdJuzgado
	LEFT JOIN Nominaciones n ON n.IdNominacion = c.IdNominacion
    LEFT JOIN TiposCaso tc ON tc.IdTipoCaso = c.IdTipoCaso
    LEFT JOIN EstadoAmbitoGestion eag ON eag.IdEstadoAmbitoGestion = c.IdEstadoAmbitoGestion
	LEFT JOIN Chats cts ON cts.IdCaso = c.IdCaso
	LEFT JOIN Mediaciones me ON me.IdCaso = c.IdCaso
	LEFT JOIN CausaPenalCaso cp ON cp.IdCaso = c.IdCaso
	LEFT JOIN ParametrosCaso p ON p.IdCaso = c.IdCaso
    WHERE	  c.IdCaso = pIdCaso;
                            
END $$
DELIMITER ;
