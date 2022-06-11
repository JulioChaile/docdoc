DROP PROCEDURE IF EXISTS `dsp_buscar_avanzado_personas`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_avanzado_personas`(pIdEstudio int, pCadena varchar(50), pTipo char(1))
BEGIN
	/*
    Permite buscar personas filtrándolas por estudio, por tipo de búsqueda en pTipo = T: Todo - N: Nombre - D: Documento - C: Cuit.
    Ordena por Apellidos, Nombres.
    */
    SET pCadena = COALESCE(pCadena, '');
    IF pTipo IS NULL OR pTipo = '' OR pTipo NOT IN ('T', 'N', 'D', 'C', 'J') THEN
		SET pTipo = 'T';
	END IF;
    
    SELECT		p.*
    FROM		Personas p
    WHERE		p.IdEstudio = pIdEstudio AND
				(pTipo = 'T' AND (	CONCAT(p.Nombres, ' ', p.Apellidos) LIKE CONCAT('%', pCadena, '%') OR
									p.Apellidos LIKE CONCAT('%', pCadena, '%') OR
									p.Documento LIKE CONCAT('%', pCadena, '%') OR
									p.Cuit LIKE CONCAT('%', pCadena, '%')
				) OR                    
				(pTipo = 'N' AND CONCAT(p.Nombres, ' ', p.Apellidos) LIKE CONCAT('%', pCadena, '%')) OR
				(pTipo = 'J' AND CONCAT(p.Nombres, ' ', p.Apellidos) LIKE CONCAT('%', pCadena, '%') AND p.Tipo = 'J') OR
				(pTipo = 'D' AND p.Documento = pCadena) OR
				(pTipo = 'C' AND p.Cuit = pCadena))
	ORDER BY	p.Apellidos, p.Nombres;
                
END $$
DELIMITER ;
