DROP PROCEDURE IF EXISTS `dsp_dame_consulta`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_consulta`(pIdConsulta int)
BEGIN
	/*
    Permite instanciar una consulta desde al base de datos
    */
    DECLARE pDerivaciones json;
    
    SET pDerivaciones = (SELECT CONCAT('[', COALESCE(GROUP_CONCAT(JSON_OBJECT(
														'IdDerivacionConsulta', dc.IdDerivacionConsulta,
                                                        'FechaDerivacion', dc.FechaDerivacion,
														'Estudio', e.Estudio,
                                                        'Estado', dc.Estado
										) ORDER BY FechaDerivacion DESC), '') , ']')
						FROM		DerivacionesConsultas dc
                        INNER JOIN	Estudios e USING (IdEstudio)
                        WHERE		IdConsulta = pIdConsulta);
    
    SELECT		c.IdConsulta, c.IdDifusion, c.Apynom, c.Telefono, c.Texto,
				DATE_FORMAT(c.FechaAlta,'%d/%m/%Y %H:%i') FechaAlta, c.Estado,
                d.Difusion, pDerivaciones Derivaciones
    FROM		Consultas c
    LEFT JOIN	Difusiones d USING (IdDifusion)
    WHERE		c.IdConsulta = pIdConsulta;
END $$
DELIMITER ;
