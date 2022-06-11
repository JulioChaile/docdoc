DROP procedure IF EXISTS `dsp_buscar_avanzado_casos`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_avanzado_casos`(pIdEstudio int, pIdUsuario int, pIdNominacion int, 
			pIdEstadoCaso int, pFechaInicio date, pFechaFin date, 
            pTipo char(1), pCadena varchar(100), pIdTipoCaso smallint, pEstado char(1))
PROC: BEGIN
	/*
    Permite buscar casos filtrándolos por estudio en pIdEstudio (obligatorio), 
    por abogado en pIdUsuario (opcional. 0 para todos), por nominación en pIdNominacion 
    (opcional. 0 para todas.), por sub estado de caso en pIdSubEstadoCaso (opcional. 0 para todos), 
    por fecha de alta entre pFechaInicio y pFechaFin, por tipo en pTipo = C: TipoCaso - P: PersonasCaso - 
    J: Juzgados - T: Todos los anteriores, por una cadena de búsqueda e indicando si el caso proviene de DocDoc en 
    pEsDocDoc = [S|N|T]. Ordena por FechaAlta.
    */
    DECLARE aux date;

	DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN
		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
	END;

	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;
    
    SET @@group_concat_max_len = 1024 * 1024 * 1024;
    IF pFechaInicio > pFechaFin THEN
		SET aux = pFechaInicio;
        SET	pFechaInicio = pFechaFin;
        SET	pFechaFin = aux;
	END IF;
    
    SET pIdEstudio = COALESCE(pIdEstudio,0);
    SET pIdUsuario = COALESCE(pIdUsuario,0);
    SET pIdNominacion = COALESCE(pIdNominacion,0);
    SET pIdEstadoCaso = COALESCE(pIdEstadoCaso,0);
    SET pIdTipoCaso = COALESCE(pIdTipoCaso,0);
    SET pCadena = COALESCE(pCadena,'');
    
    IF pTipo IS NULL OR pTipo = '' OR pTipo NOT IN ('T','P','C','J') THEN
		SET pTipo = 'T';
	END IF;
    
    IF pEstado IS NULL OR pEstado = '' OR pTipo NOT IN ('T','P','C','J') THEN
		SET pEstado = 'T';
	END IF;
    
    DROP TABLE IF EXISTS tmp_busqueda_casos;
    CREATE TEMPORARY TABLE tmp_busqueda_casos ENGINE=MEMORY
		SELECT		c.IdCaso, (SELECT Estudio FROM Estudios e INNER JOIN UsuariosCaso uc USING (IdEstudio) 
							WHERE IdCaso = c.IdCaso ORDER BY uc.IdUsuarioCaso DESC LIMIT 1) Estudio, 
					c.IdNominacion, c.Caratula, c.NroExpediente, c.Carpeta, c.FechaAlta, ec.IdEstadoCaso, c.Observaciones, c.IdCompetencia, cp.Competencia, n.Nominacion,
					j.Juzgado, ju.Jurisdiccion, ec.EstadoCaso, c.FechaUltVisita, j.IdJuzgado, ju.IdJurisdiccion, c.IdTipoCaso, tc.TipoCaso, 
					c.IdOrigen, o.Origen, c.Estado, c.IdEstadoAmbitoGestion, eag.EstadoAmbitoGestion, c.FechaEstado, cts.IdChat,
					(SELECT MAX(FechaRealizado) FROM MovimientosCaso WHERE IdCaso = c.IdCaso) FechaUltimoMov,
					(SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
									'Nombres', p.Nombres,
									'Apellidos', p.Apellidos,
									'IdPersona', p.IdPersona,
									'Documento', p.Documento,
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
								))
					FROM		PersonasCaso pc
					INNER JOIN	Personas p ON p.IdPersona = pc.IdPersona
					WHERE		pc.IdCaso = c.IdCaso) PersonasCaso,
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
		FROM		Casos c
		INNER JOIN  Competencias cp ON cp.IdCompetencia = c.IdCompetencia
		INNER JOIN	TiposCaso tc ON tc.IdTipoCaso = c.IdTipoCaso
		INNER JOIN	EstadosCaso ec ON ec.IdEstadoCaso = c.IdEstadoCaso
		INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
		INNER JOIN	Juzgados j ON j.IdJuzgado = c.IdJuzgado
		LEFT JOIN	Nominaciones n ON n.IdNominacion = c.IdNominacion
		LEFT JOIN	Jurisdicciones ju ON ju.IdJurisdiccion = j.IdJurisdiccion
		LEFT JOIN	Origenes o ON o.IdOrigen = c.IdOrigen
		LEFT JOIN   PersonasCaso pc ON pc.IdCaso = c.IdCaso
		LEFT JOIN	Personas p ON p.IdPersona = pc.IdPersona
    	LEFT JOIN	EstadoAmbitoGestion eag ON eag.IdEstadoAmbitoGestion = c.IdEstadoAmbitoGestion
		LEFT JOIN	Chats cts ON cts.IdCaso = c.IdCaso
		WHERE		(c.Estado = pEstado OR (pEstado = 'T' AND c.Estado != 'B')) AND 
					(uc.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
					(uc.IdUsuario = pIdUsuario OR pIdUsuario = 0) AND
					(c.IdNominacion = pIdNominacion OR pIdNominacion = 0) AND
					(c.IdEstadoCaso = pIdEstadoCaso OR pIdEstadoCaso = 0) AND
					(c.IdTipoCaso = pIdTipoCaso OR pIdTipoCaso = 0) AND
					(
						(pFechaInicio IS NOT NULL AND pFechaFin IS NOT NULL AND c.FechaAlta BETWEEN pFechaInicio AND CONCAT(pFechaFin,' 23:59:59')) OR
						(pFechaInicio IS NOT NULL AND pFechaFin IS NULL AND c.FechaAlta >= pFechaInicio) OR
						(pFechaInicio IS NULL AND pFechaFin IS NOT NULL AND c.FechaALta <= CONCAT(pFechaFin,' 23:59:59')) OR
						(pFechaInicio IS NULL AND pFechaFin IS NULL)
					) AND
					(
						(pTipo = 'T' AND (
											p.Nombres  LIKE CONCAT('%',pCadena,'%') OR
											p.Apellidos  LIKE CONCAT('%',pCadena,'%') OR
											p.Documento  LIKE CONCAT('%',pCadena,'%')
										 ) OR
										 tc.TipoCaso  LIKE CONCAT('%',pCadena,'%') OR
										 j.Juzgado  LIKE CONCAT('%',pCadena,'%') OR
										 c.Caratula LIKE CONCAT('%',pCadena,'%') OR
										 c.NroExpediente LIKE CONCAT('%',pCadena,'%')
						) OR
						(pTipo = 'P' AND (
											p.Nombres  LIKE CONCAT('%',pCadena,'%') OR
											p.Apellidos  LIKE CONCAT('%',pCadena,'%') OR
											p.Documento  LIKE CONCAT('%',pCadena,'%')
										 )) OR 
						(pTipo = 'C' AND tc.TipoCaso  LIKE CONCAT('%',pCadena,'%')) OR
						(pTipo = 'J' AND j.Juzgado  LIKE CONCAT('%',pCadena,'%'))
					)
		ORDER BY	FechaAlta DESC;
        
	DROP TABLE IF EXISTS tmp_usuarios_casos;
	CREATE TEMPORARY TABLE tmp_usuarios_casos(
		IdCaso int,
        UsuariosCaso json
    )ENGINE=MEMORY;
    INSERT INTO tmp_usuarios_casos
		SELECT 		tbc.IdCaso, CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
											'IdUsuarioCaso', uc.IdUsuarioCaso,
											'IdUsuario', uc.IdUsuario,
                                            'IdEstudio', uc.IdEstudio,
											'Permiso', uc.Permiso,
                                            'EsCreador', uc.EsCreador,
                                            'Nombres', u.Nombres,
                                            'Apellidos', u.Apellidos,
                                            'Usuario', u.Usuario,
                                            'Email', u.Email
										)),''),']') UsuariosCaso
		FROM		tmp_busqueda_casos tbc
		INNER JOIN	UsuariosCaso uc USING(IdCaso)
        INNER JOIN	Usuarios u USING(IdUsuario)
        GROUP BY	tbc.IdCaso;

        
	DROP TABLE IF EXISTS tmp_ultimos_movimientos;
	CREATE TEMPORARY TABLE tmp_ultimos_movimientos
		SELECT 		tbc.IdCaso, mc.IdMovimientoCaso
		FROM		tmp_busqueda_casos tbc
		INNER JOIN	MovimientosCaso mc USING(IdCaso)
        ORDER BY 	mc.FechaAlta DESC;

	DROP TABLE IF EXISTS tmp_json_movimientos;
	CREATE TEMPORARY TABLE tmp_json_movimientos
		SELECT 		DISTINCT c.Caratula, mc.Color, mc.Cuaderno, mc.Detalle, mc.Escrito, mc.FechaAlta, mc.FechaEsperada,
					mc.FechaRealizado, c.IdCaso, mc.IdMovimientoCaso, mc.IdResponsable, mc.IdTipoMov, r.IdUsuario,
					mc.IdUsuarioCaso, mo.IdObjetivo, (SELECT JSON_ARRAYAGG(JSON_OBJECT('URL', Url, 'Tipo', Tipo)) 
									FROM MultimediaMovimiento 
									INNER JOIN Multimedia USING(IdMultimedia)
									WHERE IdMovimientoCaso=mc.IdMovimientoCaso) Multimedia,
					(SELECT Objetivo FROM Objetivos WHERE IdObjetivo = mo.IdObjetivo) Objetivo,
					(SELECT TipoMovimiento FROM TiposMovimiento WHERE IdTipoMov = mc.IdTipoMov) TipoMovimiento,
					CONCAT(r.Apellidos,', ',r.Nombres) UsuarioResponsable
		FROM		tmp_ultimos_movimientos tm
		INNER JOIN  MovimientosCaso mc USING(IdMovimientoCaso)
		LEFT JOIN	MovimientosObjetivo mo USING(IdMovimientoCaso)
		INNER JOIN  Casos c ON tm.IdCaso = c.IdCaso
		INNER JOIN  UsuariosCaso uc ON uc.IdUsuarioCaso = mc.IdResponsable
		INNER JOIN	Usuarios r ON r.IdUsuario = uc.IdUsuario
		ORDER BY	mc.FechaAlta DESC;
                        
	SELECT		DISTINCT *, f_json_slice((SELECT 	CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
											'Caratula', tm.Caratula,
                                            'Color', tm.Color,
                                            'Cuaderno', tm.Cuaderno,
											'Detalle', tm.Detalle,
                                            'Escrito', tm.Escrito,
                                            'FechaAlta', tm.FechaAlta,
											'FechaEsperada', tm.FechaEsperada,
											'FechaRealizado', tm.FechaRealizado,
                                            'IdCaso', tm.IdCaso,
                                            'IdMovimientoCaso', tm.IdMovimientoCaso,
                                            'IdObjetivo', tm.IdObjetivo,
                                            'IdResponsable', tm.IdResponsable,
                                            'IdTipoMov', tm.IdTipoMov,
											'IdUsuario', tm.IdUsuario,
                                            'IdUsuarioCaso', tm.IdUsuarioCaso,
                                            'IdUsuarioResponsable', tm.IdResponsable,
                                            'Multimedia', tm.Multimedia,
											'Objetivo', tm.Objetivo,
                                            'TipoMovimiento', tm.TipoMovimiento,
											'UsuarioResponsable', tm.UsuarioResponsable
											)
							),''),']')
					FROM		tmp_json_movimientos tm
                    WHERE		tm.IdCaso = tbc.IdCaso
                    ORDER BY	tm.FechaAlta DESC), 0, 3) UltimosMovimientos
    FROM		tmp_busqueda_casos tbc
    INNER JOIN	tmp_usuarios_casos tuc USING(IdCaso);

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
                    
	DROP TABLE IF EXISTS tmp_busqueda_casos;
    DROP TABLE IF EXISTS tmp_usuarios_casos;
    DROP TABLE IF EXISTS tmp_ultimos_movimientos;
	DROP TABLE IF EXISTS tmp_json_movimientos;
END$$

DELIMITER ;

