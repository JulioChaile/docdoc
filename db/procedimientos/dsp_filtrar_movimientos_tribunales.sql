DROP PROCEDURE IF EXISTS `dsp_filtrar_movimientos_tribunales`;
DELIMITER $$
CREATE PROCEDURE `dsp_filtrar_movimientos_tribunales`(pIdEstudio int)
BEGIN
	/*
    Permite buscar movimientos de caso filtrándolos por multiples cadenas de busqueda y por fecha de inicio y fin.
    */
    
    SET @@group_concat_max_len = 1024 * 1024 * 1024;
	set max_heap_table_size = 33554432*2;

    DROP TEMPORARY TABLE IF EXISTS tmp_no_realizados;
    CREATE TEMPORARY TABLE tmp_no_realizados ENGINE = MEMORY
	SELECT 		'P' Tipo, c.*, ch.IdChat, j.Juzgado, j.ModoGestion, n.Nominacion, CAST(CONCAT('[', COALESCE(GROUP_CONCAT(JSON_OBJECT(
									'IdMovimientoCaso', mc.IdMovimientoCaso,
									'IdResponsable', mc.IdResponsable,
									'IdTipoMov', mc.IdTipoMov,
									'TipoMovimiento', tm.TipoMovimiento,
                                    'IdUsuarioResponsable', res.IdUsuario,
									'IdRecordatorioMovimiento', rm.IdRecordatorioMovimiento,
									'Caso', c.Caratula,
									'Expediente', c.NroExpediente,
									'Detalle', mc.Detalle,
									'FechaAlta', mc.FechaAlta,
									'FechaEdicion', mc.FechaEdicion,
									'FechaEsperada', mc.FechaEsperada,
									'FechaRealizado', mc.FechaRealizado, 
									'Cuaderno', mc.Cuaderno,
									'Escrito', mc.Escrito,
									'Color', mc.Color,
									'UsuarioResponsable', CONCAT(res.Apellidos,', ',res.Nombres),
									'UsuarioEdicion', IFNULL(audmc.UsuarioAud, ''),
									'EsperaVencida', IF(mc.FechaEsperada < NOW(), 'S', 'N'),
									'Objetivo', ob.Objetivo,
                                    'IdObjetivo', ob.IdObjetivo,
									'Acciones' , (SELECT JSON_ARRAYAGG(JSON_OBJECT(
														'IdMovimientoAccion', ma.IdMovimientoAccion,
														'Accion', ma.Accion,
														'FechaAccion', ma.FechaAccion,
														'IdUsuario', ma.IdUsuario,
														'Apellidos', uma.Apellidos, 'Nombres', uma.Nombres
													)) FROM MovimientosAcciones ma LEFT JOIN Usuarios uma ON uma.IdUsuario = ma.IdUsuario WHERE ma.IdMovimientoCaso = mc.IdMovimientoCaso GROUP BY ma.IdMovimientoCaso)
								) ORDER BY	mc.FechaEsperada), ''), ']') as json) Movimientos,
				CAST((SELECT CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
											'IdUsuarioCaso', ucc.IdUsuarioCaso,
											'IdUsuario', ucc.IdUsuario,
                                            'IdEstudio', ucc.IdEstudio,
											'Permiso', ucc.Permiso,
                                            'EsCreador', ucc.EsCreador,
                                            'Nombres', uu.Nombres,
                                            'Apellidos', uu.Apellidos,
                                            'Usuario', uu.Usuario,
                                            'Email', uu.Email
										)),''),']') FROM UsuariosCaso ucc INNER JOIN Usuarios uu USING(IdUsuario) WHERE ucc.IdCaso = c.IdCaso
				) as json) UsuariosCaso
	FROM 		Casos c
    INNER JOIN	(SELECT IdCaso FROM UsuariosCaso WHERE IdEstudio = pIdEstudio GROUP BY IdCaso) uc ON uc.IdCaso = c.IdCaso
	INNER JOIN 	MovimientosCaso mc ON mc.IdCaso = c.IdCaso
	LEFT JOIN	RecordatorioMovimiento rm ON rm.IdMovimientoCaso = mc.IdMovimientoCaso
	INNER JOIN	TiposMovimiento tm ON mc.IdTipoMov = tm.IdTipoMov
	INNER JOIN	Juzgados j ON c.IdJuzgado = j.IdJuzgado
	LEFT JOIN 	(SELECT UsuarioAud, amc.IdMovimientoCaso, amc.Id FROM aud_MovimientosCaso amc WHERE amc.Motivo = 'MODIFICAR' AND amc.TipoAud = 'D' ORDER BY Id DESC) audmc ON mc.IdMovimientoCaso = audmc.IdMovimientoCaso
	LEFT JOIN  Chats ch ON c.IdCaso = ch.IdCaso
	LEFT JOIN	MovimientosObjetivo mo ON mo.IdMovimientoCaso = mc.IdMovimientoCaso
	LEFT JOIN	Objetivos ob ON mo.IdObjetivo = ob.IdObjetivo
	LEFT JOIN	UsuariosCaso ucres ON mc.IdResponsable = ucres.IdUsuarioCaso
	LEFT JOIN	Usuarios res ON ucres.IdUsuario = res.IdUsuario
	LEFT JOIN	Origenes o ON c.IdOrigen = o.IdOrigen
	LEFT JOIN	Nominaciones n ON c.IdNominacion = n.IdNominacion
	WHERE 		tm.Categoria = 'P'/* AND uc.IdEstudio = pIdEstudio AND FechaRealizado IS NULL */
	GROUP BY	IdJuzgado, IdCaso, IdChat
	order by 	IdCaso DESC
	LIMIT 500;


	DROP TEMPORARY TABLE IF EXISTS tmp_todos_realizados;
	CREATE TEMPORARY TABLE tmp_todos_realizados ENGINE = MEMORY
    SELECT 		'N' Tipo, c.*, ch.IdChat, j.Juzgado, j.ModoGestion, n.Nominacion, '[]' Movimientos,
				(SELECT CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
											'IdUsuarioCaso', ucc.IdUsuarioCaso,
											'IdUsuario', ucc.IdUsuario,
                                            'IdEstudio', ucc.IdEstudio,
											'Permiso', ucc.Permiso,
                                            'EsCreador', ucc.EsCreador,
                                            'Nombres', uu.Nombres,
                                            'Apellidos', uu.Apellidos,
                                            'Usuario', uu.Usuario,
                                            'Email', uu.Email
										)),''),']') FROM UsuariosCaso ucc INNER JOIN Usuarios uu USING(IdUsuario) WHERE ucc.IdCaso = c.IdCaso
				) UsuariosCaso
	FROM 		Casos c
    INNER JOIN	(SELECT IdCaso FROM UsuariosCaso WHERE IdEstudio = pIdEstudio GROUP BY IdCaso) uc ON c.IdCaso = uc.IdCaso
    INNER JOIN 	MovimientosCaso mc ON mc.IdCaso = c.IdCaso
	INNER JOIN	Juzgados j ON c.IdJuzgado = j.IdJuzgado
	LEFT JOIN  Chats ch ON c.IdCaso = ch.IdCaso
	LEFT JOIN	Nominaciones n ON c.IdNominacion = n.IdNominacion
    /*WHERE		uc.IdEstudio = pIdEstudio*/
    GROUP BY	IdJuzgado, IdCaso, IdChat
    HAVING		SUM(mc.FechaRealizado IS NULL) = 0;

	SELECT * FROM tmp_no_realizados
    UNION 
    SELECT * FROM tmp_todos_realizados;
    
    DROP TEMPORARY TABLE tmp_no_realizados;
    DROP TEMPORARY TABLE tmp_todos_realizados;
END $$
DELIMITER ;
