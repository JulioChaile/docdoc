DROP PROCEDURE IF EXISTS `dsp_buscar_casos_judiciales`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_casos_judiciales`(pIdEstudio int, pIdUsuario int, pIdEstadoAmbitoGestion int)
PROC: BEGIN    
    	SELECT DISTINCT c.IdCaso, c.Caratula, c.NroExpediente, c.FechaAlta, 
					j.Juzgado, j.IdJuzgado, 
					 c.IdEstadoAmbitoGestion, eag.EstadoAmbitoGestion, c.FechaEstado, jeag.Orden,
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
                                                WHERE	mcc.IdCaso = c.IdCaso)
                                                LIMIT 1) UltimoMovimientoEditado
		FROM		Casos c
		INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
		LEFT JOIN	Juzgados j ON j.IdJuzgado = c.IdJuzgado
    	LEFT JOIN	EstadoAmbitoGestion eag ON eag.IdEstadoAmbitoGestion = c.IdEstadoAmbitoGestion
		LEFT JOIN	JuzgadosEstadosAmbitos jeag ON jeag.IdEstadoAmbitoGestion = eag.IdEstadoAmbitoGestion AND jeag.IdJuzgado = j.IdJuzgado
		WHERE		c.Estado NOT IN ('B', 'P', 'F', 'R', 'E') AND
                    uc.IdEstudio = pIdEstudio AND
                    uc.IdUsuario = pIdUsuario AND
					c.IdJuzgado IN (1, 6, 7, 11) AND
					c.IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion
		GROUP BY	c.IdCaso
		ORDER BY	c.FechaAlta;
END $$
DELIMITER ;
