DROP PROCEDURE IF EXISTS `dsp_movimientos_del_dia`;
DELIMITER $$
CREATE PROCEDURE `dsp_movimientos_del_dia`(pIdEstudio int)
BEGIN
    DECLARE pCantJudiciales, pCantExtrajudiciales int;
    DECLARE pJuzgadosCasos JSON;
    DROP TEMPORARY TABLE IF EXISTS tmp_casos_estudio;
    CREATE TEMPORARY TABLE tmp_casos_estudio ENGINE=MEMORY
        SELECT DISTINCT IdCaso FROM UsuariosCaso WHERE IdEstudio = pIdEstudio;

	-- SELECT 	JSON_OBJECTAGG(a.Juzgado, a.Casos)
    SELECT  JSON_OBJECTAGG(a.IdJuzgado, JSON_OBJECT("Juzgado", a.Juzgado, "Cantidad", a.Casos, "Modo", a.ModoGestion))
    INTO	pJuzgadosCasos
    FROM	(
			SELECT  j.IdJuzgado, j.Juzgado, j.ModoGestion, COUNT(*) Casos
			FROM    tmp_casos_estudio t
			INNER JOIN Casos c ON t.IdCaso = c.IdCaso
			INNER JOIN Juzgados j ON c.IdJuzgado = j.IdJuzgado
			GROUP BY    j.IdJuzgado
	) a;

    SELECT  SUM(IF(j.ModoGestion = 'J', 1, 0)) CantJudiciales,
            SUM(IF(j.ModoGestion = 'E', 1, 0)) CantExtrajudiciales
    INTO    pCantJudiciales, pCantExtrajudiciales
    FROM    tmp_casos_estudio t
    INNER JOIN Casos c ON t.IdCaso = c.IdCaso
    INNER JOIN Juzgados j ON c.IdJuzgado = j.IdJuzgado;


    SELECT  m.*, j.Juzgado, j.ModoGestion, c.*, u.Nombres, u.Apellidos, u.IdUsuario , n.Nominacion, o.Objetivo,
            NULL CantJudiciales, NULL CantExtrajudiciales, NULL JuzgadosCasos
    FROM tmp_casos_estudio t
    INNER JOIN MovimientosCaso m USING(IdCaso)
    INNER JOIN Casos c ON m.IdCaso = c.IdCaso
    INNER JOIN Juzgados j ON c.IdJuzgado = j.IdJuzgado
    LEFT JOIN UsuariosCaso uc ON m.IdResponsable = uc.IdUsuarioCaso
    LEFT JOIN Usuarios u ON uc.IdUsuario = u.IdUsuario
    LEFT JOIN Nominaciones n ON c.IdNominacion = n.IdNominacion
    LEFT JOIN MovimientosObjetivo mo ON mo.IdMovimientoCaso = m.IdMovimientoCaso
	LEFT JOIN Objetivos o ON mo.IdObjetivo = o.IdObjetivo
    WHERE DATE(m.FechaEsperada) = CURDATE()
    -- /*
    UNION
    SELECT  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
			NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, 
            COALESCE(pCantJudiciales, 0) CantJudiciales,
            COALESCE(pCantExtrajudiciales, 0) CantExtrajudiciales,
            COALESCE(pJuzgadosCasos, JSON_OBJECT()) JuzgadosCasos
    -- */
	;


    DROP TEMPORARY TABLE IF EXISTS tmp_casos_estudio;
END $$
DELIMITER ;
