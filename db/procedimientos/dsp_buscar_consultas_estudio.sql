DROP PROCEDURE IF EXISTS `dsp_buscar_consultas_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_consultas_estudio`(pIdEstudio int, pEstado char(1), pCadena varchar(50))
BEGIN
	/*
    Permite buscar consultas derivadas a un estudio, filtrándolas por estado: P: Pendiente - A: Aceptada - R: Rechazada - T: Todas. 
    Ordena por FechaDerivacion.
    */
    SELECT		c.*, dc.IdDerivacionConsulta, dc.FechaDerivacion, dc.Estado EstadoDerivacion
    FROM		Consultas c
    INNER JOIN	DerivacionesConsultas dc USING (IdConsulta)
    WHERE		dc.IdEstudio = pIdEstudio AND (pEstado = 'T' OR dc.Estado = pEstado) AND
				c.Apynom LIKE CONCAT('%', pCadena, '%')
	ORDER BY	dc.FechaDerivacion desc;
END $$
DELIMITER ;
