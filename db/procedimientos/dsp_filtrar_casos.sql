DROP PROCEDURE IF EXISTS `dsp_filtrar_casos`;
DELIMITER $$
CREATE PROCEDURE `dsp_filtrar_casos`(pIdEstudio int, pBusqueda json, 
		pFechaInicio date, pFechaFin date, pCantidad int, pOffset int)
PROC: BEGIN
	/*
    Permite buscar casos filtrándolos por multiples cadenas de busqueda y por fecha de inicio y fin.
    */
    DECLARE aux date;
    DECLARE pIndice int;
    DECLARE pIdCaso bigint;
    
    SET @@group_concat_max_len = 1024 * 1024;
    DROP TABLE IF EXISTS tmp_busqueda;
    CREATE TEMPORARY TABLE tmp_busqueda (
		cadena varchar(255)
    ) ENGINE=MEMORY;
    
    SET pIndice = 0;
    
    WHILE pIndice < f_largo(pBusqueda) DO
		INSERT INTO tmp_busqueda SELECT JSON_UNQUOTE((JSON_EXTRACT(pBusqueda, CONCAT('$[', pIndice, ']'))));
        SET pIndice = pIndice + 1;
    END WHILE;
    
    IF pFechaInicio > pFechaFin THEN
		SET aux = pFechaInicio;
        SET	pFechaInicio = pFechaFin;
        SET	pFechaFin = aux;
	END IF;
    
    SET @Fila = 0;
    DROP TEMPORARY TABLE IF EXISTS tmp_casos;
    CREATE TEMPORARY TABLE tmp_casos ENGINE = MEMORY
		SELECT		(@Fila := @Fila + 1) Fila, c.IdCaso, uc.IdEstudio, uc.IdUsuario, c.IdNominacion, c.Caratula, 
					c.NroExpediente, c.Carpeta, c.FechaAlta, 
					c.IdEstadoCaso, c.Observaciones, CONCAT(u.Apellidos,', ', u.Nombres) Abogado, e.Estudio,
					n.Nominacion, j.Juzgado, ju.Jurisdiccion, ec.EstadoCaso,
					c.FechaUltVisita, j.IdJuzgado, ju.IdJurisdiccion, c.IdTipoCaso, tc.TipoCaso, c.IdOrigen,
					o.Origen, 
                    IF(EXISTS(SELECT IdCaso FROM PersonasCaso WHERE IdCaso = c.IdCaso),
						(SELECT 	CONCAT(p.Apellidos,', ', p.Nombres) 
						FROM		PersonasCaso ac
                        INNER JOIN 	Personas p USING (IdPersona)
                        WHERE		ac.IdCaso = c.IdCaso AND ac.EsPrincipal = 'S'), NULL) ActorPrincipal
		FROM		Casos c
        LEFT JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso AND EsCreador = 'S'
		LEFT JOIN	Usuarios u ON u.IdUsuario = uc.IdUsuario 
        LEFT JOIN	TiposCaso tc ON tc.IdTipoCaso = c.IdTipoCaso
		LEFT JOIN	Estudios e ON e.IdEstudio = uc.IdEstudio
		LEFT JOIN	EstadosCaso ec ON ec.IdEstadoCaso = c.IdEstadoCaso
		LEFT JOIN	Nominaciones n ON n.IdNominacion = c.IdNominacion
		LEFT JOIN	Juzgados j ON j.IdJuzgado = n.IdJuzgado
		LEFT JOIN	Jurisdicciones ju ON ju.IdJurisdiccion = j.IdJurisdiccion
		LEFT JOIN	Origenes o ON o.IdOrigen = c.IdOrigen
		CROSS JOIN	tmp_busqueda b
		WHERE		(uc.IdEstudio = pIdEstudio) AND 
					(
						(pFechaInicio IS NOT NULL AND pFechaFin IS NOT NULL AND c.FechaAlta BETWEEN pFechaInicio AND CONCAT(pFechaFin,' 23:59:59')) OR
						(pFechaInicio IS NOT NULL AND pFechaFin IS NULL AND c.FechaAlta >= pFechaInicio) OR
						(pFechaInicio IS NULL AND pFechaFin IS NOT NULL AND c.FechaALta <= CONCAT(pFechaFin,' 23:59:59')) OR
						(pFechaInicio IS NULL AND pFechaFin IS NULL)
					) AND
					(u.Nombres LIKE CONCAT('%',b.cadena,'%') OR
					u.Apellidos LIKE CONCAT('%',b.cadena,'%') OR
					u.Usuario LIKE CONCAT('%',b.cadena,'%') OR
					u.Email LIKE CONCAT('%',b.cadena,'%') OR
					ec.EstadoCaso LIKE CONCAT('%',b.cadena,'%') OR
					n.Nominacion LIKE CONCAT('%',b.cadena,'%') OR
					j.Juzgado LIKE CONCAT('%',b.cadena,'%') OR
					ju.Jurisdiccion LIKE CONCAT('%',b.cadena,'%') OR
					o.Origen LIKE CONCAT('%',b.cadena,'%') OR
					c.Caratula LIKE CONCAT('%',b.cadena,'%') OR
					c.Carpeta LIKE CONCAT('%',b.cadena,'%') OR
					c.NroExpediente LIKE CONCAT('%',b.cadena,'%') OR
					tc.TipoCaso LIKE CONCAT('%',b.cadena,'%') OR
					EXISTS (SELECT 		IdPersona 
							FROM 		PersonasCaso acs
							INNER JOIN 	Personas per USING(IdPersona) 
							WHERE 		acs.IdCaso = c.IdCaso AND
										(per.Nombres LIKE CONCAT('%',b.cadena,'%') OR
										per.Apellidos LIKE CONCAT('%',b.cadena,'%') OR
										per.Documento LIKE CONCAT('%',b.cadena,'%') OR
										per.Email LIKE CONCAT('%',b.cadena,'%') OR
										per.Cuit LIKE CONCAT('%',b.cadena,'%'))
							)
					)
		GROUP BY	c.IdCaso
		HAVING		COUNT(c.IdCaso) >= JSON_LENGTH(pBusqueda)
        ORDER BY	c.IdCaso DESC
		LIMIT		pOffset, pCantidad;
    
    DROP TEMPORARY TABLE IF EXISTS tmp_mov_casos;
    CREATE TEMPORARY TABLE tmp_mov_casos (
		IdMovimientoCaso bigint,
        IdCaso bigint,
        IdUsuario int,
        Responsable varchar(200),
        IdTipoMov int,
        TipoMovimiento varchar(50),
        Detalle varchar(300),
        FechaAlta datetime,
        FechaEsperada datetime,
        FechaRealizado datetime,
        Cuaderno varchar(5)
    ) ENGINE = MEMORY;
    
    SET pIndice = 1;
    WHILE (pIndice <= @Fila) DO
		SET pIdCaso = (SELECT IdCaso FROM tmp_casos WHERE Fila = pIndice);

        INSERT INTO tmp_mov_casos
        SELECT 		mc.IdMovimientoCaso, pIdCaso, mc.IdUsuario, CONCAT(r.Apellidos,', ',r.Nombres) Responsable,
					mc.IdTipoMov, tm.TipoMovimiento, mc.Detalle, mc.FechaAlta, mc.FechaEsperada, mc.FechaRealizado, 
                    mc.Cuaderno
		FROM		MovimientosCaso mc
		INNER JOIN	Usuarios r USING (IdUsuario)
        INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
		WHERE		mc.IdCaso = pIdCaso
		ORDER BY	FechaAlta DESC
        LIMIT		3;
        
        SET pIndice = pIndice + 1;
    END WHILE;
    
	SELECT		c.*, (SELECT CONCAT('[',COALESCE(GROUP_CONCAT(JSON_OBJECT(
													'IdMovimientoCaso', IdMovimientoCaso,
													'Detalle',Detalle,
													'IdResponsable', IdUsuario,
													'Responsable', Responsable,
													'FechaAlta', FechaAlta,
													'FechaEsperada', FechaEsperada,
													'FechaRealizado', FechaRealizado,
													'Cuaderno', Cuaderno
														)
										),''),']')
						FROM	tmp_mov_casos tmp
						WHERE	tmp.IdCaso = c.IdCaso) UltimosMovimientos
	FROM 		tmp_casos c; 
    
    DROP TEMPORARY TABLE IF EXISTS tmp_busqueda;
    DROP TEMPORARY TABLE IF EXISTS tmp_casos;
    DROP TEMPORARY TABLE IF EXISTS tmp_mov_casos;
END $$
DELIMITER ;
