DROP PROCEDURE IF EXISTS `dsp_buscar_padron`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_padron`(pTipo char(1), pCadena varchar(50))
BEGIN
	/*
    Permite buscar personas dentro del padron electoral cargado en la base de datos, filtrándolas por tipo de 
    búsqueda en pTipo = D: Documento - P: Persona - T: Todos
    */
    

	IF EXISTS (SELECT		1
				FROM		Padron2019
				WHERE		(
								(pTipo = 'D' AND DNI LIKE CONCAT('%', pCadena, '%')) OR
								(pTipo = 'P' AND CONCAT(APELLIDO, ', ', Nombre) LIKE CONCAT('%', pCadena, '%')) OR
								(pTipo = 'T' AND (DNI LIKE CONCAT('%', pCadena, '%') OR CONCAT(APELLIDO, ', ', Nombre) LIKE CONCAT('%', pCadena, '%') OR DOMICILIO LIKE CONCAT('%', pCadena, '%')))
							) LIMIT 1) THEN
		SELECT 		CONCAT(p.APELLIDO, ', ', p.Nombre) PERSONA, p.DOMICILIO, p.Localidad LOCALIDAD, p.DNI
        FROM		Padron2019 p
		WHERE		(
						(pTipo = 'D' AND DNI LIKE CONCAT('%', pCadena, '%')) OR
						(pTipo = 'P' AND CONCAT(APELLIDO, ', ', Nombre) LIKE CONCAT('%', pCadena, '%')) OR
						(pTipo = 'T' AND (DNI LIKE CONCAT('%', pCadena, '%') OR CONCAT(APELLIDO, ', ', Nombre) LIKE CONCAT('%', pCadena, '%') OR DOMICILIO LIKE CONCAT('%', pCadena, '%')))
					)
		ORDER BY 	PERSONA;
	ELSE
        SELECT		*
		FROM		Padron
		WHERE		(
						(pTipo = 'D' AND DNI LIKE CONCAT('%', pCadena, '%')) OR
						(pTipo = 'P' AND PERSONA LIKE CONCAT('%', pCadena, '%')) OR
						(pTipo = 'T' AND (DNI LIKE CONCAT('%', pCadena, '%') OR PERSONA LIKE CONCAT('%', pCadena, '%') OR DOMICILIO LIKE CONCAT('%', pCadena, '%')))
					)
		ORDER BY 	PERSONA;
    ELSE
            
END $$
DELIMITER ;
