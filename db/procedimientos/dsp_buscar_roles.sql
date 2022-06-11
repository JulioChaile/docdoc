DROP PROCEDURE IF EXISTS `dsp_buscar_roles`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_roles`(pCadena varchar(30), pIncluyeBajas char(1))
BEGIN
	/*
    Permite buscar los roles dada una cadena de búsqueda y la opción si incluye o no 
    los dados de baja [S|N] respectivamente. Para listar todos, cadena vacía.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN ('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF; 
    SET pCadena = COALESCE(pCadena, '');
    
	SELECT		*
    FROM		Roles
    WHERE		Rol LIKE CONCAT('%', pCadena, '%')
				AND ((Estado = 'A' AND pIncluyeBajas = 'N') OR pIncluyeBajas = 'S')
    ORDER BY	Rol;
END $$
DELIMITER ;
