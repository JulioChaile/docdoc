DROP PROCEDURE IF EXISTS `dsp_buscar_avanzado_consultas`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_avanzado_consultas`(pCadena varchar(50), pFechaInicio date, 
		pFechaFin date, pEstado char(1), pIdDifusion smallint)
BEGIN
	/*
    Permite buscar consultas filtrándolas por fecha de alta entre pFechaInicio y pFechaFin, 
    por apynom y/o teléfono en pCadena, por campaña de difusión, e indicando si se incluyen 
    o no las dadas de baja en pEstado = [S|N]. Ordena por FechaAlta.
    */
	DECLARE aux date;
    
    IF pFechaInicio > pFechaFin THEN
		SET aux = pFechaInicio;
        SET pFechaInicio = pFechaFin;
        SET	pFechaFin = aux;
	END IF;
    IF pEstado IS NULL OR pEstado = '' OR pEstado NOT IN ('A','D','B','T') THEN
		SET pEstado = 'T';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    SET pIdDifusion = COALESCE(pIdDifusion, 0);
   
	SELECT		c.IdConsulta, c.IdDifusion, c.Apynom Persona, c.Telefono, c.Texto,
				DATE_FORMAT(c.FechaAlta,'%d/%m/%Y %H:%i') FechaAlta, c.Estado,
                d.Difusion, e.Estudio, 
				IF (MAX(de.IdDerivacionConsulta) IS NOT NULL, 
					(SELECT Estado FROM DerivacionesConsultas WHERE IdDerivacionConsulta = MAX(de.IdDerivacionConsulta)), null) EstadoDerivacion, 
				MAX(de.IdDerivacionConsulta) IdDerivacionConsulta
	FROM		Consultas c
    LEFT JOIN	Difusiones d USING (IdDifusion)
    LEFT JOIN	DerivacionesConsultas de USING (IdConsulta)
    LEFT JOIN	Estudios e USING (IdEstudio)
    RIGHT JOIN	(
					SELECT MIN(cc.IdConsulta) IdConsulta, cc.Apynom, cc.Telefono, cc.Texto 
                    FROM Consultas cc 
                    GROUP BY cc.Apynom, cc.Telefono, cc.Texto
				) t USING (IdConsulta)
    WHERE		(pEstado = 'T' OR c.Estado = pEstado) AND
				(
				
					(pFechaInicio IS NOT NULL AND pFechaFin IS NOT NULL AND c.FechaAlta BETWEEN pFechaInicio AND CONCAT(pFechaFin,' 23:59:59')) OR
                    (pFechaInicio IS NOT NULL AND pFechaFin IS NULL AND c.FechaAlta >= pFechaInicio) OR
                    (pFechaInicio IS NULL AND pFechaFin IS NOT NULL AND c.FechaAlta <= CONCAT(pFechaFin,' 23:59:59')) OR
                    (pFechaInicio IS NULL AND pFechaFin IS NULL)
				) AND
                (c.IdDifusion = pIdDifusion OR pIdDifusion = 0) AND
                c.Texto LIKE CONCAT('%',pCadena,'%')
	GROUP BY	IdConsulta, Estudio
	ORDER BY	c.FechaAlta DESC;
END $$
DELIMITER ;
