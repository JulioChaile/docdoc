DROP PROCEDURE IF EXISTS `dsp_buscar_casos_pendientes`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_casos_pendientes`(pCadena varchar(50), pIdEstudio int, pDocumento varchar(100), pTelefono varchar(100), pOffset int, pLimit int, pVisitado char(1),
                                                pEstados json, pFechasAlta json, pFechasVisitado json, pCadete int, pFinalizado varchar(1))
BEGIN
	/*
    Permite buscar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    DECLARE pCant int;
    DECLARE pCantEstados, pCantFechasAlta, pCantFechasVisitado json;

    SET pCadena = COALESCE(pCadena,'');
    SET pDocumento = COALESCE(pDocumento,'');
    SET pTelefono = COALESCE(pTelefono,'');

	IF pOffset IS NULL OR pOffset = '' THEN
		SET pOffset = 0;
	END IF;
	IF pLimit IS NULL OR pLimit = '' THEN
		SET pLimit = 30;
	END IF;
	IF pCadete IS NULL OR pCadete = '' THEN
		SET pCadete = 0;
	END IF;

    SET pCantEstados =  (SELECT 	JSON_OBJECTAGG(EstadoCasoPendiente, Cant)
                        FROM 	(SELECT ecp.EstadoCasoPendiente, COUNT(cp.IdEstadoCasoPendiente) Cant
                                FROM CasosPendientes cp
                                LEFT JOIN EstadosCasoPendiente ecp USING(IdEstadoCasoPendiente)
                                WHERE(
                                        cp.Nombres LIKE CONCAT('%',pCadena,'%') OR
                                        cp.Domicilio LIKE CONCAT('%',pCadena,'%') OR
                                        cp.Apellidos LIKE CONCAT('%',pCadena,'%') OR
                                        CONCAT(cp.Nombres, ' ', cp.Apellidos) LIKE CONCAT('%',pCadena,'%') OR
                                        CONCAT(cp.Apellidos, ' ', cp.Nombres) LIKE CONCAT('%',pCadena,'%')
                                    ) AND
                                    cp.Documento LIKE CONCAT('%', pDocumento, '%') AND
                                    cp.Telefono LIKE CONCAT('%', pTelefono, '%') AND
                                    (cp.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                                    (cp.IdEstadoCasoPendiente != 36 OR pFinalizado = 'S') AND
                                    (
                                        pVisitado = 'S' AND (cp.Visitado = 1 OR cp.Visitado = '1') OR
                                        pVisitado = 'N' AND (cp.Visitado != 1 OR cp.Visitado != '1' OR cp.Visitado IS NULL) OR
                                        pVisitado = '' OR
                                        pVisitado IS NULL
                                    ) AND
                                    (cp.IdUsuarioVisita = pCadete OR pCadete = 0) AND
                                    (JSON_CONTAINS(pEstados, CONCAT('"', ecp.EstadoCasoPendiente, '"')) = 1 OR JSON_EXTRACT(pEstados, '$[0]') IS NULL) AND
                                    (JSON_CONTAINS(pFechasAlta, CONCAT('"', DATE(cp.FechaAlta), '"')) = 1 OR JSON_EXTRACT(pFechasAlta, '$[0]') IS NULL) AND
                                    (JSON_CONTAINS(pFechasVisitado, CONCAT('"', cp.FechaVisitado, '"')) = 1 OR JSON_EXTRACT(pFechasVisitado, '$[0]') IS NULL)
                                GROUP BY cp.IdEstadoCasoPendiente) a);

    SET pCantFechasVisitado =   (SELECT 	JSON_OBJECTAGG(FechaVisitado, Cant)
                                FROM	(SELECT cp.FechaVisitado, COUNT(cp.FechaVisitado) Cant
                                        FROM	CasosPendientes cp
                                        LEFT JOIN   EstadosCasoPendiente ecp USING(IdEstadoCasoPendiente)
                                        WHERE	cp.FechaVisitado IS NOT NULL AND
                                                (
                                                    cp.Domicilio LIKE CONCAT('%',pCadena,'%') OR
                                                    cp.Nombres LIKE CONCAT('%',pCadena,'%') OR
                                                    cp.Apellidos LIKE CONCAT('%',pCadena,'%') OR
                                                    CONCAT(cp.Nombres, ' ', cp.Apellidos) LIKE CONCAT('%',pCadena,'%') OR
                                                    CONCAT(cp.Apellidos, ' ', cp.Nombres) LIKE CONCAT('%',pCadena,'%')
                                                ) AND
                                                cp.Documento LIKE CONCAT('%', pDocumento, '%') AND
                                                cp.Telefono LIKE CONCAT('%', pTelefono, '%') AND
                                                (cp.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                                                (cp.IdEstadoCasoPendiente != 36 OR pFinalizado = 'S') AND
                                                (
                                                    pVisitado = 'S' AND (cp.Visitado = 1 OR cp.Visitado = '1') OR
                                                    pVisitado = 'N' AND (cp.Visitado != 1 OR cp.Visitado != '1' OR cp.Visitado IS NULL) OR
                                                    pVisitado = '' OR
                                                    pVisitado IS NULL
                                                ) AND
                                                (cp.IdUsuarioVisita = pCadete OR pCadete = 0) AND
                                                (JSON_CONTAINS(pEstados, CONCAT('"', ecp.EstadoCasoPendiente, '"')) = 1 OR JSON_EXTRACT(pEstados, '$[0]') IS NULL) AND
                                                (JSON_CONTAINS(pFechasAlta, CONCAT('"', DATE(cp.FechaAlta), '"')) = 1 OR JSON_EXTRACT(pFechasAlta, '$[0]') IS NULL) AND
                                                (JSON_CONTAINS(pFechasVisitado, CONCAT('"', cp.FechaVisitado, '"')) = 1 OR JSON_EXTRACT(pFechasVisitado, '$[0]') IS NULL)
                                        GROUP BY cp.FechaVisitado) a);

    SET pCantFechasAlta =   (SELECT 	JSON_OBJECTAGG(FechaAlta, Cant)
                            FROM	(SELECT fcp.FechaAlta, COUNT(fcp.FechaAlta) Cant
                                    FROM	(
                                                SELECT DATE(FechaAlta) FechaAlta
                                                FROM CasosPendientes cp
                                                LEFT JOIN   EstadosCasoPendiente ecp USING(IdEstadoCasoPendiente)
                                                WHERE(
                                                    cp.Domicilio LIKE CONCAT('%',pCadena,'%') OR
                                                    cp.Nombres LIKE CONCAT('%',pCadena,'%') OR
                                                    cp.Apellidos LIKE CONCAT('%',pCadena,'%') OR
                                                    CONCAT(cp.Nombres, ' ', cp.Apellidos) LIKE CONCAT('%',pCadena,'%') OR
                                                    CONCAT(cp.Apellidos, ' ', cp.Nombres) LIKE CONCAT('%',pCadena,'%')
                                                ) AND
                                                cp.Documento LIKE CONCAT('%', pDocumento, '%') AND
                                                cp.Telefono LIKE CONCAT('%', pTelefono, '%') AND
                                                (cp.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                                                (cp.IdEstadoCasoPendiente != 36 OR pFinalizado = 'S') AND
                                                (
                                                    pVisitado = 'S' AND (cp.Visitado = 1 OR cp.Visitado = '1') OR
                                                    pVisitado = 'N' AND (cp.Visitado != 1 OR cp.Visitado != '1' OR cp.Visitado IS NULL) OR
                                                    pVisitado = '' OR
                                                    pVisitado IS NULL
                                                ) AND
                                                (cp.IdUsuarioVisita = pCadete OR pCadete = 0) AND
                                                (JSON_CONTAINS(pEstados, CONCAT('"', ecp.EstadoCasoPendiente, '"')) = 1 OR JSON_EXTRACT(pEstados, '$[0]') IS NULL) AND
                                                (JSON_CONTAINS(pFechasAlta, CONCAT('"', DATE(cp.FechaAlta), '"')) = 1 OR JSON_EXTRACT(pFechasAlta, '$[0]') IS NULL) AND
                                                (JSON_CONTAINS(pFechasVisitado, CONCAT('"', cp.FechaVisitado, '"')) = 1 OR JSON_EXTRACT(pFechasVisitado, '$[0]') IS NULL)
                                            ) fcp
                                    GROUP BY fcp.FechaAlta) a);

    SET pCant = (SELECT COUNT(*) FROM CasosPendientes cp
                LEFT JOIN   EstadosCasoPendiente ecp USING(IdEstadoCasoPendiente)
                WHERE(
                    cp.Domicilio LIKE CONCAT('%',pCadena,'%') OR
                    cp.Nombres LIKE CONCAT('%',pCadena,'%') OR
                    cp.Apellidos LIKE CONCAT('%',pCadena,'%') OR
                    CONCAT(cp.Nombres, ' ', cp.Apellidos) LIKE CONCAT('%',pCadena,'%') OR
                    CONCAT(cp.Apellidos, ' ', cp.Nombres) LIKE CONCAT('%',pCadena,'%')
                ) AND
                cp.Documento LIKE CONCAT('%', pDocumento, '%') AND
                cp.Telefono LIKE CONCAT('%', pTelefono, '%') AND
                (cp.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                (cp.IdEstadoCasoPendiente != 36 OR pFinalizado = 'S') AND
                (
                    pVisitado = 'S' AND (cp.Visitado = 1 OR cp.Visitado = '1') OR
                    pVisitado = 'N' AND (cp.Visitado != 1 OR cp.Visitado != '1' OR cp.Visitado IS NULL) OR
                    pVisitado = '' OR
                    pVisitado IS NULL
                ) AND
                (cp.IdUsuarioVisita = pCadete OR pCadete = 0) AND
                (JSON_CONTAINS(pEstados, CONCAT('"', ecp.EstadoCasoPendiente, '"')) = 1 OR JSON_EXTRACT(pEstados, '$[0]') IS NULL) AND
                (JSON_CONTAINS(pFechasAlta, CONCAT('"', DATE(cp.FechaAlta), '"')) = 1 OR JSON_EXTRACT(pFechasAlta, '$[0]') IS NULL) AND
                (JSON_CONTAINS(pFechasVisitado, CONCAT('"', cp.FechaVisitado, '"')) = 1 OR JSON_EXTRACT(pFechasVisitado, '$[0]') IS NULL));
    
    SELECT DISTINCT	cp.*, c.Caratula, ct.IdChat, ecp.EstadoCasoPendiente, o.Origen, pCant Cant, (SELECT		JSON_OBJECT(
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
                                                                        WHERE		mc.IdCaso = cp.IdCaso AND
                                                                                    mc.FechaAlta = (SELECT MAX(mcc.FechaAlta)
                                                                                                    FROM 	MovimientosCaso mcc
                                                                                                    WHERE	mcc.IdCaso = cp.IdCaso) AND
                                                                                    mc.IdMovimientoCaso = 	(SELECT MAX(mccc.IdMovimientocaso)
                                                                                                            FROM	MovimientosCaso mccc
                                                                                                            WHERE	mccc.IdCaso = cp.IdCaso)) UltimoMovimiento,
					JSON_ARRAYAGG(JSON_OBJECT(
									'Nombres', p.Nombres,
									'Apellidos', p.Apellidos,
									'IdPersona', p.IdPersona,
									'Documento', p.Documento,
									'EsPrincipal', pc.EsPrincipal,
									'Observaciones', pc.Observaciones,
									'Parametros', pc.ValoresParametros,
									'DocumentacionSolicitada', pc.DocumentacionSolicitada,
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
                pCantFechasAlta CantFechasAlta, pCantFechasVisitado CantFechasVisitado, pCantEstados CantEstados, cp.IdUsuarioVisita, CONCAT(u.Apellidos, ', ', u.Nombres) UsuarioVisita
    FROM		CasosPendientes cp
    LEFT JOIN   EstadosCasoPendiente ecp USING(IdEstadoCasoPendiente)
    LEFT JOIN   Origenes o USING(IdOrigen)
    LEFT JOIN   Casos c USING(IdCaso)
    LEFT JOIN   Chats ct USING(IdCaso)
    LEFT JOIN   Usuarios u ON u.IdUsuario = cp.IdUsuarioVisita
    LEFT JOIN   PersonasCaso pc ON pc.IdCaso = cp.IdCaso
    LEFT JOIN	Personas p ON p.IdPersona = pc.IdPersona
    LEFT JOIN	TelefonosPersona tp ON tp.IdPersona = p.IdPersona
    WHERE		(
                    cp.Domicilio LIKE CONCAT('%',pCadena,'%') OR
                    cp.Nombres LIKE CONCAT('%',pCadena,'%') OR
                    cp.Apellidos LIKE CONCAT('%',pCadena,'%') OR
                    CONCAT(cp.Nombres, ' ', cp.Apellidos) LIKE CONCAT('%',pCadena,'%') OR
                    CONCAT(cp.Apellidos, ' ', cp.Nombres) LIKE CONCAT('%',pCadena,'%')
                ) AND
                cp.Documento LIKE CONCAT('%', pDocumento, '%') AND
                cp.Telefono LIKE CONCAT('%', pTelefono, '%') AND
                (cp.IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                (cp.IdEstadoCasoPendiente != 36 OR pFinalizado = 'S') AND
                (
                    pVisitado = 'S' AND (cp.Visitado = 1 OR cp.Visitado = '1') OR
                    pVisitado = 'N' AND (cp.Visitado != 1 OR cp.Visitado != '1' OR cp.Visitado IS NULL) OR
                    pVisitado = '' OR
                    pVisitado IS NULL
                ) AND
                (cp.IdUsuarioVisita = pCadete OR pCadete = 0) AND
                (JSON_CONTAINS(pEstados, CONCAT('"', ecp.EstadoCasoPendiente, '"')) = 1 OR JSON_EXTRACT(pEstados, '$[0]') IS NULL) AND
                (JSON_CONTAINS(pFechasAlta, CONCAT('"', DATE(cp.FechaAlta), '"')) = 1 OR JSON_EXTRACT(pFechasAlta, '$[0]') IS NULL) AND
                (JSON_CONTAINS(pFechasVisitado, CONCAT('"', cp.FechaVisitado, '"')) = 1 OR JSON_EXTRACT(pFechasVisitado, '$[0]') IS NULL)
    GROUP BY cp.IdCaso
    ORDER BY    cp.FechaAlta DESC
    LIMIT       pOffset, pLimit;
END $$
DELIMITER ;
