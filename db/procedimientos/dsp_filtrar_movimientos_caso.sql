DROP PROCEDURE IF EXISTS `dsp_filtrar_movimientos_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_filtrar_movimientos_caso`(pIdEstudio int, pBusqueda json, 
		pFechaInicio date, pFechaFin date, pCantidad int, pOffset int)
BEGIN
	/*
    Permite buscar movimeintos de caso filtrándolos por multiples cadenas de busqueda y por fecha de inicio y fin.
    */
    DECLARE aux date;
    DECLARE pIndice int;
    DECLARE pIdCaso bigint;
    
    SET @@group_concat_max_len = 1024 * 1024;
    
    DROP TABLE IF EXISTS tmp_busqueda;
    CREATE TEMPORARY TABLE tmp_busqueda (
		cadena varchar(255)
    ) ENGINE = MEMORY;
    
    SET pIndice = 0;
    WHILE pIndice < f_largo(pBusqueda) DO
		INSERT INTO tmp_busqueda SELECT JSON_UNQUOTE(JSON_EXTRACT(pBusqueda, CONCAT('$[', pIndice, ']')));
        SET pIndice = pIndice + 1;
    END WHILE;
    
    IF pFechaInicio > pFechaFin THEN
		SET aux = pFechaInicio;
        SET	pFechaInicio = pFechaFin;
        SET	pFechaFin = aux;
	END IF;
    
    SELECT		mc.IdMovimientoCaso, mc.IdCaso, mc.IdUsuario, mc.IdTipoMov, mc.Detalle, mc.FechaAlta, mc.FechaEsperada,
				mc.FechaRealizado, mc.Cuaderno, mc.Escrito, mc.Color, CONCAT(res.Apellidos,', ',res.Nombres) Responsable, tm.TipoMovimiento,
                j.IdJuzgado IdJuzgado, n.IdNominacion IdNominacion, j.Juzgado Juzgado, n.Nominacion Nominacion, c.Caratula Caso,
                c.NroExpediente Expediente, IF(mc.FechaEsperada < NOW(), 'S','N') EsperaVencida
	FROM		MovimientosCaso mc
    INNER JOIN	Casos c USING (IdCaso)
    INNER JOIN	Usuarios res USING (IdUsuario)
    INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
    INNER JOIN	Nominaciones n USING (IdNominacion)
    INNER JOIN	Juzgados j USING (IdJuzgado)
	LEFT JOIN	UsuariosCaso uc USING (IdCaso)
    CROSS JOIN	tmp_busqueda b
    WHERE		uc.IdEstudio = pIdEstudio AND
				(
					(pFechaInicio IS NOT NULL AND pFechaFin IS NOT NULL AND mc.FechaAlta BETWEEN pFechaInicio AND CONCAT(pFechaFin,' 23:59:59')) OR
					(pFechaInicio IS NOT NULL AND pFechaFin IS NOT NULL AND mc.FechaEsperada BETWEEN pFechaInicio AND CONCAT(pFechaFin,' 23:59:59')) 
                    -- OR
--                     (pFechaInicio IS NOT NULL AND pFechaFin IS NOT NULL AND mc.FechaRealizado IS NOT NULL 
	-- 					AND mc.FechaRealizado BETWEEN pFechaInicio AND CONCAT(pFechaFin,' 23:59:59'))
				) AND
                (
					res.Nombres LIKE CONCAT('%',b.cadena,'%') OR
					res.Apellidos LIKE CONCAT('%',b.cadena,'%') OR
                    mc.Detalle LIKE CONCAT('%', b.cadena,'%') OR
                    j.Juzgado LIKE CONCAT('%', b.cadena,'%') OR
                    n.Nominacion LIKE CONCAT('%', b.cadena,'%') OR
                    tm.TipoMovimiento LIKE CONCAT('%', b.cadena,'%') OR
                    c.Caratula LIKE CONCAT('%', b.cadena,'%')
                )
	GROUP BY	c.IdCaso
	HAVING		COUNT(c.IdCaso) >= JSON_LENGTH(pBusqueda)
    ORDER BY	mc.FechaEsperada DESC
	LIMIT		pOffset, pCantidad;
    
    DROP TABLE IF EXISTS tmp_busqueda;
    
END $$
DELIMITER ;
