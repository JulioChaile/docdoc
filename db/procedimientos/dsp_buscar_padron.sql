DROP PROCEDURE IF EXISTS `dsp_buscar_padron`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_padron`(pTipo char(1), pCadena varchar(50))
BEGIN
	/*
    Permite buscar personas dentro del padron electoral cargado en la base de datos, filtrándolas por tipo de 
    búsqueda en pTipo = D: Documento - P: Persona - T: Todos
    */
    SELECT		*
    FROM		Padron
	WHERE		(
					(pTipo = 'D' AND DNI LIKE CONCAT(pCadena, '%')) OR
					(pTipo = 'P' AND PERSONA LIKE CONCAT(pCadena, '%')) OR
					(pTipo = 'T' AND (DNI LIKE CONCAT(pCadena, '%') OR PERSONA LIKE CONCAT(pCadena, '%') OR DOMICILIO LIKE CONCAT(pCadena, '%')))
				)
	ORDER BY 	PERSONA;
            
END $$
DELIMITER ;
