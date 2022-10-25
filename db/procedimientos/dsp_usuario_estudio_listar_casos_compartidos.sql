DROP PROCEDURE IF EXISTS `dsp_usuario_estudio_listar_casos_compartidos`;
DELIMITER $$
CREATE PROCEDURE `dsp_usuario_estudio_listar_casos_compartidos`(pIdUsuario int)
PROC: BEGIN
	/*
    Permite listar todos los casos compartidos de un usuario perteneciente a un estudio.
    */
	DECLARE pIdEstudio int;
    -- SET @@group_concat_max_len = 1024 * 1024 * 1024;

	SELECT IdEstudio INTO pIdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario;

	DROP TABLE IF EXISTS tmp_comparticiones_casos_enviadas;
	CREATE TEMPORARY TABLE tmp_comparticiones_casos_enviadas ENGINE=MEMORY
		SELECT		c.IdCaso, JSON_ARRAYAGG(JSON_OBJECT(
						'IdComparticion', c.IdComparticion,
						'IdCaso', c.IdCaso,
						'Email', c.Email,
						'Token', c.TokenMensaje,
						'FechaEnviado', c.FechaEnviado,
						'FechaRecibido', c.FechaRecibido,
						'IdUsuarioOrigen', c.IdUsuarioOrigen,
						'IdUsuarioDestino', c.IdUsuarioDestino,
						'IdEstudioDestino', c.IdEstudioDestino,
						'IdEstudioOrigen', c.IdEstudioOrigen
					)) Comparticiones
		FROM		Comparticiones c
		WHERE		c.IdUsuarioOrigen IN (SELECT IdUsuario FROM UsuariosEstudio WHERE IdEstudio = pIdEstudio)
		GROUP BY	c.IdCaso;
	
	DROP TABLE IF EXISTS tmp_comparticiones_casos_recibidas;
	CREATE TEMPORARY TABLE tmp_comparticiones_casos_recibidas ENGINE=MEMORY
		SELECT		c.IdCaso, JSON_ARRAYAGG(JSON_OBJECT(
						'IdComparticion', c.IdComparticion,
						'IdCaso', c.IdCaso,
						'Email', c.Email,
						'FechaEnviado', c.FechaEnviado,
						'FechaRecibido', c.FechaRecibido,
						'IdUsuarioOrigen', c.IdUsuarioOrigen,
						'IdUsuarioDestino', c.IdUsuarioDestino,
						'IdEstudioDestino', c.IdEstudioDestino,
						'IdEstudioOrigen', c.IdEstudioOrigen
					)) Comparticiones
		FROM		Comparticiones c
		WHERE		c.IdEstudioDestino = pIdEstudio
		GROUP BY	c.IdCaso;


	DROP TABLE IF EXISTS tmp_casos_compartidos;
    CREATE TEMPORARY TABLE tmp_casos_compartidos ENGINE=MEMORY
        SELECT      DISTINCT a.IdCaso
        FROM        (SELECT	c.IdCaso
					FROM Casos c
        			INNER JOIN  UsuariosCaso uc USING(IdCaso)
        			INNER JOIN  UsuariosEstudio ue USING(IdUsuario)
        			WHERE       ue.IdUsuario = pIdUsuario
                    			-- El creador fue un usuario de mi estudio
                    			AND EXISTS (SELECT IdUsuario FROM UsuariosCaso WHERE IdEstudio = ue.IdEstudio AND IdCaso = uc.IdCaso AND EsCreador = 'S')
                    			-- Existen usuarios de otros estudios
                    			AND EXISTS (SELECT IdEstudio FROM UsuariosCaso WHERE IdEstudio != ue.IdEstudio AND IdCaso = uc.IdCaso)
					UNION
					SELECT	IdCaso
					FROM	tmp_comparticiones_casos_enviadas
					UNION
					SELECT	IdCaso
					FROM	tmp_comparticiones_casos_recibidas
					) a;
    
    DROP TABLE IF EXISTS tmp_busqueda_casos;
    CREATE TEMPORARY TABLE tmp_busqueda_casos ENGINE=MEMORY
		SELECT		c.IdCaso, (SELECT Estudio FROM Estudios e INNER JOIN UsuariosCaso uc USING (IdEstudio) 
							WHERE IdCaso = c.IdCaso ORDER BY uc.IdUsuarioCaso DESC LIMIT 1) Estudio, 
					c.IdNominacion, c.Caratula, c.NroExpediente, c.Carpeta, c.FechaAlta, ec.IdEstadoCaso, c.Observaciones, n.Nominacion,
					j.Juzgado, ju.Jurisdiccion, ec.EstadoCaso, c.FechaUltVisita, j.IdJuzgado, ju.IdJurisdiccion, c.IdTipoCaso, tc.TipoCaso, 
					c.IdOrigen, o.Origen, c.Estado, c.IdEstadoAmbitoGestion, eag.EstadoAmbitoGestion, c.FechaEstado,
					(SELECT MAX(FechaRealizado) FROM MovimientosCaso WHERE IdCaso = c.IdCaso) FechaUltimoMov,
					(SELECT JSON_ARRAYAGG(JSON_OBJECT(
														'Nombres', p.Nombres,
														'Apellidos', p.Apellidos,
														'IdPersona', p.IdPersona,
														'Documento', p.Documento
														)
										)
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
                                mc.FechaEdicion = (SELECT MAX(mcc.FechaEdicion)
                                                FROM 	MovimientosCaso mcc
                                                WHERE	mcc.IdCaso = c.IdCaso)) UltimoMovimiento
		FROM		Casos c
        INNER JOIN  tmp_casos_compartidos tcc USING(IdCaso)
		INNER JOIN	TiposCaso tc ON tc.IdTipoCaso = c.IdTipoCaso
		INNER JOIN	EstadosCaso ec ON ec.IdEstadoCaso = c.IdEstadoCaso
		INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
    	LEFT JOIN	EstadoAmbitoGestion eag ON eag.IdEstadoAmbitoGestion = c.IdEstadoAmbitoGestion
		INNER JOIN	Juzgados j ON j.IdJuzgado = c.IdJuzgado
		LEFT JOIN	Nominaciones n ON n.IdNominacion = c.IdNominacion
		LEFT JOIN	Jurisdicciones ju ON ju.IdJurisdiccion = j.IdJurisdiccion
		LEFT JOIN	Origenes o ON o.IdOrigen = c.IdOrigen
		WHERE		c.Estado != 'B'
        GROUP BY    IdCaso
		ORDER BY	FechaAlta DESC;
        
	DROP TABLE IF EXISTS tmp_usuarios_casos;
	CREATE TEMPORARY TABLE tmp_usuarios_casos
		SELECT 		tbc.IdCaso, JSON_ARRAYAGG(JSON_OBJECT(
											'IdUsuarioCaso', uc.IdUsuarioCaso,
											'IdUsuario', uc.IdUsuario,
                                            'IdEstudio', uc.IdEstudio,
											'Permiso', uc.Permiso,
                                            'EsCreador', uc.EsCreador,
                                            'Nombres', u.Nombres,
                                            'Apellidos', u.Apellidos,
                                            'Usuario', u.Usuario,
                                            'Email', u.Email
										)) UsuariosCaso
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
                        
	SELECT		*, f_json_slice((SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
											'Caratula', c.Caratula,
                                            'Color', mc.Color,
                                            'Cuaderno', mc.Cuaderno,
											'Detalle', mc.Detalle,
                                            'Escrito', mc.Escrito,
                                            'FechaAlta', mc.FechaAlta,
											'FechaEsperada', mc.FechaEsperada,
											'FechaRealizado', mc.FechaRealizado,
                                            'IdCaso', c.IdCaso,
                                            'IdMovimientoCaso', mc.IdMovimientoCaso,
                                            'IdObjetivo', mo.IdObjetivo,
                                            'IdResponsable', mc.IdResponsable,
                                            'IdTipoMov', mc.IdTipoMov,
                                            'IdUsuarioCaso', mc.IdUsuarioCaso,
                                            'IdUsuarioResponsable', mc.IdResponsable,
                                            'Multimedia', 	(SELECT JSON_ARRAYAGG(JSON_OBJECT('URL', Url, 'Tipo', Tipo)) 
															FROM MultimediaMovimiento 
															INNER JOIN Multimedia USING(IdMultimedia)
															WHERE IdMovimientoCaso=mc.IdMovimientoCaso),
											'Objetivo', (SELECT Objetivo FROM Objetivos WHERE IdObjetivo = mo.IdObjetivo),
                                            'TipoMovimiento', (SELECT TipoMovimiento FROM TiposMovimiento WHERE IdTipoMov = mc.IdTipoMov),
											'UsuarioResponsable', CONCAT(r.Apellidos,', ',r.Nombres)
											)
							)
					FROM		tmp_ultimos_movimientos tm
                    INNER JOIN  MovimientosCaso mc USING(IdMovimientoCaso)
                    LEFT JOIN	MovimientosObjetivo mo USING(IdMovimientoCaso)
                    INNER JOIN  Casos c ON mc.IdCaso = c.IdCaso
                    INNER JOIN  UsuariosCaso uc ON uc.IdUsuarioCaso = mc.IdResponsable
					INNER JOIN	Usuarios r ON r.IdUsuario = uc.IdUsuario
                    WHERE		tm.IdCaso = tbc.IdCaso
                    ORDER BY	mc.FechaAlta DESC), 0, 3) UltimosMovimientos,
				(SELECT Comparticiones FROM tmp_comparticiones_casos_enviadas WHERE IdCaso = tbc.IdCaso) ComparticionesEnviadas,
				(SELECT Comparticiones FROM tmp_comparticiones_casos_recibidas WHERE IdCaso = tbc.IdCaso) ComparticionesRecibidas,
				tuc.UsuariosCaso
    FROM		tmp_busqueda_casos tbc
    INNER JOIN	tmp_usuarios_casos tuc USING(IdCaso);
                    
	DROP TABLE IF EXISTS tmp_comparticiones_casos_enviadas;
	DROP TABLE IF EXISTS tmp_comparticiones_casos_recibidas;
	DROP TABLE IF EXISTS tmp_busqueda_casos;
    DROP TABLE IF EXISTS tmp_usuarios_casos;
    DROP TABLE IF EXISTS tmp_ultimos_movimientos;
    DROP TABLE IF EXISTS tmp_casos_compartidos;
END $$
DELIMITER ;
