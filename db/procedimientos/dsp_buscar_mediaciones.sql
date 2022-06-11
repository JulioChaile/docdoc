DROP PROCEDURE IF EXISTS `dsp_buscar_mediaciones`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_mediaciones`(pIdEstudio int, pIdUsuario int, pCadena varchar(100), pOffset int, pCausaPenal int)
PROC: BEGIN
	DECLARE pLimit int;

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
	SET pLimit = 50;
    
    SELECT DISTINCT	c.Caratula, c.IdCaso, c.IdEstadoAmbitoGestion, eag.EstadoAmbitoGestion, jea.Orden OrdenEstado, c.FechaEstado, c.FechaAlta, ch.IdChat,
                    m.IdMediacion, m.IdMediador, m.IdBono, m.IdBeneficio, m.FechaBonos, m.FechaPresentado, m.FechaProximaAudiencia, m.Legajo, bo.Bono,
                    bo.Color ColorBono, be.Beneficio, me.Nombre NombreMediador, cp.IdCausaPenalCaso, cp.EstadoCausaPenal, cp.NroExpedienteCausaPenal,
                    cp.RadicacionCausaPenal, cp.Comisaria, cp.FechaEstadoCausaPenal, pc.Parametros, c.IdTipoCaso, tc.TipoCaso, c.IdOrigen, o.Origen,
                    m.IdEstadoBeneficio, ebe.EstadoBeneficio, c.IdNominacion, nom.Nominacion, c.IdJuzgado, juz.Juzgado,
                    (SELECT 	JSON_ARRAYAGG(JSON_OBJECT(
                                    'Nombres', p.Nombres,
                                    'Apellidos', p.Apellidos,
                                    'IdPersona', p.IdPersona,
                                    'Documento', p.Documento,
                                    'EsPrincipal', pc.EsPrincipal,
                                    'Parametros', pc.ValoresParametros,
                                    'Observaciones', pc.Observaciones,
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
                                                ),
                                    'IdHistoriaClinica', hc.IdHistoriaClinica,
                                    'EstadoHC', hc.Estado,
                                    'NumeroHC', hc.Numero,
                                    'CentroMedicoHC', hc.CentroMedico,
                                    'FechaEstadoHC', hc.FechaEstadoHC
                                ))
                    FROM		PersonasCaso pc
                    INNER JOIN	Personas p USING(IdPersona)
					LEFT JOIN	HistoriaClinicaPersonaCaso hc USING (IdCaso, IdPersona)
                    WHERE		pc.IdCaso = c.IdCaso) PersonasCaso,
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
                                                WHERE	mcc.IdCaso = m.IdCaso) AND
                                mc.IdMovimientoCaso = 	(SELECT MAX(mccc.IdMovimientocaso)
                                                        FROM	MovimientosCaso mccc
                                                        WHERE	mccc.IdCaso = c.IdCaso)) UltimoMovimiento
    FROM			Casos c
	INNER JOIN      TiposCaso tc ON tc.IdTipoCaso = c.IdTipoCaso
	LEFT JOIN       Origenes o ON o.IdOrigen = c.IdOrigen
    LEFT JOIN       Nominaciones nom ON nom.IdNominacion = c.IdNominacion
    LEFT JOIN		Mediaciones m USING(IdCaso)
    LEFT JOIN		UsuariosCaso uc USING(IdCaso)
    LEFT JOIN		BonosMediacion bo USING(IdBono)
    LEFT JOIN		BeneficiosMediacion be USING(IdBeneficio)
    LEFT JOIN       EstadosBeneficioMediacion ebe USING(IdEstadoBeneficio)
    LEFT JOIN		Mediadores me USING(IdMediador)
    LEFT JOIN		Chats ch USING(IdCaso)
    LEFT JOIN       EstadoAmbitoGestion eag USING(IdEstadoAmbitoGestion)
    LEFT JOIN       JuzgadosEstadosAmbitos jea ON (jea.IdEstadoAmbitoGestion = eag.IdEstadoAmbitoGestion AND jea.IdJuzgado = c.IdJuzgado)
	LEFT JOIN       CausaPenalCaso cp USING(IdCaso)
	LEFT JOIN       ParametrosCaso pc USING(IdCaso)
    LEFT JOIN       Juzgados juz ON juz.IdJuzgado = c.IdJuzgado
    WHERE		    (uc.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
				    (uc.IdUsuario = pIdUsuario OR pIdUsuario = 0) AND
                    (
                        (c.Estado = 'A' AND c.IdJuzgado = 8 AND pCausaPenal = 0) OR
                        (c.Estado = 'A' AND cp.IdCausaPenalCaso IS NOT NULL AND pCausaPenal = 1) OR
                        (c.Estado = 'P' AND cp.IdCausaPenalCaso IS NOT NULL)
                    )
    ORDER BY		c.FechaAlta DESC;
END $$
DELIMITER ;
