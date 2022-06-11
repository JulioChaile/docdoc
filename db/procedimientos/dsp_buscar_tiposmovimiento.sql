DROP PROCEDURE IF EXISTS `dsp_buscar_tiposmovimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_tiposmovimiento`(pIdEstudio int, pCadena varchar(50), pCategoria char(1))
BEGIN
	/*
    Permite buscar tipos de movimiento filtrándolos por una cadena de búsqueda y
    por categoría en pCategoria = P: Procesales - O: Gestión de oficina - T: Todos.
    Ordena por TipoMovimiento.
    */
    IF pCategoria IS NULL OR pCategoria = '' OR pCategoria NOT IN ('P','O') THEN
		SET pCategoria = 'T';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		*
    FROM		TiposMovimiento
    WHERE		IdEstudio = pIdEstudio AND 
				(Categoria = pCategoria OR pCategoria = 'T') AND
				TipoMovimiento LIKE CONCAT('%',pCadena,'%')
	ORDER BY 	TipoMovimiento ASC;
END $$
DELIMITER ;
